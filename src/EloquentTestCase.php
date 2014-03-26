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
 * Abstract test case class for testing models using a test database.
 */
abstract class EloquentTestCase extends PHPunit_Framework_TestCase
{
	protected $container;
	protected $capsule;
	protected $eventDispatcher;
	protected $cacheManager;
	protected $enableEvents = false;
	protected $enableCache = false;

	/**
	 * Set up class aliases if necessary, as these are quite often used in
	 * migrations, models and seeders.
	 */
	public static function setUpBeforeClass()
	{
		if (!class_exists('Schema')) {
			class_alias('Illuminate\Support\Facades\Schema', 'Schema');
		}
		if (!class_exists('Eloquent')) {
			class_alias('Illuminate\Database\Eloquent\Model', 'Eloquent');
		}
		if (!class_exists('Seeder')) {
			class_alias('Illuminate\Database\Seeder', 'Seeder');
		}
	}

	public function setUp()
	{
		$this->container = new Container;
		Facade::setFacadeApplication($this->container);
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

		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();
		$this->runMigrations('up');
		$this->runSeeds();
	}

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

	protected function runMigrations($direction)
	{
		foreach($this->getMigrations() as $class) {
			$migration = $this->container->make($class);
			$migration->$direction();
			$migration = null;
		}
	}

	public function runSeeds()
	{
		foreach ($this->getSeeders() as $class) {
			$seeder = $this->container->make($class);
			// https://github.com/laravel/framework/pull/3995
			// $seeder->setContainer($this->container);
			$seeder->run();
		}
	}

	protected function getDatabaseConfig()
	{
		return [
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		];
	}

	protected function getMigrations()
	{
		return [];
	}

	protected function getSeeders()
	{
		return [];
	}
}
