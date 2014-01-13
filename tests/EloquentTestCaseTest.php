<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-testing
 */

use Mockery as m;

/**
 * This test ensures that the EloquentTestCase class works by inserting and
 * then retrieving a stub model.
 */
class EloquentTestCaseTest extends \c\EloquentTestCase
{
	protected function getMigrations()
	{
		return ['MigrationStub'];
	}

	public function testInsertAndRetrieve()
	{
		$model = EloquentStub::create(['name' => 'foobar']);
		$model = EloquentStub::find($model->getKey());
		$this->assertTrue($model->exists);
		$this->assertEquals('foobar', $model->name);
		$this->assertEquals(1, $model->id);
	}
}

class EloquentStub extends Illuminate\Database\Eloquent\Model
{
	protected $fillable = ['name'];
	public $timestamps = false;
}

class MigrationStub extends Illuminate\Database\Migrations\Migration
{
	public function up()
	{
		Schema::create('eloquent_stubs', function($t) {
			$t->increments('id');
			$t->string('name', 50);
		});
	}

	public function down()
	{
		Schema::drop('eloquent_stubs');
	}
}
