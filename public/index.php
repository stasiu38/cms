<?php

error_reporting(E_ERROR | E_WARNING  | E_NOTICE);

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../acl'));

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
date_default_timezone_set('Europe/London');

set_include_path('.' . PATH_SEPARATOR . '../library'
. PATH_SEPARATOR . get_include_path());

//If class not found instanciate it automatically
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

// setup controller
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(true);
$frontController->setParam('useDefaultControllerAlways', true);
$frontController->setControllerDirectory('../application/controllers');

// run!
//$frontController->dispatch();

require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
	->run();