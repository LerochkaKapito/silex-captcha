<?php

/**
 * Part of the CaptchaServiceProvider
 *
 * @author  Kilte <nwotnbm@gmail.com>
 * @package CaptchaServiceProvider
 */

namespace Kilte\Silex\Captcha;

use Gregwar\Captcha\CaptchaBuilder;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * CaptchaServiceProvider Class
 *
 * Service provider for Gregwar/Captcha ({@link https://github.com/Gregwar/Captcha})
 *
 * @package Kilte\Silex\Captcha
 */
class CaptchaServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{

    /**
     * @var array List of the default settings
     */
    protected $settings = array(
        'session_key'    => 'gw_captcha',
        'route_name'     => 'gregwar.captcha',
        'phrase_builder' => null,
        'phrase'         => null,
        'width'          => 150,
        'height'         => 40,
        'font'           => null,
        'fingerprint'    => null,
        'quality'        => 90,
        'distortion'     => true,
        'background'     => null,
        'interpolation'  => true,
    );

    /**
     * @{inheritdoc}
     */
    public function register(Application $app)
    {
        // Load defaults
        foreach ($this->settings as $key => $value) {
            $key = 'captcha.' . $key;
            if (!isset($app[$key])) {
                $app[$key] = $value;
            }
        }
        // Instance of builder
        $app['captcha.builder'] = $app->share(
            function (Application $app) {
                return new CaptchaBuilder($app['captcha.phrase'], $app['captcha.phrase_builder']);
            }
        );
        // Checks captcha
        $app['captcha.test'] = $app->protect(
            function ($phrase) use ($app) {
                /** @var $builder CaptchaBuilder */
                $builder = $app['captcha.builder'];
                /** @var $session Session */
                $session = $app['session'];
                $builder->setPhrase($session->get($app['captcha.session_key']));

                return $builder->testPhrase($phrase);
            }
        );
        // Returns absolute URL to the image
        $app['captcha.image_url'] = $app->protect(
            function () use ($app) {
                /** @var $urlGenerator UrlGenerator */
                $urlGenerator = $app['url_generator'];

                return $urlGenerator->generate($app['captcha.route_name'], array(), UrlGenerator::ABSOLUTE_URL);
            }
        );
    }

    /**
     * @{inheritdoc}
     */
    public function boot(Application $app)
    {
    }

    /**
     * @{inheritdoc}
     */
    public function connect(Application $app)
    {
        /** @var $collection ControllerCollection */
        $collection = $app['controllers_factory'];

        // Display image and store phrase to the session
        // Note: SessionServiceProvider should be enabled
        $collection->get(
            '/captcha',
            function () use ($app) {
                /** @var $builder CaptchaBuilder */
                $builder = $app['captcha.builder'];
                /** @var $session Session */
                $session = $app['session'];
                $session->set($app['captcha.session_key'], $builder->getPhrase());

                // Set background, if specified
                if ($app['captcha.background'] !== null) {
                    $builder->setBackgroundColor(
                        $app['captcha.background'][0],
                        $app['captcha.background'][1],
                        $app['captcha.background'][2]
                    );
                }

                $image = $builder
                    ->setDistortion($app['captcha.distortion'])
                    ->setInterpolation($app['captcha.interpolation'])
                    ->build($app['captcha.width'], $app['captcha.height'], $app['captcha.font'], $app['captcha.fingerprint'])
                    ->get($app['captcha.quality']);

                return new Response($image, 200, array('Content-Type' => 'image/jpeg'));
            }
        )->bind($app['captcha.route_name']);

        return $collection;
    }

}
