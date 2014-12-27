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
	 * Whether to enable events for the test.
	 *
	 * @var boolean
	 */
	protected $enableEvents = false;

	/**
	 * Set up the test case. This method is called before every test method.
	 *
	 * If you override this method, remember to call parent::setUp();
	 */
	public function setUp()
	{
		// create the IOC container and bind it to the facades.
		$this->container = new Container;
		$this->setUpContainer($this->container);
		Facade::setFacadeApplication($this->container);

		// create the capsule and bind it to the IOC container.
		$this->capsule = new DatabaseCapsule($this->container);
		$this->setUpCapsule($this->capsule);
		$this->container['db'] = $this->capsule;
		$this->capsule->addConnection($this->getDatabaseConfig());

		if ($this->enableEvents) {
			$this->eventDispatcher = new Dispatcher($this->container);
			$this->setUpEventDispatcher($this->eventDispatcher);
			$this->capsule->setEventDispatcher($this->eventDispatcher);
		}

		// make the capsule available globally and enable eloquent
		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();

		// run migrations ans seeds
		$this->runMigrations('up');
		$this->runSeeds();
	}

	/**
	 * Set up the IoC container.
	 *
	 * @param \Illuminate\Container\Container $container
	 */
	protected function setUpContainer($container) {}

	/**
	 * Set up the database capsule.
	 *
	 * @param \Illuminate\Database\Capsule\Manager $capsule
	 */
	protected function setUpCapsule($capsule) {}

	/**
	 * Set up the event dispatcher.
	 *
	 * @param \Illuminate\Events\Dispatcher $eventDispatcher
	 */
	protected function setUpEventDispatcher($eventDispatcher) {}

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
		unset($this->eventDispatcher);
		unset($this->capsule);
		unset($this->container);
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
			unset($migration);
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
			$seeder->setContainer($this->container);
			$seeder->run();
			unset($seeder);
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
