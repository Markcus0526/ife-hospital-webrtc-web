<?php
	/************************* Copyright Info ***************************
	*   Project Name:       3QC World Tele Clinic System                *
	*   Framework:          MAL MVC Web Framewrok v1.0                  *
	*   Author:             Quan                                        *
	*   Date:               2017/10/02                                  *
	*                                                                   *
	*   2017 ©      ALL Rights Reserved.                                *
	************************** Copyright Info **************************/
	
	define('OB_DISABLE',        true);
	define('DEFAULT_PHP',       'batch.php');
	define("TIME_LIMIT",        5);

	require_once("core/global.php");

	if (!isset($argv[0])) {
		print "You could not access this page from web browser.\n";
		exit;
	}

	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	class batchController {
		private static $batch;

		public function __construct(){
			_my_mobile('batch');
			_my_name('batch');

			static::$batch = new batchModel(false);
		}

		public function run() {
			while(true) {
				static::$batch->run();
				sleep(TIME_LIMIT);
			}
		}
	}

	$batch = new batchController;

	$batch->run();
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.                     *
	*   2017 ©      ALL Rights Reserved.                                *
	**************************** The END *******************************/