<?php
/**
 * Testing base classes
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-testing
 */

namespace anlutro\LaravelTesting;

/**
 * Test case for using a full-stack laravel application inside a package.
 *
 * Useful if you have a package with routing or want to test the package's
 * service providers.
 *
 * Add laravel/laravel to your package's require-dev, implement the abstract
 * getVendorPath method and you should be good to go.
 */
abstract class PkgAppTestCase extends \anlutro\LaravelTesting\L4TestCase
{
	/**
	 * Create the application.
	 *
	 * @return Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$unitTesting = true;
		$testEnvironment = 'testing';
		$app = new Illuminate\Foundation\Application;
		$env = $app->detectEnvironment(function() { return 'testing'; });
		$app->bindInstallPaths(require $this->getVendorPath() . '/laravel/laravel/bootstrap/paths.php');
		require Illuminate\Foundation\Application::getBootstrapFile();
		return $app;
	}

	protected abstract function getVendorPath();

	/**
	 * Refresh the application instance.
	 *
	 * @return void
	 */
	protected function refreshApplication()
	{
		$this->app = $this->createApplication();

		$this->client = $this->createClient();

		$this->app->setRequestForConsoleEnvironment();

		// allow registration of extra service providers before boot is
		// called, as some providers rely on others being loaded in time.
		foreach ($this->getExtraProviders() as $provider) {
			$this->app->register($provider);
		}

		$this->app->boot();
	}

	protected function getExtraProviders()
	{
		return [];
	}
}
