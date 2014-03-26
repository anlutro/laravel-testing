<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-testing
 */

namespace anlutro\LaravelTesting;

use Illuminate\Support\Facades\Facade;

/**
 * Abstract test case class for testing models using a test database.
 */
abstract class EloquentTestCase extends \PHPunit_Framework_TestCase
{
	protected $enableEvents = false;
	protected $enableCache = false;

	public static function setUpBeforeClass()
	{
		if (!class_exists('Schema')) {
			class_alias('Illuminate\Support\Facades\Schema', 'Schema');
		}
		if (!class_exists('Eloquent')) {
			class_alias('Illuminate\Database\Eloquent\Model', 'Eloquent');
		}
	}

	public function setUp()
	{
		$this->capsule = new \Illuminate\Database\Capsule\Manager;
		$this->capsule->addConnection($this->getDatabaseConfig());

		if ($this->enableEvents) {
			if (!isset($container)) $container = new \Illuminate\Container\Container;
			$this->eventDispatcher = new \Illuminate\Events\Dispatcher($container);
			$this->capsule->setEventDispatcher($this->eventDispatcher);
		}

		if ($this->enableCache) {
			if (!isset($container)) $container = new \Illuminate\Container\Container;
			$container['config'] = ['cache.driver' => 'array'];
			$this->cacheManager = new \Illuminate\Cache\CacheManager($container);
			$this->capsule->setCacheManager($this->cacheManager);
		}

		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();
		$this->runMigrations('up');
	}

	public function tearDown()
	{
		$this->runMigrations('down');
		$this->capsule = null;
	}

	protected function runMigrations($direction)
	{
		$this->setUpFacades();

		foreach($this->getMigrations() as $class) {
			$migration = new $class;
			$migration->$direction();
			$migration = null;
		}

		$this->tearDownFacades();
	}

	protected function setUpFacades()
	{
		$this->app = $this->makeFakeApp();
		Facade::setFacadeApplication($this->app);
	}

	protected function makeFakeApp()
	{
		return ['db' => $this->capsule];
	}

	protected function tearDownFacades()
	{
		$this->app = null;
		Facade::setFacadeApplication(null);
		Facade::clearResolvedInstances();
	}

	protected function getDatabaseConfig()
	{
		return [
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		];
	}

	abstract protected function getMigrations();
}
