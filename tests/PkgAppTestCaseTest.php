<?php
namespace anlutro\LaravelTesting\Tests;

use anlutro\LaravelTesting\PkgAppTestCase;
use PHPUnit_Framework_TestCase;
use Mockery as m;

class PkgAppTestCaseTest extends PkgAppTestCase
{
	/** @test */
	public function works()
	{
		$this->call('GET', '/');
		$this->assertResponseOk();
	}

	protected function getVendorPath()
	{
		return dirname(__DIR__) . '/vendor';
	}
}
