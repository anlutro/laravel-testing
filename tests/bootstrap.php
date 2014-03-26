<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
foreach (glob(__DIR__ . '/stubs/*.php') as $file) {
	require_once $file;
}
date_default_timezone_set('UTC');
Carbon\Carbon::setTestNow(Carbon\Carbon::now());
