# CaptchaServiceProvider

This is the [Gregwar/Captcha](https://github.com/Gregwar/Captcha/) service provider for [Silex](http://silex.sensiolabs.org)

# Requirements

- PHP >= 5.3.3

# Usage

- Register [`SessionServiceProvider`](http://silex.sensiolabs.org/doc/providers/session.html)
- Register [`UrlGeneratorServiceProvider`](http://silex.sensiolabs.org/doc/providers/url_generator.html) (Optional)
- Register `CaptchaServiceProvider`
- Mount `CaptchaServiceProvider`

For example:

    use Kilte\Silex\Captcha\CaptchaServiceProvider;
    $app = new Silex\Application();
    $captcha = new CaptchaServiceProvider();
    $app->register(new SessionServiceProvider)
        ->register(new UrlGeneratorServiceProvider)
        ->register($captcha)
        ->mount('/', $captcha);
    $app->run();

## Options

|  Key             |    Default      | Type                                            |  Description
|------------------|-----------------|-------------------------------------------------|--------
| session_key      | gw_captcha      | string                                          | Name of the session key
| route_name       | gregwar.captcha | string                                          | Name of the route
| phrase_builder   | null            | null or Gregwar\Captcha\CaptchaBuilderInterface | Phrase builder (will be used if phrase is null)
| phrase           | null            | string or null                                  | Overrides the phrase
| width            | 150             | int                                             | Image width in the pixels
| height           | 40              | int                                             | Image height in the pixels
| font             | null            | string or null                                  | Path to the font
| fingerprint      | null            | boolean                                         | *I don't know that it does, see sources*
| quality          | 90              | int                                             | Image quality
| distortion       | true            | boolean                                         | Enable or disable the distortion
| background       | null            | null or array(r, g, b)                          | Force background color (this will disable many effects and is not recommended)
| interpolation    | true            | boolean                                         | Enable or disable the interpolation, disabling it will be quicker but the images will look uglier



- `captcha.builder` - Instance of `Gregwar\Captcha\CaptchaBuilder`.
- `captcha.test` - Performs check user input. (Instance of the `\Closure`)
- `captcha.image_url` -  Returns absolute URL to the image. (Instance of the `\Closure`)

# LICENSE

The MIT LICENSE (MIT)