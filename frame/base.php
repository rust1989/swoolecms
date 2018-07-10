<?php
define ('APP_PATH',__DIR__.'/');
define ('CONFIG_PATH',dirname(__DIR__).'/config/');
define('LOG_PATH',dirname(__DIR__).'/log/');
require_once  APP_PATH.'lib/Loader.php';
require_once  APP_PATH.'helper.php';
Jacky\Loader::register();
Jacky\Loader::addNamespace(config('app.namespace'),config('app.path'));