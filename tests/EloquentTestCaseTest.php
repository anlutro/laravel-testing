<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-testing
 */

/**
 * This test ensures that the EloquentTestCase class works by inserting and
 * then retrieving a stub model.
 */
class EloquentTestCaseTest extends \anlutro\LaravelTesting\EloquentTestCase
{
	protected function getMigrations()
	{
		return ['MigrationStub'];
	}

	public function getSeeders()
	{
		return ['SeederStub'];
	}

	public function testInsertAndRetrieve()
	{
		$model = EloquentStub::first();
		$this->assertTrue($model->exists);
		$this->assertEquals('foobar', $model->name);
		$this->assertEquals(1, $model->id);
	}
}
