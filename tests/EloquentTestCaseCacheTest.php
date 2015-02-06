<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-testing
 */

use Illuminate\Foundation\Application;

/**
 * Ensure that the cache can be used in the Eloquent test case.
 */
class EloquentTestCaseCacheTest extends \anlutro\LaravelTesting\EloquentTestCase
{
	protected $enableCache = true;

	public function setUp()
	{
		// tearDown() is called even when the test is skipped, so call
		// parent::setUp() to prevent errors
		parent::setUp();
		if (version_compare(Application::VERSION, '5.0', '>=')) {
			$this->markTestSkipped('Caching not possible in 5.x');
		}
	}

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
