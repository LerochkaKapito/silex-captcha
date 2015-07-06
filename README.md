# CaptchaServiceProvider

This is the [Gregwar/Captcha](https://github.com/Gregwar/Captcha/) service provider for [Silex](http://silex.sensiolabs.org)

[![Build Status](https://travis-ci.org/Kilte/silex-captcha.svg?branch=master)](https://travis-ci.org/Kilte/silex-captcha)


## Requirements

- PHP >= 5.3.3
- [`SessionServiceProvider`](http://silex.sensiolabs.org/doc/providers/session.html)
- [`UrlGeneratorServiceProvider`](http://silex.sensiolabs.org/doc/providers/url_generator.html) (Optional)


## Usage

```php
use Kilte\Silex\Captcha\CaptchaServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

$app = new Silex\Application();
$captcha = new CaptchaServiceProvider();
$app->register(new SessionServiceProvider)
    ->register(new UrlGeneratorServiceProvider)
    ->register($captcha)
    ->mount('/', $captcha);
$app->run();
```

### Options

|  Key             |    Default      | Type                                            |  Description
|------------------|-----------------|-------------------------------------------------|--------
| session_key      | gw_captcha      | string                                          | Name of the session key
| route_name       | gregwar.captcha | string                                          | Name of the route
| phrase_builder   | null            | null or Gregwar\Captcha\CaptchaBuilderInterface | Phrase builder (will be used if phrase is null)
| phrase           | null            | string or null                                  | Overrides the phrase
| width            | 150             | int                                             | Image width in the pixels
| height           | 40              | int                                             | Image height in the pixels
| font             | null            | string or null                                  | Path to the font
| fingerprint      | null            | boolean                                         | *I don't know what it does, see sources*
| quality          | 90              | int                                             | Image quality
| distortion       | true            | boolean                                         | Enable or disable the distortion
| background       | null            | null or array(r, g, b)                          | Force background color (this will disable many effects and is not recommended)
| interpolation    | true            | boolean                                         | Enable or disable the interpolation, disabling it will be quicker but the images will look uglier



- `captcha.builder` - Instance of `Gregwar\Captcha\CaptchaBuilder`.
- `captcha.test` - Performs check user input. (Instance of the `\Closure`)
- `captcha.image_url` -  Returns absolute URL to the image. (Instance of the `\Closure`)


## Tests

```bash
$ composer install
$ vendor/bin/phpunit
```


## Changelog

### 1.0.1 \[31.08.2014\]

- Added unit tests
- PSR-4 autoloading
- Other small changes

### 1.0.0 \[02.01.2014\]

- First release

## Contributing

- Fork it
- Create your feature branch (git checkout -b awesome-feature)
- Make your changes
- Write/update tests, if necessary
- Update README.md, if necessary
- Push your branch to origin (git push origin awesome-feature)
- Send pull request
- ???
- PROFIT\!\!\!

Do not forget merge upstream changes:

    git remote add upstream https://github.com/Kilte/silex-captcha
    git checkout master
    git pull upstream
    git push origin master

Now you can to remove your branch:

    git branch -d awesome-feature
    git push origin :awesome-feature


# LICENSE

The MIT LICENSE (MIT)
