<?php

/**
 * Part of the CaptchaServiceProvider
 *
 * For the full copyright and license information,
 * view the LICENSE file that was distributed with this source code.
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
            ->register($provider, array('captcha.background' => array(255, 255, 255)))
            ->mount('/', $provider);

        return $app;
    }

    public function testRegister()
    {
        $app = $this->createApplication();

        $this->assertInstanceOf('Gregwar\\Captcha\\CaptchaBuilder', $app['captcha.builder']);

        $this->assertStringMatchesFormat('http://%s/captcha', $app['captcha.image_url']());

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
