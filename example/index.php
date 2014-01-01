<?php

/**
 * Part of the CaptchaServiceProvider
 *
 * @author Kilte <nwotnbm@gmail.com>
 * @package CaptchaServiceProvider
 */

use Kilte\Silex\Captcha\CaptchaServiceProvider;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application(array('debug' => true));

// Captcha settings
$app['captcha.width'] = 140;
$app['captcha.height'] = 90;
//$app['captcha.phrase'] = (string) rand(1000, 9999);
$app['captcha.quality'] = 100;
$app['captcha.distortion'] = true;
//$app['captcha.background'] = array(0, 255, 0);
$app['captcha.interpolation'] = false;

$captcha = new CaptchaServiceProvider();

$app->register(new SessionServiceProvider)
    ->register(new UrlGeneratorServiceProvider)
    ->register($captcha)
    ->mount('/', $captcha);

$app->match(
    '/',
    function (Request $request) use ($app) {
        if ($request->getMethod() === 'POST') {
            if ($app['captcha.test']($request->get('captcha'))) {
                $result = '<p><span style="color: #00ff00;">Ok</span></p>';
            } else {
                $result = '<p><span style="color: #ff0000;">Failed</span></p>';
            }
        } else {
            $result = '';
        }

        return '<form method="post" action="">'
            . '<p><img src="' . $app['captcha.image_url']() . '" alt="Captcha" /></p>'
            . '<p><input type="text" name="captcha" value="" /></p>'
            . $result
            . '<p><input type="submit" /></p>'
            . '</form>';
    }
);

$app->run();