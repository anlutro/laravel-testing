<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  testing
 */

use Illuminate\Support\Facades\Facade;

/**
 * Abstract test case class for testing models using a test database.
 */
abstract class EloquentTestCase extends PHPunit_Framework_TestCase
{
	public function setUp()
	{
		$this->capsule = new Illuminate\Database\Capsule\Manager;
		$this->capsule->addConnection($this->getDatabaseConfig());
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
