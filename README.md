# Laravel 4 Test classes
Installation: `composer require anlutro/l4-testing`

Pick the latest stable version from packagist or the GitHub tag list.

### L4 TestCase
Set `protected $controller = 'MyController'` on your test class and you get access to some shorthands like `$this->getAction('show', [1])` which will expand into `$this->call('get', URL::action('MyController@show', [1]))`.

`$this->assertRouteHasFilter()` can be used to assert that the previously called route has a certain filter. Note that this only works with filters defined in routes.php, not filters defined in controller constructors.

### EloquentTestCase
Standalone test for testing Eloquent models. Defaults to using an sqlite in-memory database, but you can configure this by overriding `getDatabaseConfig()`.

The class has one abstract method, `getMigrations`, which should return the class names of the migrations to run for the tests. These classes need to be autoloaded somehow.

## Contact
Open an issue on GitHub if you have any problems or suggestions.

## License
The contents of this repository is released under the [MIT license](http://opensource.org/licenses/MIT).