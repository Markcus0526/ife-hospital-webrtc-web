<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
	header ("Cache-Control: no-cache, must-revalidate"); 
	header ("Pragma: no-cache"); 
	
    define('DEFAULT_PHP',       'index.php');

	require_once("core/global.php");
	
	$_rurl = isset($_SERVER["REDIRECT_URL"]) ? $_SERVER["REDIRECT_URL"] : (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "");
	$_rurl = urldecode(substr($_rurl, strlen(SITE_BASE)));
	$_params = preg_split("/\//", $_rurl);
	
	define("HOME_BASE", "");
	define("APP_BASE", defined('FRONTEND_APP') ? FRONTEND_APP : "application");

    if (isset($_GET["lang"]))
    	_lang($_GET["lang"]);
		
	include_once("resource/lang/" . _lang() . ".php");

	if ($_params[0] == "api" || $_params[0] == "apitest") {
		define("API_MODE", "1");
		if ($_params[0] == "apitest")
			define("API_TEST", "1");
		$_params = array_slice($_params, 1);

		if (isset($_SERVER['HTTP_ORIGIN'])) {
			header("Access-Control-Allow-Origin:*");
			header("Access-Control-Allow-Headers:accept, content-type");
			header("Access-Control-Allow-Methods:GET, POST, OPTIONS");
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

			exit(0);
		}	
	}

	load_controller(
		get_param(0, "home"), 
		get_param(1, "index"), 
		get_param(2), 
		APP_BASE . "/controller/");

	function get_param($i, $default = null) 
	{
		global $_params;
		if ($default == null) {
			return array_slice($_params, $i);
		}
		else {
			return count($_params) > $i && $_params[$i] != "" ? $_params[$i] : $default;
		}
	}

	function load_controller($_controller, $_action, $_params, $_path)
	{
		global $cur_controller;
		
		$controller_class = stripslashes($_controller) . "Controller";
		$_path .= $controller_class . ".php";

		if (file_exists($_path))
		{
			require_once($_path);

			$cur_controller = new $controller_class;
			$cur_controller->process($_controller, $_action, $_params);
		}
		else
		{
			$cur_controller = new controller;
			$cur_controller->check_error(ERR_NOTFOUND_PAGE);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/