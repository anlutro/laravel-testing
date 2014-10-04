# Laravel 4 Test classes [![Build Status](https://travis-ci.org/anlutro/laravel-testing.png?branch=master)](https://travis-ci.org/anlutro/laravel-testing) [![Latest Version](http://img.shields.io/github/tag/anlutro/laravel-testing.svg)](https://github.com/anlutro/laravel-testing/releases)
Installation: `composer require anlutro/l4-testing`

Pick the latest stable version from packagist or the GitHub tag list.

### PkgAppTestCase
If you're writing an extensive package with routes, views, event listeners, view composers etc., this one is for you. This test case basically takes the default laravel installation, lets you add your package's service providers on top, and then you have a fully functional test case just like you would in a regular Laravel application - except it's in a package.

    class MyTest extends \anlutro\LaravelTesting\PkgAppTestCase { }

To make this test case work, you need to require the laravel/laravel package in your package's composer.json "require-dev". The test case has one abstract method, `getVendorPath`, which is what it sounds like.

The method `getExtraProviders` should return an array of strings containing the fully qualified class names of any service providers your package requires to function in addition to the default Laravel ones.

### EloquentTestCase
Standalone test for testing Eloquent models.

    class MyTest extends \anlutro\LaravelTesting\EloquentTestCase { }

Defaults to using an sqlite in-memory database, but you can configure this by overriding `getDatabaseConfig()`. The protected methods `getMigrations` and `getSeeders` can be used to have migrations and seeds run before each test. These methods should return an array of strings that are the fully qualified class names of said migrations and seeders. It is your responsibility to make sure that these are either autoloaded via composer or required manually in your tests.

You can set `protected $enableEvents` or `protected $enableCache` to true if you want to use events or the cache in your test.

If you need some additional stuff to be available to facades (the hasher, for example), you can add it manually to `$this->container`. For example, to make `Hash::make` available to the test environment, put the following in your `setUp` method:

    parent::setUp();
    $this->container->bindShared('hash', function() {
        return new \Illuminate\Hashing\BcryptHasher;
    });

Keep in mind that while in a normal Laravel application, all facades are available in the global namespace. This is not the case in an isolated environment. Efforts have been made to make the most common Eloquent-related facades available globally, but some may not be. To solve this, simply look up the real name of the class in app/config/app.php under "aliases" and add a use statement at the top of your model class where you import this class.

### L4TestCase
Basically just an improvement on the default Laravel TestCase.

    class MyTest extends \anlutro\LaravelTesting\L4TestCase { }

Set `protected $controller = 'MyController'` on your test class and you get access to some shorthands like `$this->getAction('show', [1])` which will expand into `$this->call('get', URL::action('MyController@show', [1]))`. This works similarily for `assertRedirectedToAction`.

`$this->assertRouteHasFilter()` can be used to assert that the previously called route has a certain filter. Note that this only works with filters defined in routes.php, not filters defined in controller constructors.

## Contact
Open an issue on GitHub if you have any problems or suggestions.

## License
The contents of this repository is released under the [MIT license](http://opensource.org/licenses/MIT).