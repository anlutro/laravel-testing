<?php
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