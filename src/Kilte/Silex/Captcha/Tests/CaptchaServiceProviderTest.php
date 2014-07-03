<?php

/**
 * Part of the CaptchaServiceProvider
 *
 * @author  Kilte <nwotnbm@gmail.com>
 * @package CaptchaServiceProvider
 */

namespace Kilte\Silex\Captcha\Tests;

use Kilte\Silex\Captcha\CaptchaServiceProvider;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class CaptchaServiceProviderTest
 *
 * @package Kilte\Silex\Captcha\Tests
 */
class CaptchaServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Returns Silex Application instance
     *
     * @return Application
     */
    private function createApplication()
    {
        $app = new Application();
        $provider = new CaptchaServiceProvider();
        $app
            ->register(new SessionServiceProvider(), array('session.test' => true))
            ->register(new UrlGeneratorServiceProvider())
            ->register($provider)
            ->mount('/', $provider);

        return $app;
    }

    public function testRegister()
    {
        $app = $this->createApplication();

        $this->assertInstanceOf('Gregwar\\Captcha\\CaptchaBuilder', $app['captcha.builder']);

        $this->assertStringMatchesFormat(
            'http://%s/captcha',
            $app['url_generator']->generate('gregwar.captcha', array(), UrlGenerator::ABSOLUTE_URL)
        );

        $phrase = $app['captcha.builder']->getPhrase();
        $app['session']->set($app['captcha.session_key'], $phrase);
        $this->assertTrue($app['captcha.test']($phrase));
        $this->assertFalse($app['captcha.test']('wrong_phrase'));
    }

    public function testController()
    {
        $app = $this->createApplication();
        $request = Request::create('/captcha');
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('image/jpeg', $response->headers->get('Content-Type'));

    }

}
