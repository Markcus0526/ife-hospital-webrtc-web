<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	@include_once("config.inc");

	define('LOG_MODE',			1); // 0:NONE, 1:DEBUG
	
	if (!LOG_MODE) {
		error_reporting(0);
		ini_set('display_errors', '0');
	}
	else {
		error_reporting(E_ALL ^ E_NOTICE);
	}

	define('PRODUCT_NAME',		"MARKCUS国际医疗视频会诊系统");
	define('CORE_VERSION',		'1.0');

	define('SITE_BASE',			preg_replace('/\/'. DEFAULT_PHP . '/i', '', $_SERVER["SCRIPT_NAME"]) . "/");
	$http_schema = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on") ? "https" : "http");
	define("SITE_ORIGIN",		$http_schema . "://" . $_SERVER["HTTP_HOST"]);
	define("SITE_BASEURL",		SITE_ORIGIN . SITE_BASE);
	define('SITE_ROOT',			preg_replace('/.core.global.php/i', '', __FILE__) . "/");

	define("SITE_MODE",			0); // 0:Standard 1:CreateMockup 2:Mockup
	define("IS_NOMOCKUP",		SITE_MODE == 0);
	define("IS_CREATEMOCKUP",	SITE_MODE == 1);
	define("IS_MOCKUP",			SITE_MODE == 2);

	define('LOG_PATH',			SITE_ROOT . 'log/');

	define('TMP_URL',			'tmp/');
	define('TMP_PATH',			SITE_ROOT . TMP_URL);
	define('DATA_PATH',			SITE_ROOT . 'data/');
	define('AVARTAR_URL',		'avartar/');
	define('AVARTAR_PATH',		DATA_PATH . AVARTAR_URL);
	define('ATTACH_URL',		'attach/');
	define('ATTACH_PATH',		DATA_PATH . ATTACH_URL);
	define('CHAT_URL',			'chat/');
	define('CHAT_PATH',			DATA_PATH . CHAT_URL);

	define('AVARTAR_SIZE',		240); // 240x240
	define('THUMB_SIZE',		100); // 100x100
		
	define('PAD_SIZE',			4);
	define('HASH_SIZE',			32);
	define('CLEAR_REQSESS_KEY',	'c');

	// browser flag
	define('ISIE6',				(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], "MSIE 6.0")) ? true : false);
	define('ISIE7',				(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], "MSIE 7.0")) ? true : false);
	define('ISIE8',				(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], "MSIE 8.0")) ? true : false);
	define('ISIE11',				(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], "rv:11.0")) ? true : false);
	define('ISIE',				ISIE6 | ISIE7 | ISIE8 | ISIE11);
	define('ISEDGE',			(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], "Edge")) ? true : false);

	define("MAIL_HEADER", "MARKCUS");
	define("MAIL_FOOTER", "--「MARKCUS」--");

	if (_request("TOKEN") != null)
		_load_session_from_token(_request("TOKEN"));
	else 
		session_start();

	if (!defined('DEFAULT_LANGUAGE')) 
		define('DEFAULT_LANGUAGE', 'zh-CN');

	include_once("consts.php");

	if (defined('OB_DISABLE'))
    	ob_implicit_flush();
	else
		ob_start("mb_output_handler");
	//header("Content-Type: text/html; charset=UTF-8");
	// setting mbstring
	mb_internal_encoding("UTF-8");

	// cache related
	$g_cache_key = null;

	$g_timezone = null; 
	if (_request("brtt") != null)
		_reset_timezone();
	else
		$g_timezone = _time_zone();

	include_once("core/controller.php");
	include_once("core/module.php");
	include_once("core/timezones.php");
	include_once("db/model.php");

	//---------------------
	// 1. Auto loadding class
	//---------------------
	function __autoload($class_name)
	{
		if ($class_name == "db") {
			include_once("db/db.php");
		}
		else if (preg_match('/PayPal/', $class_name)) {
			$class_dir = str_replace("\\", "/", $class_name);
			require_once("plugins/payment/PayPal-PHP-SDK/paypal/rest-api-sdk-php/lib/$class_dir.php");
		}
		else if (preg_match('/Psr/', $class_name)) {
			$class_dir = str_replace("\\", "/", $class_name);
			require_once("plugins/payment/PayPal-PHP-SDK/psr/log/$class_dir.php");
		}
		else if (preg_match('/Module$/', $class_name)) {
			include_once(APP_BASE . "/module/" . $class_name . ".php");
		}
		else if (preg_match('/Helper$/', $class_name)) {
			include_once("core/helpers/" . $class_name . ".php");
		}
		else if (preg_match('/Model$/', $class_name)) {
			include_once(FRONTEND_APP . "/model/" . $class_name . ".php");
		}
		else if (file_exists("core/" . $class_name . ".php")){
			include_once("core/" . $class_name . ".php");
		}
		else if ($class_name == "PHPMailer") {
			include_once("plugins/mail/" . strtolower($class_name) . ".php");
		}
		else if ($class_name == "xml") {
			include_once("plugins/xml/" . $class_name . ".php");
		}
		else if ($class_name == "ldap") {
			include_once("plugins/ldap/" . $class_name . ".php");
		}
		else if ($class_name == "TCPDF") {
			include_once("plugins/tcpdf/" . strtolower($class_name) . ".php");
		}
		else if ($class_name == "PHPExcel") {
			include_once("plugins/phpexcel/" . strtolower($class_name) . ".php");
		}
		else if ($class_name == "smsManager") {
			include_once("plugins/sms/" . strtolower($class_name) . ".php");
		}
		else if($class_name == "paybase" ||
			$class_name == "chinapay" ||
			$class_name == "paypal") {
			include_once("plugins/payment/" . $class_name . ".php");
		}
	}

	//---------------------
	// 2. HTTP related
	//---------------------
	
	// get data of Query
	function _request($name)
	{
		$ret = _post($name);
		if ($ret != null)
			return $ret;

		return _get($name);
	}

	// get POST data
	function _post($txt, $key=null)
	{
		global $_POST;
		
		if ($key == null)
			$ret = isset($_POST[$txt]) ? $_POST[$txt] : null;
		else
			$ret = isset($_POST[$txt][$key]) ? $_POST[$txt][$key] : null;

		if(!isset($ret))
			return $ret;

		$ret = str_replace("\\\\", "\\", $ret);
		$ret = str_replace("\\\"", "\"", $ret);
		$ret = str_replace("\\'", "'", $ret);

		return $ret;
	}
	
	// get GET data
	function _get($name)
	{
		global $_GET;
		
		return isset($_GET[$name]) ? $_GET[$name] : null;
	}
	
	// clear/get/set Session data
	function _session($name=null, $value="@no_val@")
	{
		global $_SESSION;
		if ($name == null && $value == "@no_val@")
		{
			global $_COOKIE;
			if (isset($_COOKIE[session_name()]))
				setcookie(session_name(), '', time()-42000, '/');
			session_destroy();
		}
		else {
			if (defined('APP_BASE'))
				$name = APP_BASE . $name;
			if ($value == "@no_val@") {
				if (!is_array($_SESSION) || !array_key_exists($name, $_SESSION))
					return null;

				return $_SESSION[$name];
			}
			else 
				$_SESSION[$name] = $value;	
		}
	}

	// get/set Cookie data
	function _cookie($name=null, $value="@no_val@")
	{
		global $_COOKIE;
		if ($value == "@no_val@") {
			if (!array_key_exists($name, $_COOKIE))
				return null;

			return $_COOKIE[$name];
		}
		else 
			setcookie($name, $value, time() + 3600 * 24 * 30, '/');
	}
	
	function _load_ip_session()
	{
		$session_id = str_replace(".", "a", _ip());

		session_write_close();
		session_id($session_id);
		session_start();
	}

	function _token()
	{
		$user_id = _my_id();
		$session_id = session_id();
		return $user_id . ":" . $session_id;
	}

	function _load_session_from_token($token)
	{
		if ($token != null) {
			$tokens = @preg_split("/:/", $token);
			if (count($tokens) == 2) {
				$org_session_id = session_id();
				$user_id = $tokens[0];
				$session_id = $tokens[1];
				@session_write_close();
				session_id($session_id);
				session_start();
				$session = sessionModel::get_model(array($session_id, $user_id));
				if ($session == null || $session->user_id != $user_id) {
					session_write_close();
					session_id($org_session_id);
					session_start();
					return false;
				}

				if ($session->user_id == $user_id) {
					if (_my_id() != $user_id) {
						$user = userModel::get_model($user_id);
						if ($user == null)
							return false;
						userModel::init_session_data($user);
					}
					else {
						return true;
					}
				}
				return false;
			}
		}

		return false;
	}
	
	// clear/get/set Server data
	function _server($name=null, $value="@no_val@")
	{
		global $_SESSION;

		$old_session_id = session_id();
		session_write_close();

		session_id("SERVER");
		session_start();
		
		$ret = null;
		if ($name == null && $value == "@no_val@")
		{
			$_SESSION = array();
		}
		else if ($value == "@no_val@") {
			if (!array_key_exists($name, $_SESSION))
				$ret = null;
			else
				$ret = $_SESSION[$name];
		}
		else 
			$_SESSION[$name] = $value;
		session_write_close();

		session_id($old_session_id);
		session_start();

		return $ret;
	}
	
	// goto URL
	function _abs_goto($url)
	{
		ob_clean();
		header('Location: ' . $url);
		exit;
	}

	function _goto($url)
	{
		ob_clean();
		header('Location: ' . _abs_url($url));
		exit;
	}

	function _nocache()
	{
		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");   // always modified
		header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
		header ("Pragma: no-cache");  // HTTP/1.0
	}
	
	// convert relative url to absolute url
	function _abs_url($url)
	{
		return SITE_BASEURL . HOME_BASE . $url;
	}

	function _url($url)
	{
		return HOME_BASE . $url;
	}

	function _js_url($js_path)
	{
		if (DEBUG_MODE)
		{

		}
		else {
			$js_path .= ".min";
		}
		$js_path .= ".js?" . VERSION;
		return $js_path;
	}

	function _open_url($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_exec($ch);
		curl_close($ch);
	}

	function _server_os()
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
			return 'WIN';
		else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX')
			return 'LINUX';

		return '';
	}

	function _client_os()
	{
		global $_SERVER;
		$u_agent = $_SERVER['HTTP_USER_AGENT']; 
	    $platform = 'Unknown';

	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'LINUX';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'MAC';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'WIN';
	    }

	    return $platform;
	}

	//---------------------
	// 3. String & Number & Date related
	//---------------------

	function _is_empty($str)
	{
		return $str === null || $str === "";
	}

	function _html2str($html)
	{
		$html = preg_replace('/<br[\/]>/i', '#@NR@#', $html);
		$html = preg_replace('/<\/p>/i', '#@NR@#', $html);

		$str = strip_tags($html);
		$str = preg_replace('/\&nbsp\;/i', ' ', $str);
		$str = preg_replace('/\&quot\;/i', '"', $str);
		$str = preg_replace('/\&gt\;/i', '>', $str);
		$str = preg_replace('/\&lt\;/i', '<', $str);
		$str = preg_replace('/[\b]+/i', ' ', $str);

		$str = str_replace('#@NR@#', '\n', $str);		
		$str = preg_replace('/[\n\r]+/i', ' ', $str);

		return $str;
	}

	function _intro_html($str, $max_size = 300)
	{
		$str = htmlspecialchars($str);
		$str = str_replace('\n', '<br/>', $str);
		$str = mb_substr($str, 0, $max_size);

		$p = strrpos($str, '.');

		if ($p !== FALSE) {
			$str = substr($str, 0, $p);	
			$str .= ".";
		}
		else {
			$str .= "...";
		}

		return $str;
	}

	// generate sql safe string
	function _sql($txt, $db=null)
	{
		if ($txt === null || $txt === "")
			return "NULL";

		if ($txt === "##NOW()")
			return "NOW()";

		if ($db == null)
			$db = db::get_db();
		if ($db) {
			$txt = $db->escape($txt);
		}
		else {
			$txt = str_replace("'", "''", $txt);	
		}
		return "'" . $txt . "'";
	}

	function _sql_order($order)
	{
		if ($order == "DESC")
			return $order;
		else
			return "ASC";
	}

	function _sql_field($field)
	{
		return preg_replace('/[^\.\w\d]/i', "", $field);
	}

	function _sql_number($field)
	{
		return preg_replace('/[^\.\d]/i', "", $field);
	}

	function _DateTime($d=null)
	{
		try {
			if ($d == null) {
				$d = new DateTime('now', new DateTimeZone(_time_zone()));
			}
			else if (is_string($d)) {
				$d = str_replace(".", "-", $d);
				$d = new DateTime($d, new DateTimeZone(_time_zone()));
			}
			else if (is_numeric($d)) {
				$dt = new DateTime('now', new DateTimeZone(_time_zone()));
				$dt->setTimestamp($d);
				$d = $dt;
			}
		}
		catch(Exception $e) {
			$d = _DateTime();
		}

		return $d;
	}

	function _date($d=null, $format="Y-m-d")
	{
		$d = _DateTime($d);
		return $d->format($format);
	}

	function _date_time($d=null, $format="Y-m-d H:i:s")
	{		
		$d = _DateTime($d);
		return $d->format($format);
	}

	function _date_add($d, $days=0, $format="Y-m-d")
	{
		$d = _DateTime($d);
		$d = $d->modify($days . ' day');

		return $d->format($format);
	}

	function _time($d=null, $format="H:i")
	{
		$d = _DateTime($d);
		return $d->format($format);
	}

	function _time_add($d, $hours=0, $format="H:i")
	{
		$d = _DateTime($d);
		$d = $d->modify($hours . ' hour');

		return $d->format($format);
	}

	function _l_date($d=null, $lang=null)
	{
		$d = _DateTime($d);
		if ($lang == null)
			$lang = _lang();
		$format = "Y-m-d";
		if ($lang == 'en-US') 
			$format = "F j, Y";
		return $d->format($format);
	}

	function _l_time($d=null, $lang=null)
	{
		$d = _DateTime($d);
		if ($lang == null)
			$lang = _lang();
		$format = "H:i";
		if ($lang == 'en-US') 
			$format = "g:i a";
		return $d->format($format);
	}

	function _datetime_label($date)
	{
		$now = _DateTime(); 
		$date = _DateTime($date);
		$diff = $now->diff($date);
		$diff_time= $diff->format("%i");

		if ($diff_time < 0)
			$suffix = "後";
		else
			$suffix = "前";

		if ($diff_time == 0)
			return "1分" . $suffix;

		if ($diff_time < 60)
			return $diff_time . "分" . $suffix;

		$diff_time= round($diff_time / 60);
		if ($diff_time < 24)
			return $diff_time . "时间" . $suffix;

		$diff_day = round($diff_time / 24);
		if ($diff_day < 5)
			return $diff_day . "日" . $suffix;
		return date("Y/m/d", $date);
	}

	function _seconds_label($seconds, $zero_label = "&nbsp;")
	{
		if ($seconds === null)
			$s =$zero_label;
		else {
			$s = $seconds;
			if ($s <= 0) {
				if ($zero_label)
					$s =$zero_label;
				else
					$s = "00:00:00";
			}
			else
				$s = sprintf("%02d:%02d:%02d", $s / 3600, ($s % 3600) / 60, $s % 60);
		}

		return $s;
	}

	function _in_period($from, $to=null, $d=null) {		
		$from == _date($from, "Y-m-d");
		$to = (($to == null) ? _date(null, "Y-m-d") : _date($to, "Y-m-d"));
		$d = _date($d, "Y-m-d");
		if ($d >= $from AND $d <= $to)
			return true;
		return false;
	}

	function _days_in_month($year, $month)
	{
		return cal_days_in_month(CAL_GREGORIAN, $month, $year);
	}

	function _month_first_day($date)
	{
		if ($date == null || substr($date, 0, 4) == 0)
			return '0000-00-00';

		$year = substr($date, 0, 4);
		$month = substr($date, 5, 2);

		return "$year-$month-01";
	}

	function _month_last_day($date)
	{
		if ($date == null || substr($date, 0, 4) == 9999)
			return '9999-12-31';

		$year = substr($date, 0, 4);
		$month = substr($date, 5, 2);
		$days = _days_in_month($year, $month);

		return "$year-$month-$days";
	}

	function _from_date($date, $view_to_db = true)
	{
		if ($view_to_db) {
			// view to db
			return $date == null ? '0000-00-00' : $date;
		}
		else {
			// db to view
			return substr($date, 0, 4) == 0 ? null : $date;
		}
	}

	function _to_date($date, $view_to_db = true)
	{
		if ($view_to_db) {
			// view to db
			return $date == null ? '9999-12-31' : substr($date, 0, 10) . " 23:59:59";
		}
		else {
			// db to view
			return substr($date, 0, 4) == '9999' ? null : $date;
		}
	}

	/**
	 * 第ｎ ｗ曜日の日付を求める
	 * @param <integer> $year    年
	 * @param <integer> $month   月
	 * @param <integer> $no      第ｎ週
	 * @param <integer> $week    0 (日曜)から 6 (土曜)
	 * @return <DateTime>
	 */
	function _nth_week_day($year, $month, $no, $week)
	{
	    // 最初の一週間分の曜日を求める
	    // 1日の曜日を求める

	    $date = new DateTime();
	    /*
	     * 以下の記述は PHP 5.3.0 以降で可能
	     * それ以前の PHP では setDate が成功した場合は null がリターンされるので、
	     * 2行に分けて $date->format('w'); のように記述すること
	     */
	    // 指定年月の１日の曜日を抽出する
	    $first_week = $date->setDate($year, $month, 1)
	                       ->format('w'); // 0 (日曜)から 6 (土曜) を取得する

	    // 1日の曜日の指定週の日付を求める
	    $day = ($no - 1) * 7 + 1;

	    // 指定曜日と1日の曜日の差分（日数）を求め、指定の日付を計算する
	    $diff = $week - $first_week;
	    if($diff < 0) {
	        $day += $diff + 7; // 1日の曜日より前の曜日の場合
	    } else {
	        $day += $diff;     // 1日の曜日より後の曜日の場合
	    }

	    // 組み立てた日付が月の最終日（日数）よりも大きい場合は false リターン
	    if($date->format('t') < $day) {
	        return false;
	    }

	    // 前述のとおり、PHP 5.3.0 以降での記述
	    return $date->setDate($year, $month, $day);
	}

	function _nth_week($d=null, $format="Y-m-d")
	{
		if ($d == null)
			$d = date($format, time());
		$time = strtotime($d);
        $year = date("Y", $time);
        $month = date("n", $time);
        $day = date("j", $time);

        $first_day = date_format(_nth_week_day($year, $month, 1, 0), "d") + 0;
        $week = 0; // 週
        $wday = 0; // 1 (月曜)から 7 (日曜)

        if ($first_day > $day) { // that day is in last week
        	$wday = $day + 7 - $first_day;
            $year = ($month == 1) ? $year - 1 : $year;
            $month = ($month == 1) ? 12 : $month - 1;
            $days = _days_in_month($year, $month);
            $first_day = date_format(_nth_week_day($year, $month, 1, 0), "d") + 0;
            $week = ($first_day + 28 < $days) ? 5 : 4;
        } else {
        	$days = _days_in_month($year, $month);
            $week = ceil(($day - $first_day + 1) / 7);
            $wday = $day - 7 * ($week - 1) - $first_day;
        }

	    return array("year"=>$year, "month"=>$month, "week"=>$week, "wday"=>$wday, "day"=>$day);
	}

	// sunday in week
	function _week_start($d)
	{
		if (is_string($d)) {
			$d = str_replace(".", "-", $d);
		}
		$date = _DateTime($d);
		$w = $date->format('w');
		if ($w < 7) {
			$date = $date->modify("-" . $w . " day");
		}
		return $date->format('Y-m-d');	
	}
	
	function _week_end($d)
	{
		if (is_string($d)) {
			$d = str_replace(".", "-", $d);
		}
		$start = _week_start($d);
		$date = _DateTime($start);
		$date = $date->modify("+6 day");
		return $date->format('Y-m-d');
	}

	function _age($d=null)
	{
		$bir_y = _date($d, 'Y') * 1;
		$now_y = _date(null, 'Y') * 1;
		$bir_md = _date($d, 'md');
		$now_md = _date(null, 'md');

		$age = $now_y - $bir_y;
		if ($now_md < $bir_md)
			$age --;

		if ($age < 0)
			$age = 0;

		return $age;
	}

	function _normalize_time($time)
	{
		$ts = explode(":", $time);
		if ($ts == null || count($ts) < 2)
			return null;

		return str_pad($ts[0], 2, "0", STR_PAD_LEFT) . ":" . 
			str_pad($ts[1], 2, "0", STR_PAD_LEFT);
	}

	function _minutes2str($minutes)
	{
		if ($minutes === null || $minutes === '')
			return "";
		
		$hour = floor($minutes / 60);
		$minute = $minutes % 60;

		return str_pad($hour, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minute, 2, "0", STR_PAD_LEFT);
	}

	function _str2minutes($str)
	{
		if ($str === null || $str === '')
			return null;

		$arr = preg_split("/:/", $str);
		$len = count($arr);
		if ($len > 0) {
			$hour = $arr[0] + 0;
			if ($hour < 0) $hour = 0;

			if ($len > 1) {
				$minute = $arr[1] + 0;
				if ($minute < 0) $minute = 0;
				else if ($minute > 59) $minute = 59;
			}
			else {
				$minute = 0;
			}

			return $hour * 60 + $minute;
		}
		else
			return null;
	}

	function _minutes2hours($minutes)
	{
		if ($minutes === null || $minutes === '')
			return "";
		
		$hour = round($minutes / 60, 2);

		return $hour;
	}

	function _minutes2hour($minutes)
	{
		if ($minutes === null || $minutes === '')
			return "";
		
		return floor($minutes / 60);
	}

	function _str2hours($str)
	{
		$minutes = _str2minutes($str);
		if ($minutes <= 0)
			return 0;

		return _minutes2hours($minutes);
	}

	function _hours2minutes($hours)
	{
		if ($hours === null || $minutes === '')
			return null;

		return floor($hours * 60);
	}

	function _trim_all($s)
	{
		return str_replace(" ", '', $s);
	}

	function _is_valid_number($val)
	{
		if (intval($val) == NULL)
			return false;

		return true;
	}

	function _is_valid_date($val)
	{
		$ret = date_parse($val);

		if ($ret["error_count"] > 0)
			return false;

		return true;
	}

	function _reset_timezone()
	{
		$browser_timestamp = _request("brtt");
		$reg = '/(GMT[\s\+\-]\d{3,4})/i';
		preg_match($reg, $browser_timestamp, $matches);

		$time_zone = $matches[1];
		if (isset($time_zone)) {
			$time_zone = preg_replace("/(GMT)([\s\+]*)(\d{1,2})(\d{2})$/", "$1+$3$4", $time_zone);
			$time_zone = preg_replace("/(GMT)([\s\-]*)(\d{1,2})(\d{2})$/", "$1-$3$4", $time_zone);

			_time_zone($time_zone);
		}
	}

	function _time_zone($time_zone = null, $mysql_mode = false)
	{
		global $g_timezone;
		if ($time_zone == null) { // read
			if ($g_timezone == null)
				$g_timezone = _session('BROWSER_TIME_ZONE');
			if ($g_timezone == null && defined("TIME_ZONE")) {
				$g_timezone = TIME_ZONE;
			}

			if ($mysql_mode) {
				$m_timezone = preg_replace("/(GMT)([\+\-]*)(\d{1,2})(\d{2})$/", "$2$3:$4", $g_timezone);
				return $m_timezone;
			}
			else
				return $g_timezone;
		}
		else { // write
			_session('BROWSER_TIME_ZONE', $time_zone);
			$g_timezone = $time_zone;
		}
	}
	
	function _str2html($str)
	{
		$str = htmlspecialchars($str);
		//$str = preg_replace('/ /i', '&nbsp;', $str);
		return nl2br($str);
	}

	function _str2paragraph($str)
	{
		$str = htmlspecialchars($str);

		$ps = preg_split("/\n/", $str);
		
		$str = "";

		foreach($ps as $p)
		{
			$str .= "<p>" . $p . "</p>";
		}

		return $str;
	}

	function _shift_space($str, $shift=1)
	{
		$ps = preg_split("/\n/", $str);
		
		$str = array();
		
		$space = "";
		for ($i = 0; $i < $shift; $i ++) {
			$space .= "   ";
		}
		foreach($ps as $p)
		{
			$str[] = $space . $p;
		}


		return implode("\n", $str);
	}

	function _str2firstparagraph($str)
	{
		$str = htmlspecialchars($str);

		$ps = preg_split("/\n/", $str);
		
		if (count($ps) > 0) 
			$str = "<p>" . $ps[0] . "</p>";
		else
			$str = "<p></p>";

		return $str;
	}

	function _str2json($str) 
	{
		$str = str_replace("\\", "\\\\", $str);
		$str = str_replace("\r", "", $str);
		$str = str_replace("\n", "\\n", $str);
		$str = str_replace("\"", "\\\"", $str);
		return $str;
	}

	function _number($v) 
	{
		if ($v == null)
			return "0";
		return number_format($v);
	}

	function _round($v, $point=0, $type=2)
	{
		$p = pow(10, $point);
        if ($type == 0) # 切上
            return ceil($v * $p) / $p;
        else if ($type == 1) # 切捨
            return floor($v * $p) / $p;
        else if ($type == 2) # 四捨五入
            return round($v * $p) / $p;
        else # なし
            return $v;
	}

	function _currency($v) 
	{
		if ($v == null)
			return "0";
		$c = number_format($v, 2, '.', ',');
		$c = preg_replace("/(\.[1-9]*)([0]+)$/", "$1", $c);
		$c = preg_replace("/\.$/", "", $c);
		return $c;
	}

	function _cunit($cunit)
	{
		switch ($cunit) {
			case 'rmb':
				return "￥";
			case 'usd':
				return "$ ";
			default:
				return "";
		}
	}

	function _now()
	{
		$db = db::get_db();
		$now = $db->scalar("SELECT NOW()");

		return _date_time($now);
	}

	function _suffix($str, $suffix)
	{
		if (_is_empty($str))
			return "";
		else 
			return $str . $suffix;
	}

	//---------------------
	// 4. Uploading related
	//---------------------
	function _mkdir($dir)
	{
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
	}

	function _rmfile($file)
	{
		if (file_exists($file)) {
			unlink($file);
		}
	}

	function _rmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") 
						_rmdir($dir."/".$object); 
					else 
						@unlink($dir."/".$object);
				}
			}
			reset($objects);
			@rmdir($dir);
		}
	}

	function _upload($field, $dest_path)
	{
		global $_FILES;
		if ($_FILES[$field]["error"] != 0)
			return null;

		if (!move_uploaded_file($_FILES[$field]["tmp_name"], $dest_path))
			return null;

		$ext = _get_uploaded_ext($field);
		if ($ext == 'jpg')
		{
			// from iphone
			$exif = exif_read_data($dest_path);
			if ($exif !== FALSE) {
				$orientation = $exif['Orientation'];
				switch ($orientation) {
				   	case 3:
				   		$source = imagecreatefromjpeg($dest_path);
				    	$rotated = imagerotate($source, 180, 0);
				      	break;
				   	case 6:
				   		$source = imagecreatefromjpeg($dest_path);
				    	$rotated = imagerotate($source, -90, 0);
				      	break;
				   	case 8:
				   		$source = imagecreatefromjpeg($dest_path);
				    	$rotated = imagerotate($source, 90, 0);
				      	break;
				}
				imagejpeg($rotated, $dest_path, 100);
			}
		}

		return $_FILES[$field]["name"];
	}

	function _get_uploaded_ext_of_image($field)
	{
		global $_FILES;
		if ($_FILES[$field]["error"] != 0)
			return null;
		if ($_FILES[$field]["type"] == "image/png" ||
			$_FILES[$field]["type"] == "image/x-png")
			return "png";
		if ($_FILES[$field]["type"] == "image/jpeg" ||
			$_FILES[$field]["type"] == "image/pjpeg")
			return "jpg";
		if ($_FILES[$field]["type"] == "image/gif")
			return "gif";
		if ($_FILES[$field]["type"] == "image/bmp" ||
			$_FILES[$field]["type"] == "image/x-windows-bmp")
			return "bmp";

		return null;
	}

	function _mem_size($b_size) 
	{
		if ($b_size < 1024)
			return $b_size . "B";

		$k_size = round($b_size / 1024, 2);
		if ($k_size < 1024)
			return $k_size . "KB";

		$m_size = round($k_size / 1024, 2);
		if ($m_size < 1024)
			return $m_size . "MB";

		$g_size = round($m_size / 1024, 2);
		if ($g_size < 1024)
			return $g_size . "GB";

		$t_size = round($g_size / 1024, 2);
		
		return $t_size . "TB";
	}

	function _get_uploaded_filesize($field, $unit = 2)
	{
		global $_FILES;

		$file_size = filesize($_FILES[$field]["tmp_name"]);
		
		switch ($unit) {
			case 1: // KB
				return round($file_size / 1024, 2);
				break;

			case 2: // MB
				return round($file_size / pow(1024, 2), 2);
				break;

			case 3: // GB
				return round($file_size / pow(1024, 3), 2);
				break;

			case 4: // TB
				return round($file_size / pow(1024, 4), 2);
				break;
			
			default: // byte
				return $filesize;
				break;
		}
	}

	function _get_uploaded_ext($field)
	{
		global $_FILES;
		if ($_FILES[$field]["error"] != 0)
			return null;
		if ($_FILES[$field]["type"] == "application/pdf")
			return "pdf";
		if ($_FILES[$field]["type"] == "application/vnd.ms-excel" ||
			$_FILES[$field]["type"] == "text/plain" || 
			$_FILES[$field]["type"] == "text/csv" || 
			$_FILES[$field]["type"] == "text/tsv")
			return "csv";
		
		return _get_uploaded_ext_of_image($field);
	}

	function _uploaded_path($url)
	{
		$pos = strpos($url, "tmp/");
		if ($pos !== FALSE) {
			$tmppath = substr($url, $pos);
			return $tmppath;
		}
		return null;
	}

	//---------------------
	// 5. Image Processing
	//---------------------
	function _resize_wh($orig_w, $orig_h, $dest_w, $dest_h, $mode = RESIZE_ZOOM) {
		if ($orig_w <= 0 || $orig_h <= 0)
			return false;
		// at least one of dest_w or dest_h must be specific
		if ($dest_w <= 0 && $dest_h <= 0)
			return false;

		switch ($mode) {
			case RESIZE_ZOOM:
				// $dest_w x $dest_hに全体画像が置くように拡大縮小する。
				$s_w = $orig_w;
				$s_h = $orig_h;

				$s_x = 0;
				$s_y = 0;

				list( $new_w, $new_h ) = _constrain_wh( $orig_w, $orig_h, $dest_w, $dest_h );

				// if the resulting image would be the same size or larger we don't want to resize it
				if ( $new_w >= $orig_w && $new_h >= $orig_h )
					return false;

				return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $s_w, (int) $s_h, (int) $new_w, (int) $new_h );

			case RESIZE_CROP:
				// $dest_w x $dest_hに拡大縮小するときにオーバーする部分をクロップする。
				$aspect_ratio = $orig_w / $orig_h;
				$new_w = min($dest_w, $orig_w);
				$new_h = min($dest_h, $orig_h);

				if ( !$new_w ) {
					$new_w = intval($new_h * $aspect_ratio);
				}

				if ( !$new_h ) {
					$new_h = intval($new_w / $aspect_ratio);
				}

				$size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

				$s_w = round($new_w / $size_ratio);
				$s_h = round($new_h / $size_ratio);

				$s_x = floor( ($orig_w - $s_w) / 2 );
				$s_y = floor( ($orig_h - $s_h) / 2 );

				return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $s_w, (int) $s_h, (int) $new_w, (int) $new_h );

			case RESIZE_CONTAIN:
				// $dest_w x $dest_hに拡大縮小するときに余白を追加
				$d_w = $dest_w;
				$d_h = intval($orig_h * $dest_w / $orig_w);
				if ($d_h > $dest_h) {
					$d_h = $dest_h;
					$d_w = intval($orig_w * $dest_h / $orig_h);
				}

				$d_x = ($dest_w - $d_w) / 2;
				$d_y = ($dest_h - $d_h) / 2;

				return array( (int) $d_x, (int) $d_y, 0, 0, (int) $d_w, (int) $d_h, (int) $orig_w, (int) $orig_h, $dest_w, $dest_h );
		}

		return false;
	}

	function _constrain_wh( $w, $h, $max_w=0, $max_h=0 ) {
		if ( !$max_w and !$max_h )
			return array( $w, $h );

		$width_ratio = $height_ratio = 1.0;
		$did_width = $did_height = false;

		if ( $max_w > 0 && $w > 0 && $w > $max_w ) {
			$width_ratio = $max_w / $w;
			$did_width = true;
		}

		if ( $max_h > 0 && $h > 0 && $h > $max_h ) {
			$height_ratio = $max_h / $h;
			$did_height = true;
		}

		// Calculate the larger/smaller ratios
		$smaller_ratio = min( $width_ratio, $height_ratio );
		$larger_ratio  = max( $width_ratio, $height_ratio );

		if ( intval( $w * $larger_ratio ) > $max_w || intval( $h * $larger_ratio ) > $max_h )
	 		// The larger ratio is too big. It would result in an overflow.
			$ratio = $smaller_ratio;
		else
			// The larger ratio fits, and is likely to be a more "snug" fit.
			$ratio = $larger_ratio;

		$w = intval( $w  * $ratio );
		$h = intval( $h * $ratio );

		// Sometimes, due to rounding, we'll end up with a result like this: 465x700 in a 177x177 box is 117x176... a pixel short
		// We also have issues with recursive calls resulting in an ever-changing result. Constraining to the result of a constraint should yield the original result.
		// Thus we look for dimensions that are one pixel shy of the max value and bump them up
		if ( $did_width && $w == $max_w - 1 )
			$w = $max_w; // Round it up
		if ( $did_height && $h == $max_h - 1 )
			$h = $max_h; // Round it up

		return array( $w, $h );
	}

	function _resize_image($path, $src_ext, $dst_ext, $w, $h=null, $mode = RESIZE_ZOOM ){
		$ext = _ext($path);
		if ($ext == "")
			$ext = $dst_ext;

		if ($src_ext == "png")
			$src_img = imagecreatefrompng($path); 
		else if ($src_ext == "jpg")
			$src_img = imagecreatefromjpeg($path); 
		else {
			$src_img = imagecreatefromstring(_fread($path));
		}

		if (!is_resource($src_img))
			return false;

		$ow = imagesx($src_img);
		$oh = imagesy($src_img);

		$dims = _resize_wh( $ow, $oh, $w, $h, $mode );
		if ( ! $dims ) {
			return false;
		}
		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $new_w, $new_h ) = $dims;

		$dst_img = imagecreatetruecolor($new_w, $new_h);
		if ( is_resource($dst_img) && function_exists('imagealphablending') ) {
			if ($dst_w != $new_w || $dst_h != $new_h) {
				imagealphablending($dst_img, true);
				$back = imagecolorallocatealpha($dst_img, 255, 255, 255, 0);
				imagefilledrectangle($dst_img, 0, 0, $new_w - 1, $new_h - 1, $back);
			}
			else {
				imagealphablending($dst_img, false);
			}
		}
		
		imagecopyresampled( $dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );
		
		if ( is_resource($dst_img) && function_exists('imagesavealpha') ) 
			imagesavealpha($dst_img, true);

		if ($ext == "png")
			imagepng($dst_img, $path); 
		else if ($ext == "jpg")
			imagejpeg($dst_img, $path); 

		imagedestroy($src_img);
		imagedestroy($dst_img);

		return true;
	}

	function _resize_userphoto($path, $source_ext, $width, $height){
		$ext = _ext($path);
		if ($ext == "")
			$ext = $source_ext;

		if ($source_ext == "png")
			$src_img = imagecreatefrompng($path); 
		else if ($source_ext == "jpg")
			$src_img = imagecreatefromjpeg($path); 
		else
			return;

		$ow = imagesx($src_img);
		$oh = imagesy($src_img);
		
		$w = $width;
		$h = intval($oh * $width / $ow);
		if ($h < $height) {
			$h = $height;
			$w = intval($ow * $height / $oh);
		}
		$x = - ($w - $width) / 2;
		$y = - ($h - $height) / 2;

		$dst_img = imagecreatetruecolor($width, $height); 
		imagecopyresampled($dst_img, $src_img, $x, $y, 0, 0, $w, $h, $ow, $oh); 
		
		if ($ext == "png")
			imagepng($dst_img, $path); 
		else if ($ext == "jpg")
			imagejpeg($dst_img, $path);  

		imagedestroy($src_img);
		imagedestroy($dst_img);
	}

	function _to_jpg($src_path, $path, $maxw=null, $maxh=null){
		$img = file_get_contents($src_path);

		$src_img = imagecreatefromstring($img);
		if ($src_img === FALSE)
		{
			// check bmp
			$src_img = imagecreatefrombmp($src_path);
			if ($src_img === FALSE)
				return;	
		}

		$ow = imagesx($src_img);
		$oh = imagesy($src_img);

		if ($maxw == null && $maxh == null) {
			$w = $ow;
			$h = $oh;
		}
		else {
			if (!($ow < $maxw && $oh < $maxh)) {
				$w = $maxw;
				$h = intval($oh * $maxw / $ow);
				if ($h > $maxh) {
					$h = $maxh;
					$w = intval($ow * $maxh / $oh);
				}
			}
			else {
				$w = $ow;
				$h = $oh;
			}
		}

		$dst_img = imagecreatetruecolor($w, $h); 
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $w, $h, $ow, $oh); 

		imagejpeg($dst_img, $path); 

		imagedestroy($src_img);
		imagedestroy($dst_img);
	}

	function imagecreatefrombmp($filename)
	{
		if (! $f1 = fopen($filename,"rb")) return FALSE;

		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
		if ($FILE['file_type'] != 19778) return FALSE;

		$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
		             '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
		             '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
		$BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
		if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] = 4-(4*$BMP['decal']);
		if ($BMP['decal'] == 4) $BMP['decal'] = 0;

		$PALETTE = array();
		if ($BMP['colors'] < 16777216)
		{
			$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
		}

		$IMG = fread($f1,$BMP['size_bitmap']);
		$VIDE = chr(0);

		$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
		$P = 0;
		$Y = $BMP['height']-1;
		while ($Y >= 0)
		{
			$X=0;
			while ($X < $BMP['width'])
			{
				if ($BMP['bits_per_pixel'] == 24)
					$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
				elseif ($BMP['bits_per_pixel'] == 16)
				{  
					$COLOR = unpack("n",substr($IMG,$P,2));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 8)
				{  
					$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 4)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 1)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
					elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
					elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
					elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
					elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
					elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
					elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
					elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				else
					return FALSE;
				
				imagesetpixel($res,$X,$Y,$COLOR[1]);
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}
			$Y--;
			$P+=$BMP['decal'];
		}

		fclose($f1);

		return $res;
	}

	//---------------------
	// 6. CSV related
	//---------------------
	function _downheader($filename)
	{
		if (ISIE || ISEDGE) {
            header('Content-Disposition: attachment; filename=' . urlencode($filename));
        }
        else if (ISSafari) {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        else {
            header('Content-Disposition: attachment; filename="' . $filename . '"; filename*=UTF-8\'\'' . urlencode($filename));
        }
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: private, must-revalidate");  // HTTP/1.1
		header("Expires: 0");
	}
	
	function _csvheader($filename)
	{		
		header("ContentType: application/text-csv; charset=UTF-8");
		_downheader($filename);
	}

	function _csvrow($arr, $fp=null)
	{
		for ($i = 0; $i < count($arr); $i ++) {
			if ($arr[$i]) {
				$arr[$i] = str_replace('"', '""', $arr[$i]);
				$arr[$i] = '"' . $arr[$i] . '"';
			}
		}
		$txt = implode(",", $arr);
		$txt .= "\r\n";
		$txt = mb_convert_encoding($txt, 'SJIS-win', 'UTF-8');
		if ($fp == null)
			print $txt;
		else 
			@fputs($fp, $txt);
	}

	function _excelheader($filename, $size)
	{
		header('Content-type: application/vnd.ms-excel');
		_downheader($filename);
        header("Content-Length: " . $size);
	}

	//---------------------
	// 7. Log related
	//---------------------
	function _log($log_type, $msg, $convert_break=true)
	{
		jlog::write($log_type, $msg, $convert_break);
	}

	function _access_log($msg, $url = "")
	{
		if ($url != "")
			$url = " url:" . $url;
		_log(LOGTYPE_ACCESS, $msg . $url);
	}

	function _opr_log($msg, $convert_break=true)
	{
		$msg .= " 操作员账号编号:"._my_id();
		_log(LOGTYPE_OPERATION, $msg, $convert_break);
	}

	function _warn_log($msg, $convert_break=true)
	{
		_log(LOGTYPE_WARNING, $msg, $convert_break);
	}

	function _err_log($msg, $convert_break=true)
	{
		_log(LOGTYPE_ERROR, $msg, $convert_break);
	}

	function _debug_log($msg, $convert_break=true)
	{
		_log(LOGTYPE_DEBUG, $msg, $convert_break);
	}

	function _batch_log($msg, $convert_break=true)
	{
		_log(LOGTYPE_OPERATION, $msg, $convert_break);
	}


	//---------------------
	// 8. File related
	//---------------------
	function _is_stream( $path ) {
		$wrappers = stream_get_wrappers();
		$wrappers_re = '(' . join('|', $wrappers) . ')';

		return preg_match( "!^$wrappers_re://!", $path ) === 1;
	}

	function _fwrite($path, $str)
	{
		$fp = @fopen($path,"wb");
		if ($fp != null) {
			@fputs($fp, $str);
			@fclose($fp);
		}
	}

	function _fread($path)
	{
		$fp = @fopen($path,"rb");
		if ($fp != null) {
			$str = '';
			while (!feof($fp)) {
			  $str .= fread($fp, 8192);
			}
			@fclose($fp);
		}
		return $str;
	}

	function _basename($file, $suffix='') 
	{ 
	    return end(explode('/',$file)); 

	    //return basename($file, $suffix);
	} 

	function _rename($from, $to) {
		if (file_exists($to)) {
			$p = strrpos($to, '/');
			$dir = substr($to, 0, $p);
			$filename = substr($to, $p);
			if ($filename != '')
			{
				$p = strrpos($filename, '.');
				if ($p === FALSE)
					return;

				$pre = substr($filename, 0, $p);
				$ext = substr($filename, $p);

				if ($pre != '') {
					$p = strrpos($pre, '_');
					$new = '';
					if ($p !== FALSE) {
						$pre0 = substr($pre, 0, $p);
						$idx = substr($pre, $p + 1);
						if (is_numeric($idx))
						{
							$idx = $idx + 1;
							$new = $dir . $pre0 . '_' . $idx. $ext;	
						}
					}

					if ($new == '') {
						$new = $dir . $pre . '_1' . $ext;		
					}

					_rename($from, $new);
				}
			}
		}
		else {
			rename($from, $to);
		}
	}

	function _files_in_dir($dir) { 
		$files = array();
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir . "/" . $object) != "dir") 
						$files[] = $object; 
				} 
			} 
		} 

		return $files;
	}

	function _count_in_dir($dir) {
		$count = 0;
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir . "/" . $object) != "dir") 
						$count ++;
				} 
			} 
		} 

		return $count;
	}

	//---------------------
	// 9. User Session Related
	//---------------------
	function _my_type($utype = null) {
		if ($utype == null)
			return _session("utype");
		else
			_session("utype", $utype);
	}

	function _my_id($user_id = null) {
		if ($user_id == null)
			return _session("user_id");
		else
			_session("user_id", $user_id);
	}

	function _my_name($user_name = null) {
		if ($user_name == null)
			return _session("user_name");
		else
			_session("user_name", $user_name);
	}

	function _my_mobile($mobile = null) {
		if ($mobile == null)
			return _session("mobile");
		else
			_session("mobile", $mobile);
	}

	function _country_id($country_id = null) {
		if ($country_id == null)
			return _session("country_id");
		else
			_session("country_id", $country_id);
	}

	function _my_priv($priv = null) {
		if ($priv == null)
			return _session("priv");
		else
			_session("priv", $priv);
	}

	function _has_priv($priv_type, $priv_id = null)
	{
		$priv = _my_priv();
		if ($priv && isset($priv[$priv_type])) {
			$p = $priv[$priv_type];
			if ($priv_id != null)
				return $p & $priv_id;
			else
				return $p > 0;
		}

		return null;
	}

	$_cur_user = null;
	function _user() {
		global $_cur_user;
		if ($_cur_user == null) {
			$user = userModel::get_model(_my_id());

			if ($user != null) {
				$_cur_user = $user;
			}
		}
		return $_cur_user;
	}

	function _login_ip($login_ip = null) {
		if ($login_ip == null)
			return _session("login_ip");
		else
			_session("login_ip", $login_ip);
	}

	function _auto_login_token($token = null) {
		if ($token == null)
			return _cookie("hc_token");
		else
			_cookie("hc_token", $token);
	}
	
	function _editor_type($editor_type = null) {
		if ($editor_type == null)
			return _session("editor_type");
		else
			_session("editor_type", $editor_type);
	}

	function _logout()
    {
        _access_log("logout");
        _session();
        _auto_login_token("NOAUTO");
    }

   	function _hash($string) {
		return hash_hmac('sha1', $string, 'teleclinic=hashd30988cf88f61e27fe803a8c4cf27/markcus:2017');
	}

	function _password($password) {
		return hash_hmac('sha1', $password, 'teleclinic=3b780d30988cf88f61e27fe803a8c4cf27/markcus:2017');
	}	

	function _generate_passkey_text($length = 5) {
		$chars = '0123456789';
		$len = strlen($chars);
		$txt = '';
		for ($i = 0; $i < $length; $i++) {
			$txt .= $chars[rand(0, $len - 1)];
		}
		return $txt;
	}

	//---------------------
	// 10. File Path Related
	//---------------------
	function _tmp_path($ext = null)
	{
		_mkdir(TMP_PATH);

		$tmppath = "";
		$seed = time();
		while(1) {
			$tmpfile = sha1($seed);
			if ($ext != null)
				$tmpfile .= "." . $ext;
			$tmppath = TMP_PATH . $tmpfile;
			
			if (!file_exists($tmppath))
				break;
			
			$seed += 12345;
		}
		
		return $tmppath;
	}

	function _avartar_url($id=null, $with_cache_id=true)
	{
		if ($id == null)
			$id = _my_id(); // mine
		$url = AVARTAR_URL . $id . ".png";
		if ($with_cache_id)
			$url .= "?" . _avartar_cache_id();

		return $url;
	}

	function _avartar_cache_id()
	{
		$cache_id = _session("AVARTAR_CACHE_ID");
		if ($cache_id == null) {
			return session_id();
		}
		else {
			return $cache_id;
		}
	}

	function _renew_avartar_cache_id()
	{
		_session("AVARTAR_CACHE_ID", _rand_hash());
	}

	function _ext($path)
	{
		return strtolower(pathinfo($path, PATHINFO_EXTENSION));
	}

	function _ext_icon($path)
	{
		$ext = _ext($path);
		switch ($ext) {
			case 'pdf':
				return 'fa fa-file-pdf-o';
			case 'xls':
			case 'xlsx':
				return 'fa fa-file-excel-o';
			case 'doc':
			case 'docx':
				return 'fa fa-file-word-o';
			case 'ppt':
			case 'pptx':
				return 'fa  fa-file-powerpoint-o';
			case 'zip':
			case 'rar':
			case '7z':
				return 'fa fa-file-archive-o';
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'bmp':
			case 'gif':
			case 'tiff':
				return 'fa fa-file-image-o';
		}

		return 'icon-paper-clip';
	}

	function _filename($path)
	{
		return pathinfo($path, PATHINFO_FILENAME);
	}

	function _safe_filename($filename)
	{
		$filename = str_replace("'", "", $filename);
		$filename = str_replace("\"", "", $filename);
		return $filename;
	}

	function _dirname($path)
	{
		return pathinfo($path, PATHINFO_DIRNAME);
	}

	function _mime_type($path)
	{
		$ext = pathinfo($path, PATHINFO_EXTENSION);
		
		switch(strtolower($ext))
		{
			case 'jar': $mime = "application/java-archive"; break;
			case 'zip': $mime = "application/zip"; break;
			case 'jpeg': 
			case 'jpg': $mime = "image/jpeg"; break;
			case 'jad': $mime = "text/vnd.sun.j2me.app-descriptor"; break;
			case "gif": $mime = "image/gif"; break;
			case "png": $mime = "image/png"; break;
			case "pdf": $mime = "application/pdf"; break;
			case "txt": $mime = "text/plain"; break;
			case "doc": $mime = "application/msword"; break;
			case "ppt": $mime = "application/vnd.ms-powerpoint"; break;
			case "wbmp": $mime = "image/vnd.wap.wbmp"; break;
			case "wmlc": $mime = "application/vnd.wap.wmlc"; break;
			case "mp4s": $mime = "application/mp4"; break;
			case "ogg": $mime = "application/ogg"; break;
			case "pls": $mime = "application/pls+xml"; break;
			case "asf": $mime = "application/vnd.ms-asf"; break;
			case "swf": $mime = "application/x-shockwave-flash"; break;
			case "mp4": $mime = "video/mp4"; break;
			case "m4a": $mime = "audio/mp4"; break;
			case "m4p": $mime = "audio/mp4"; break;
			case "mp4a": $mime = "audio/mp4"; break;
			case "mp3": $mime = "audio/mpeg"; break;
			case "m3a": $mime = "audio/mpeg"; break;
			case "m2a": $mime = "audio/mpeg"; break;
			case "mp2a": $mime = "audio/mpeg"; break;
			case "mp2": $mime = "audio/mpeg"; break;
			case "mpga": $mime = "audio/mpeg"; break;
			case "wav": $mime = "audio/wav"; break;
			case "m3u": $mime = "audio/x-mpegurl"; break;
			case "bmp": $mime = "image/bmp"; break;
			case "ico": $mime = "image/x-icon"; break;
			case "3gp": $mime = "video/3gpp"; break;
			case "3g2": $mime = "video/3gpp2"; break;
			case "mp4v": $mime = "video/mp4"; break;
			case "mpg4": $mime = "video/mp4"; break;
			case "m2v": $mime = "video/mpeg"; break;
			case "m1v": $mime = "video/mpeg"; break;
			case "mpe": $mime = "video/mpeg"; break;
			case "mpeg": $mime = "video/mpeg"; break;
			case "mpg": $mime = "video/mpeg"; break;
			case "mov": $mime = "video/quicktime"; break;
			case "qt": $mime = "video/quicktime"; break;
			case "avi": $mime = "video/x-msvideo"; break;
			case "midi": $mime = "audio/midi"; break;
			case "mid": $mime = "audio/mid"; break;
			case "amr": $mime = "audio/amr"; break;
			default: $mime = "application/force-download";
		}
		return $mime;
	}

	function _unique_filename($path)
	{
		$dirname = _dirname($path);
        $ext = _ext($path);
        $filename = _filename($path);

        if ($ext != "") 
        	$ext = "." . $ext;

        $i = 0;
        $suffix = "";
        $path = "";
        do {
            if ($i > 0)
                $suffix = "_" . $i;
            $path = $dirname . "/" . $filename . $suffix . $ext;
            $i ++;
        } while (file_exists($path));

        return $filename . $suffix . $ext;
	}

	//---------------------
	// 11. Template Related
	//---------------------
	function _set_template($t)
	{
		_session("template_path", APP_BASE . "/view/" . $t . "/");
	}

	function _template($path)
	{
		$template = _session("template_path");
		if ($template == null)
			$template = APP_BASE . "/view/normal/";
		return $template . $path;
	}

	//---------------------
	// 12. View Output Related
	//---------------------
	function p($d)
	{
		if (!is_array($d))
			print $d;
	}
	function p_number($d)
	{
		print number_format($d);
	}

	function _nodata_message($data)
	{
		if (count($data) == 0) {
			?>
			<div class="alert alert-block alert-warning">
				<?php p(_err_msg(ERR_NODATA));?>
			</div>
			<?php
		}
	}

	function _insert_row($i, $cols_lg, $cols_md, $cols_sm, $cols_xs, $class="row")
	{
		if ($i > 0) {
			if ($i % $cols_lg == 0) {
				?>
				</div>
				<div class="<?php p($class); ?>">
				<?php
			}
		}
	}

	//---------------------
	// 13. Mail & SMS Related
	//---------------------

	// send email
	function _send_mail($from_name, $to_address, $to_name, $title, $body, $file_path=null, $file_name=null)
	{
		if (MAIL_ENABLE == ENABLED) {
			$mailer = new PHPMailer();
			// if ($from == null || _is_empty($from->mail_from) || $from->mail_smtp_auth != 1) {
				$mailer->From = MAIL_FROM;
				$mailer->FromName = $from_name;
				$mailer->SMTPAuth = MAIL_SMTP_AUTH;
				if (MAIL_SMTP_USE_SSL && $mailer->SMTPAuth) {
					$mailer->SMTPSecure = "ssl";
				}
				$mailer->Host 	= MAIL_SMTP_SERVER;
				$mailer->Username = MAIL_SMTP_USER;
				$mailer->Password = MAIL_SMTP_PASSWORD;
				$mailer->Port     = MAIL_SMTP_PORT;
			// }
			// else {
			// 	$mailer->From = $from->mail_from;
			// 	$mailer->FromName = $from->mail_fromname;
			// 	$mailer->SMTPAuth = ($from->mail_smtp_auth == 1);
			// 	if (($from->mail_smtp_use_ssl == 1) && $mailer->SMTPAuth) {
			// 		$mailer->SMTPSecure = "ssl";
			// 	}
			// 	$mailer->Host 	= $from->mail_smtp_server;
			// 	$mailer->Username = $from->mail_smtp_user;
			// 	$mailer->Password = $from->mail_smtp_password;
			// 	$mailer->Port     = $from->mail_smtp_port;
			// }

			$mailer->IsSMTP();	
			$mailer->Subject = $title;
			$mailer->Body = $body;

			if (_is_empty($to_address) && _is_empty($to_name)) {
				if ($from != null && !_is_empty($from->mail_from)) {
					$to_address = $from->mail_from;
					$to_name = $from->mail_fromname;
				}
				else {
					$to_address = MAIL_FROM;
					$to_name = MAIL_FROMNAME;
				}

				$mailer->AddAddress($to_address, $to_name);
			}
			else {
				$mailer->AddAddress($to_address, $to_name);

				// confirm email
				// if ($from != null && !_is_empty($from->mail_from))
				// 	$mailer->AddBCC($from->mail_from, $from->mail_fromname);
				// else
				// 	$mailer->AddBCC(MAIL_FROM, MAIL_FROMNAME);
			}

			if ($file_path != null) {
				$mailer->AddAttachment($file_path, $file_name);
			}
			
			$ret = $mailer->Send();

			_opr_log("sended mail from: " . $mailer->From . " to: " . $to_address . "(" . $to_name . ") result:" . $ret);

			return $ret;
		}
		return false;
	}

	// send sms message
	function _sms_string($mobile, $ll)
	{
		if (is_array($ll)) {
			$lang = null;
			if (strstr($mobile, "+86"))
				$lang = array("lang" => "zh-CN");
			else 
				$lang = array("lang" => "en-US");
			if (count($ll) > 0)
				$ll[count($ll)] = $lang;
			$str = call_user_func_array("_l", $ll);
			$message = str_replace("\n", " ", $str);

			return $message;	
		}
		else 
			return $ll;
	}

	function _push_message($user, $ll_title, $ll_content)
	{
		if ($user == null)
			return ERR_OK;

		if (is_numeric($user) || is_string($user))
		{
			$user = userModel::get_model($user);
		}
		$err = messageModel::push($user, $ll_title, $ll_content);
		return ERR_OK;
	}

    function _send_sms($mobile, $ll)
    {
        $ret = false;
        if (SMS_ENABLE == ENABLED) {
        	$msg = "【" . _sms_string($mobile, _ll("MARKCUS远程医疗")) . "】";
        	$msg .= _sms_string($mobile, $ll);
            $ret = smsManager::sendSMS($mobile, $msg);
        }

        if ($ret)
            return ERR_OK;

        return ERR_FAIL_SMS;
    }

	//---------------------
	// 14. Language Related
	//---------------------
	// set/get current language
	function _lang($lang = null) {
		if ($lang == null)
		{
			$lang = _session("LANGUAGE");
			$lang = $lang == null ? DEFAULT_LANGUAGE : $lang;
			return $lang;
		}
		else {
			_session("LANGUAGE", $lang);
			$language_name = languageModel::get_language_name($lang);
			_session("LANGUAGE_NAME", $language_name);
		}
	}

	function _lang_name() {
		$language_name = _session("LANGUAGE_NAME");
		if (_is_empty($language_name)) {
			_lang(DEFAULT_LANGUAGE);
			$language_name = _session("LANGUAGE_NAME");
		}
		return $language_name;
	}

	function _load_lang($lang) {
		global $g_string;
		if (!is_array($g_string))
			$g_string = array();

		if (!isset($g_string[$lang]))
			@include_once("resource/lang/" . $lang . ".php");
	}

	// from current language(_lang())
	function _l($str, $p1=null) { 
		// variable arguments
		// for example: _l("hello, %s! my name is %s", "world", "quan");
		// last argument is options
		// for example: _l("hello, %s! my name is %s", "world", "quan", array("lang" => 'en-US'));
		global $g_string;
		$args = func_get_args();
		$argc = func_num_args();
		if ($argc > 1 && is_array($args[$argc - 1])) {
			$options = $args[$argc - 1];
			if (isset($options["lang"]))
			{
				$lang = $options["lang"];
				_load_lang($lang);
			}
			else
				$lang = _lang();
		}
		else
			$lang = _lang();
		$lstr = (isset($g_string[$lang]) && isset($g_string[$lang][$str])) ? $g_string[$lang][$str] : null;
		$lstr = ($lstr == null ? $str : $lstr);
		

		// localize date time
		for ($i = 1; $i < $argc; $i ++)
		{
			$d = $args[$i];
			if (is_array($d) && isset($d[0])) {
				if ($d[0] == "date") {
					$args[$i] = _l_date($d[1], $lang);
				}
				else if ($d[0] == "time") {
					$args[$i] = _l_time($d[1], $lang);
				}
			}

			if (!is_array($args[$i])) {
				$lstr = str_replace("{" . ($i-1) . "}", $args[$i], $lstr);
			}
		}
		$args[0] = $lstr;
		return call_user_func_array("sprintf", $args);
	}

	// this function is used for generating language file
	function _ll() {
		return func_get_args();;
	}

	function _ll_date($d) {
		return array("date", $d);
	}

	function _ll_time($d) {
		return array("time", $d);
	}

	function l($str, $p1=null) {
		$args = func_get_args();
		$str = call_user_func_array("_l", $args);
		print $str;
	}

	function _l_model($model, $prop, $prop_lang=null)
	{
		$v = (is_array($model)? $model[$prop] : $model->$prop);
		if ($prop_lang == null)
			$prop_lang = $prop . "_l";
		$lang = _lang();
		//if ($lang != DEFAULT_LANGUAGE) {
			$v2 = _l_model_2($model, $prop_lang);
			if ($v2 != null)
				$v = $v2;
		//}

		return $v;
	}

	function _l_model_2($model, $prop)
	{
		$lang = _lang();
		$v_l = (is_array($model)? $model[$prop] : $model->$prop);
		if (is_string($v_l)) {
			$v_l = json_decode($v_l, true);
		}
		if (is_array($v_l) && isset($v_l[$lang])) {
			return $v_l[$lang];
		}

		return null;
	}
	//---------------------
	// 15. Batch service related
	//---------------------
	function _save_batch_ini() {
		if (_server_os() == 'LINUX') {
			_fwrite(SITE_ROOT . "resource/service/redhat/batch.ini", SITE_ROOT);
			_fwrite(SITE_ROOT . "resource/service/ubuntu/batch.ini", SITE_ROOT);
		}
	}

	//---------------------
	// 16. Other
	//---------------------
	function _rand_hash($seed=null, $len=18) {
		$s = RANDOM_SEED;
		if ($seed)
			$s .= $seed;
		return date('YmdHis') . substr(strtolower(md5(microtime() . $s . rand())), 0, $len);
	}

	function _rand_id()
	{
		list($usec, $sec) = explode(' ', microtime());
		srand((float) $sec + ((float) $usec * 100000));
		return date('YmdHi') . str_pad(rand(0, 999), 3, "0", STR_PAD_LEFT);
	}

	function _erase_old($dir) {
		$files = scandir($dir);
		if (count($files) == 0)
			return;

		$now = time();
		foreach ($files as $file)
		{
			if ($file == '.' || $file == '..')
				continue;
			$tm = filectime($dir . $file);
			if ($now - $tm > 3600 * 3) // before 3 hour 
			{
				@unlink($dir . $file);
				if (is_dir($dir . $file)) {
					_rmdir($dir . $file);
				}
			}
		}
	}

	function _ip()
	{
		global $_SERVER;
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} 
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} 
		elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		else {
			$ip = "local";
		}
		return $ip;
	}

	function _country_id_from_ip($ip) {
		$json = file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip);
		//_debug_log("http://www.geoplugin.net/json.gp?ip=" . $ip);
		$details = json_decode($json, true);
		//_debug_log("json_decode" . $details['geoplugin_countryCode']);
		if ($details !== NULL && isset($details['geoplugin_countryCode']))
			return $details['geoplugin_countryCode'];
		else
			return "CN";
	}

	function _in_blacklist()
	{
		$ip = _ip();
		if ($ip == "::1")
			return false;

		$ip = preg_split("/\./", $ip);
		if (count($ip) != 4)
			return false;


		if (defined("BLACKLIST") && BLACKLIST != "")
		{
			$ll = @preg_split("/;/", BLACKLIST);
			foreach ($ll as $l)
			{
				$bl = preg_split("/,/", $l);
				$addr = preg_split("/\./", $bl[1]);
				$mask = preg_split("/\./", $bl[2]);

				$check = true;
				for($i = 0; $i < 4; $i ++)
				{
					$ii = ($ip[$i] + 0) & ($mask[$i] + 0);
					if ($ii != $addr[$i])
						$check = false;
				}
				if ($check)
					return true;
			}
		}

		return false;
	}

	function _ifnull($val, $default)
	{
		return $val == null ? $default : $val;
	}

	function _path2id($path)
	{
		$ps = preg_split("/\//", $path);
		if ($ps == null)
			return $path;

		return $ps[count($ps) - 1] + 0;
	}

	function _path2parent_ids($path)
	{
		$ps = preg_split("/\//", $path);
		if ($ps == null || count($ps) == 1)
			return array();

		$parent_ids = array();
		for ($i = 0; $i < count($ps) - 1; $i ++)
		{
			array_push($parent_ids, $ps[$i] + 0);
		}
		return $parent_ids;
	}

	function _rating($sum, $count)
	{
		if ($count > 0)
			return floor(($sum / $count) * 100) / 100;
		else
			return 0;
	}
	
	// for sorting tree
	function _parent_sort($sort)
	{
		$sorts = preg_split("/\//", $sort);

		if ($sorts != null) 
		{
			array_pop($sorts);
			return join("/", $sorts);
		}
		else
			return "";
	}

	function _make_sort($parent_sort, $sort_no, $pad_size = PAD_SIZE)
	{
		$sort = str_pad($sort_no, $pad_size, "0", STR_PAD_LEFT);

		if ($parent_sort != "")
			$sort = $parent_sort . "/" . $sort;

		return $sort;
	}

	// class utility
	function get_public_methods($className) {
		/* Init the return array */
		$returnArray = array();

		/* Iterate through each method in the class */
		foreach (get_class_methods($className) as $method) {

			/* Get a reflection object for the class method */
			$reflect = new ReflectionMethod($className, $method);

			/* For private, use isPrivate().  For protected, use isProtected() */
			/* See the Reflection API documentation for more definitions */
			if($reflect->isPublic()) {
				/* The method is one we're looking for, push it onto the return array */
				array_push($returnArray,$method);
			}
		}
		/* return the array to the caller */
		return $returnArray;
	}

	function get_this_class_methods($class){
		$array1 = get_public_methods($class);
		if($parent_class = get_parent_class($class)){
			$array2 = get_public_methods($parent_class);
			$array3 = array_diff($array1, $array2);
		}else{
			$array3 = $array1;
		}
		return($array3);
	}

	// json related
	function _json_encode($a=false)
	{
	    if (is_null($a)) return 'null';
	    if ($a === false) return 'false';
	    if ($a === true) return 'true';
	    if (is_scalar($a))
	    {
			if (is_float($a))
			{
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if (is_numeric($a))
			{
				$b = $a + 0;
				if (($a . "") === ($b . ""))
					return $a;
			}

			if (is_string($a))
			{
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "", "", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '', '', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}
			else
				return $a;
	    }

	    $isList = true;
	    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
	    {
	    	if (key($a) !== $i)
	    	{
	        	$isList = false;
	        	break;
	      	}
	    }

	    $result = array();
	    if ($isList)
	    {
	    	foreach ($a as $v) $result[] = _json_encode($v);
			return '[' . join(',', $result) . ']';
	    }
	    else
	    {
	      	foreach ($a as $k => $v) {
	      		if (is_string($k)) {
	      			if ($k[0] == "'" && $k[strlen($k)-1] == "'")
	      			{
	      				$k = substr($k, 1, strlen($k) - 2);
	      			}
	      		}
	      		$result[] = _json_encode($k).':'._json_encode($v);
	      	}
	      	return '{' . join(',', $result) . '}';
	    }
	}

	// zip related
	function _create_zip($dir, $files = array(), $destination = '', $overwrite = false) {
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { return false; }
		//vars
		$valid_files = array();
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($dir. $file)) {
					$valid_files[] = $file;
				}
			}
		}
		//if we have good files...
		if(count($valid_files)) {
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			//add the files
			foreach($valid_files as $file) {
				$zip->addFile($dir. $file, $file);
			}

			//close the zip -- done!
			$zip->close();
			
			//check to make sure the file exists
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}

	function _rotate($point, $angle, $width) {
		$point[0] -= $width / 2;
		$point[1] -= $width / 2;
		$new_point = array(0, 0);
		$angle = deg2rad($angle);

		$new_point[0] = $point[0] * cos($angle) - $point[1] * sin($angle);
		$new_point[1] = $point[0] * sin($angle) + $point[1] * cos($angle);
		$new_point[0] += $width / 2;
		$new_point[1] += $width / 2;
		return $new_point;
	}

	function clamp($val) {
	    return min(1, max(0, $val));
	}

    function hue($h, $m1, $m2) {
        $h = $h < 0 ? $h + 1 : ($h > 1 ? $h - 1 : $h);
        if      ($h * 6 < 1) return $m1 + ($m2 - $m1) * $h * 6;
        else if ($h * 2 < 1) return $m2;
        else if ($h * 3 < 2) return $m1 + ($m2 - $m1) * (2/3 - $h) * 6;
        else                return $m1;
    }

	function hsla($h, $s, $l, $a) {
        $h = (($h) % 360) / 360;
        $s = $s;
        $l = $l;
        $a = $a;

        $m2 = $l <= 0.5 ? $l * ($s + 1) : $l + $s - $l * $s;
        $m1 = $l * 2 - $m2;
        return array((integer)(hue($h + 1/3, $m1, $m2) * 255), (integer)(hue($h, $m1, $m2) * 255), (integer)(hue($h - 1/3, $m1, $m2) * 255), (integer)($a));
    }

	function _toHSL($color) {
        $r = $color[0] / 255;
        $g = $color[1] / 255;
        $b = $color[2] / 255;
        $a = $color[3];

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = $s = $l = ($max + $min) / 2;
        $d = $max - $min;

        if ($max === $min) {
            $h = $s = 0;
        } else {
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r: $h = ($g - $b) / $d + ($g < $b ? 6 : 0); break;
                case $g: $h = ($b - $r) / $d + 2;               break;
                case $b: $h = ($r - $g) / $d + 4;               break;
            }
            $h /= 6;
        }
        $HSL['h'] = $h * 360;
	    $HSL['s'] = $s;
	    $HSL['l'] = $l;
	    $HSL['a'] = $a;
        return $HSL;
    }

	function lighten($color, $amount) {
		$hsl = _toHSL($color);
        $hsl['l'] += $amount / 100;
        $hsl['l'] = clamp($hsl['l']);
        return hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
	}

	function darken($color, $amount) {
		$hsl = _toHSL($color);
        $hsl['l'] -= $amount / 100;
        $hsl['l'] = clamp($hsl['l']);
        return hsla($hsl['h'], $hsl['s'], $hsl['l'], $hsl['a']);
	}

	function my_sort($val1, $val2) {
		return $val1[1] < $val2[1];
	}

	function _create_avartar($id) {
		$hash_id = md5($id);
		$hash_strings = str_split($hash_id, 4);
		$cw_values = array();
		for ($i=0; $i < count($hash_strings); $i++) {
			$str = $hash_strings[$i];
			$cw_value = str_split($str, 2);
			array_push($cw_values, $cw_value);
		}
		usort($cw_values, "my_sort");
		$pattle_red = array(array(0xF4, 0x43, 0x36, 0), array(0xE9, 0x1E, 0x63, 0), array(0x9C, 0x27, 0xB0, 0));
		$pattle_blue = array(array(0x67, 0x3A, 0xB7, 0), array(0x3F, 0x51, 0xB5, 0), array(0x21, 0x96, 0xF3, 0));
		$pattle_lightblue = array(array(0x03, 0xA9, 0xF3, 0), array(0x00, 0xBC, 0xD4, 0), array(0x00, 0x96, 0x88, 0));
		$pattle_green = array(array(0x4C, 0xAF, 0x50, 0), array(0x8B, 0xC3, 0x4A, 0), array(0xCD, 0xDC, 0x39, 0));
		$pattle_yellow = array(array(0xFF, 0xEB, 0x3B, 0), array(0xFF, 0xC1, 0x07, 0), array(0xFF, 0x98, 0x00, 0));
		$pattle_deeporange = array(array(0xFF, 0x57, 0x22, 0), array(0x79, 0x55, 0x48, 0), array(0x9E, 0x9E, 0x9E, 0));

		$color_group = array($pattle_red, $pattle_blue, $pattle_lightblue, $pattle_green, $pattle_yellow, $pattle_deeporange);

		$max_width = hexdec($cw_values[0][1]);
		$group_ids = array(hexdec($cw_values[0][0]) % 6, ( hexdec($cw_values[0][0]) + 3 ) % 6);

		$path = AVARTAR_PATH.$id.".svg";
		$myfile = fopen($path, "w");
		if ($myfile) {
			$txt = "<svg width='100' height='100' viewBox='-50 -50 100 100' xmlns='http://www.w3.org/2000/svg'>";
			for ($i=0; $i < count($cw_values); $i+=2) {
				$cw_value = $cw_values[$i];
				$c_value = hexdec($cw_value[0]);
				$w_value = hexdec($cw_value[1]);

				if (($w_value / $max_width) < 0.1)
					$w_value = 10;
				else
					$w_value = (integer)(($w_value / $max_width)*100);

				if (($i/2)%2 == 0)
					$color = darken($color_group[$group_ids[($i/2)%2]][($c_value / 16)%3], ($c_value / 16)*1.5);
				else
					$color = lighten($color_group[$group_ids[($i/2)%2]][($c_value / 16)%3], ($c_value / 16)*1.5);
				$txt .= "<rect x='-".($w_value/2)."' y='-50' width='".$w_value."' height='100' style='fill: rgba(".$color[0].",".$color[1].",".$color[2].", 0.5)' transform='rotate(45) scale(1)'> </rect>";
				$txt .= "<rect x='-".($w_value/2)."' y='-50' width='".$w_value."' height='100' style='fill: rgba(".$color[0].",".$color[1].",".$color[2].", 0.5)' transform='rotate(-45) scale(1)'> </rect>";
			}
			$txt .= "</svg>";
			fwrite($myfile, $txt);
			fclose($myfile);

			return $path;
		}

		return null;
	}

	// for checkbox
	function _arr2bits($arr)
	{
		if (is_array($arr)) {
			$val = 0;
			foreach($arr as $v) {
				if ($v != -1)
					$val |= $v;
			}

			return $val;
		}
		else
			return $arr;
	}

	function _bits2arr($bits)
	{
		$arr = array();
		for ($i = 0; $i < 32; $i ++)
		{
			if ($bits & (1 << $i))
				$arr[] = 1 << $i;
		}

		return $arr;
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/