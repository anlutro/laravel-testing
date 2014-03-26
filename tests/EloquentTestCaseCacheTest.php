<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-testing
 */

/**
 * Ensure that the cache can be used in the Eloquent test case.
 */
class EloquentTestCaseCacheTest extends \anlutro\LaravelTesting\EloquentTestCase
{
	protected $enableCache = true;

	protected function getMigrations()
	{
		return ['MigrationStub'];
	}

	public function testInsertAndRetrieve()
	{
		EloquentStub::create(['name' => 'foobar']);
		$model = EloquentStub::query()
			->where('name', '=', 'foobar')
			->remember(1, 'cache_key')
			->first();
		$this->assertTrue($model->exists);
		$this->assertEquals('foobar', $model->name);
		$this->assertEquals(1, $model->id);
		$this->assertTrue($this->cacheManager->has('cache_key'));
	}
}
