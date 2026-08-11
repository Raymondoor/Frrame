<?php declare(strict_types=1);
	if($_ENV['APP_DEBUG'] === '1' && $_ENV['APP_PROD'] === '0'){
		ini_set('display_errors','1');
		ini_set('display_startup_errors','1');
		error_reporting(E_ALL);
	}
	if(($_ENV['APP_PROD'] === '1')){
		ob_start();
		ini_set('display_errors','0');
		ini_set('display_startup_errors','0');
		error_reporting(0);
		// Set warnings as Exception as well.
		set_error_handler(function($errno, $errstr, $errfile, $errline){
			throw new \ErrorException($errstr, $errno, E_ALL, $errfile, $errline);
		});
		set_exception_handler(function($exception){
			ob_end_clean();
			$log = "--- ".date('Y-m-d H:i:s',)." Uncaught Error/Exception ---\n";
			$log .= "\t".$exception::class." thrown in ".$exception->getFile()." : ".$exception->getLine()." : ".$exception->getMessage()."\n";
			$log .= "\tTrace: ".implode("->", $exception->getTrace())."\n";
			$log .= "------\n\n";
			if(error_log($log,3,__DIR__.'/../log/error.log')){
				// Application side fault
				http_response_code(500);
			}else{
				// Even this handler failed.
				http_response_code(503);
			}
			exit;
		});
	}

date_default_timezone_set($_ENV['APP_TIMEZONE']);
