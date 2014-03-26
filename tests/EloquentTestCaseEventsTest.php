<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-testing
 */

/**
 * Ensure that the event dispatcher can be used in the Eloquent test case.
 */
class EloquentTestCaseEventsTest extends \anlutro\LaravelTesting\EloquentTestCase
{
	protected $enableEvents = true;

	protected function getMigrations()
	{
		return ['MigrationStub'];
	}

	public function testInsertAndRetrieve()
	{
		$triggered = false;
		EloquentStub::creating(function($model) use(&$triggered) {
			$triggered = true;
		});
		EloquentStub::create(['name' => 'foobar']);
		$this->assertTrue($triggered);
	}
}
