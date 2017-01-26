<?php

use Doctrine\Common\ClassLoader;

use Symfony\Component\Debug\Debug;

require_once __DIR__.'/../app/bootstrap.novice.php';

$environement = 'dev';
$debug = true;

if($debug == true){
	Debug::enable();
}
else{
	function myErrorHandler($errno, $errstr, $errfile, $errline) {
		error_reporting(E_ALL ^ E_USER_DEPRECATED);
		switch($errno){
			case E_RECOVERABLE_ERROR:
				throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
				break;
			/*case E_USER_DEPRECATED:
				//throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
				return true;
				break;*/
			default:
				break;
		}
		return false;
	}
	set_error_handler('myErrorHandler');
	//error_reporting(E_ALL ^ E_USER_DEPRECATED);
}

require_once __DIR__.'/../app/App.php';
$app = new App($environement, $debug);
$app->run();
exit;
exit($app->getExecutionTime());