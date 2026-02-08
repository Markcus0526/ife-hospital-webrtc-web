<?php
	/************************* Copyright Info ***************************
	*   Project Name:       MARKCUS World Tele Clinic System                *
	*   Framework:          MAL MVC Web Framewrok v1.0                  *
	*   Author:             Markcus                                        *
	*   Date:               2017/10/02                                  *
	*                                                                   *
	*   2017 ©      ALL Rights Reserved.                                *
	************************** Copyright Info **************************/
	
	define('OB_DISABLE',        true);
	define('DEFAULT_PHP',       'lang.php');
	define("TIME_LIMIT",        5);

	require_once("core/global.php");

	$lang = "zh-CN";
	if (isset($argv[1]))
		$lang = $argv[1];
	else {
		print "Invalid arguments.\n";
		print "for example:\n";
		print "php lang.php en-US\n";
		exit;
	}

	$option = "";
	if (isset($argv[2]))
		$option = $argv[2];

	$lang_path = 'resource/lang/' . $lang . '.php';	
	if (is_file(SITE_ROOT . $lang_path))
	{
		@include_once($lang_path);
	}
	else {
		$g_string[$lang]=array();
	}

	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	$folders = array(
		"application",
		"core",
		"plugins",
	);

	function pl($str)
	{
		print $str;
		print "\n";
	}

	function set_string($matches)
	{
		global $g_new_string, $g_string, $lang;
		foreach ($matches as $str) {
			if (isset($g_string[$lang]) && isset($g_string[$lang][$str]))
				$val = $g_string[$lang][$str];
			else
				$val = $str;
			$g_new_string[$str] = $val;	
		}
	}

	function parse_file($path, $file)
	{
		if (_ext($path) == "php" || $file == "config.inc")
		{
			$content = _fread($path);

			if (preg_match_all("/[^a-zA-Z]l{1,2}\([\'\"]{1}([^\'\"]+)[\'\"]{1}[\,\)]{1}/i", $content, $matches)) {
				pl("parse $path");
				set_string($matches[1]);
			}

			if (preg_match_all("/define\(\'PRODUCT_NAME\',[\t]+[\'\"]{1}([^\'\"]+)[\'\"]{1}/i", $content, $matches)) {
				pl("parse $path");
				set_string($matches[1]);
			}

			if (preg_match_all("/define\(\'MAIL_FROMNAME\',[\t]+[\'\"]{1}([^\'\"]+)[\'\"]{1}/i", $content, $matches)) {
				pl("parse $path");
				set_string($matches[1]);
			}
		}
	}

	function each_folder($folder)
	{
		//pl("folder " . $folder);
		if ($d = opendir($folder)) {
			while (false !== ($file = readdir($d))) {
				if ($file == "." || $file == "..")
					continue;

				$path = $folder . "/" . $file;
				if (is_dir($path)) {
					each_folder($path);
				}
				else {
					parse_file($path, $file);
				}

		    }
		}
	}

	foreach ($folders as $folder) {
		each_folder(SITE_ROOT . $folder);
	}

	if (count($g_new_string)) {
		$string_path = SITE_ROOT . "resource/lang/$lang.php";

		pl("");
		pl("Generate $string_path");

		$fp = fopen($string_path, "w+");
		if ($fp)
		{
			$string = "<?php\n";
			$string .= '$g_string["' . $lang .'"]=array(' . "\n";
			foreach ($g_new_string as $key => $value) {
				$key = preg_replace('/\n/', "\\n", $key);
				$key = preg_replace('/\"/', "\\\"", $key);
				$value = preg_replace('/\n/', "\\n", $value);
				$value = preg_replace('/\"/', "\\\"", $value);
				if ($option == "strip_single_quote")
					$value = str_replace("\\'", "'", $value);
				$string .= "	\"" . $key . "\" => \"" . $value . "\"," . "\n";
			}

			$string .= ");\n";
			
			fwrite($fp, $string);

			fclose($fp);
		}
	}