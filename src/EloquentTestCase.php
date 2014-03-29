<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-testing
 */

namespace anlutro\LaravelTesting;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as DatabaseCapsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Facade;
use PHPunit_Framework_TestCase;

/**
 * Set up class aliases if necessary, as these are quite often used in
 * migrations, models and seeders.
 */
if (!class_exists('Schema')) {
	class_alias('Illuminate\Support\Facades\Schema', 'Schema');
}
if (!class_exists('Eloquent')) {
	class_alias('Illuminate\Database\Eloquent\Model', 'Eloquent');
}
if (!class_exists('Seeder')) {
	class_alias('Illuminate\Database\Seeder', 'Seeder');
}
if (!class_exists('DB')) {
	class_alias('Illuminate\Support\Facades\DB', 'DB');
}

/**
 * Abstract test case class for testing models using a test database.
 */
abstract class EloquentTestCase extends PHPunit_Framework_TestCase
{
	/**
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * @var \Illuminate\Database\Capsule\Manager
	 */
	protected $capsule;

	/**
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $eventDispatcher;

	/**
	 * @var \Illuminate\Cache\CacheManager
	 */
	protected $cacheManager;

	/**
	 * Whether to enable events for the test.
	 *
	 * @var boolean
	 */
	protected $enableEvents = false;

	/**
	 * Whether to enable cache for the test.
	 *
	 * @var boolean
	 */
	protected $enableCache = false;

	/**
	 * Set up the test case. This method is called before every test method.
	 *
	 * If you override this method, remember to call parent::setUp();
	 */
	public function setUp()
	{
		// create the IOC container and bind it to the facades.
		$this->container = new Container;
		Facade::setFacadeApplication($this->container);

		// create the capsule and bind it to the IOC container.
		$this->capsule = new DatabaseCapsule($this->container);
		$this->container['db'] = $this->capsule;
		$this->capsule->addConnection($this->getDatabaseConfig());

		if ($this->enableEvents) {
			$this->eventDispatcher = new Dispatcher($this->container);
			$this->capsule->setEventDispatcher($this->eventDispatcher);
		}

		if ($this->enableCache) {
			$this->container['config']['cache.driver'] = 'array';
			$this->cacheManager = new CacheManager($this->container);
			$this->capsule->setCacheManager($this->cacheManager);
		}

		// make the capsule available globally and enable eloquent
		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();

		// run migrations ans seeds
		$this->runMigrations('up');
		$this->runSeeds();
	}

	/**
	 * Tear down the test case. This method is called after each test method.
	 *
	 * If you override this method, remember to call parent::tearDown();
	 */
	public function tearDown()
	{
		$this->runMigrations('down');
		Facade::setFacadeApplication(null);
		Facade::clearResolvedInstances();
		$this->eventDispatcher = null;
		$this->cacheManager = null;
		$this->capsule = null;
		$this->container = null;
	}

	/**
	 * Run the test's migrations.
	 *
	 * @param  string $direction "up" or "down"
	 *
	 * @return void
	 */
	protected function runMigrations($direction)
	{
		foreach($this->getMigrations() as $class) {
			$migration = $this->container->make($class);
			$migration->$direction();
			$migration = null;
		}
	}

	/**
	 * Run the test's seeders.
	 *
	 * @return void
	 */
	protected function runSeeds()
	{
		foreach ($this->getSeeders() as $class) {
			$seeder = $this->container->make($class);
			// https://github.com/laravel/framework/pull/3995
			// $seeder->setContainer($this->container);
			$seeder->run();
		}
	}

	/**
	 * Get the database configuration that should be used for the test. This
	 * corresponds to what would be inside a connection in laravel's default
	 * app/config/database.php. By default an in-memory SQLite database is used,
	 * but you can ovverride this to use whatever you like.
	 *
	 * @return array
	 */
	protected function getDatabaseConfig()
	{
		return [
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		];
	}

	/**
	 * Get a list of class names for migrations to run for each test. Make sure
	 * that these classes are autoloaded.
	 *
	 * @return string[]
	 */
	protected function getMigrations()
	{
		return [];
	}

	/**
	 * Get a list of class names for seeders to run before each test. Make sure
	 * that these classes are autoloaded.
	 *
	 * @return string[]
	 */
	protected function getSeeders()
	{
		return [];
	}
}
