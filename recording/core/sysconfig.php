<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/
	
	define('MIN_PHP_VER', '5.4.7');

	class sysconfig
	{
		private $_props;
		private $conn;
		private $sql_result;

		private $_viewHelper;

		public function __construct()
		{
			$this->init_props(array(
				"version",

				"debug_mode",
				"enable_ssl",

				"time_zone",

				/* frontend setting */
				"frontend_app",

				/* db related */
				"db_hostname",
				"db_user",
				"db_password",
				"db_name",
				"db_port",

				/* sms related */
				"sms_enable",
				"sms_user",
				"sms_password",
				"sms_limittime",

				/* e-mail related */
				"mail_enable",
				"mail_from",
				"mail_fromname",
				"mail_smtp_auth",
				"mail_smtp_use_ssl",
				"mail_smtp_server",
				"mail_smtp_user",
				"mail_smtp_password",
				"mail_smtp_port",

				/* security */
				"password_min_length",
				"login_fail_lock",
				"random_seed",
				"dist_inc_count", // count of distributed servers
				"dist_inc_no", // no of the distributed server (0 ~ dist_inc_count - 1)

				"default_language",

				/* other */
				"admin_name", 
				"admin_password", 
				"admin_mobile", 
				"install_sample",
				"record_remain",

				"ffmpeg",
				"ffprobe"
				));


			$this->_viewHelper = new viewHelper($this);
			$this->init();
		}

		public function init() {
			$os = _server_os();

			$option = array(
				"version" => "1.0",

				"debug_mode" => false,
				"enable_ssl" => true,

				"time_zone" => "GMT+800", // Asia/Shanghai

				"frontend_app" => "application",

				"db_hostname" => "localhost",
				"db_user" => "root",
				"db_name" => "teleclinic",
				"db_port" => 3306,

				"sms_enable" => 1,
				"sms_user" => "52963",
				"sms_password" => "6f4fio",
				"sms_limittime" => 20,

				"mail_enable" => 1,
				"mail_fromname" => "3QC全球远程医疗会诊系统",
				"mail_smtp_server" => "mail",
				"mail_smtp_auth" => true,
				"mail_smtp_use_ssl" => false,
				"mail_smtp_port" => 25,

				"password_min_length" => "5",
				"login_fail_lock" => "5",
				"random_seed" => "teleclinic",
				"dist_inc_count" => 2,
				"dist_inc_no" => 0,

				"default_language" => "zh-CN",

				"admin_name" => "超级管理员",
				"admin_mobile" => "0005",
				"install_sample" => true,
				"record_remain" => false,

				"interpreter_fee" => 0,

				"refresh_interval" => 15,
				"interview_pay_limit" => 30,
				"interview_alarm_start" => 30,
				"interview_accept_time_limit" => 30,
				"interview_offline_limit" => 15,
				"interview_alarm_offline_limit" => 5,
				"interview_p_accept_ctime_limit" => 30,
				"interview_i_accept_ctime_limit" => 60,
				"interview_i_cancel_limit" => 24,
				"penalty_amount" => 100,

				"ffmpeg" => "ffmpeg",
				"ffprobe" => "ffprobe"
			);

			foreach($this->_props as $prop_name => $val) {
				$this->init_prop($prop_name, $option[$prop_name]);
			}
		}

		public function save() 
		{
			$path = SITE_ROOT . "/config.inc";

			$fp = fopen($path, "w+");
			if (!$fp)
				return ERR_FAILOPENFILE;

			$config = "<?php\n";
			$config .= $this->define_string("version");
			$config .= "\n";
			$config .= $this->define_bool("debug_mode");
			$config .= $this->define_bool("enable_ssl");
			$config .= "\n";
			$config .= "/* System Time Zone */\n";
			$config .= $this->define_string("time_zone");
			$config .= "\n";

			$config .= "/* Frontend setting */\n";
			$config .= $this->define_string("frontend_app");
			$config .= "\n";

			$config .= "/* DB related */\n";
			$config .= $this->define_string("db_hostname");
			$config .= $this->define_string("db_user");
			$config .= $this->define_string("db_password");
			$config .= $this->define_string("db_name");
			$config .= $this->define_number("db_port");
			$config .= "\n";

			$config .= "/* SMS related */;\n";
			$config .= $this->define_bool("sms_enable");
			$config .= $this->define_string("sms_user");
			$config .= $this->define_string("sms_password");
			$config .= $this->define_number("sms_limittime");
			$config .= "\n";
			
			$config .= "/* E-mail related */\n";
			$config .= $this->define_bool("mail_enable");
			$config .= $this->define_string("mail_from");
			$config .= $this->define_string("mail_fromname");
			$config .= $this->define_bool("mail_smtp_auth");
			$config .= $this->define_bool("mail_smtp_use_ssl");
			$config .= $this->define_string("mail_smtp_server");
			$config .= $this->define_string("mail_smtp_user");
			$config .= $this->define_string("mail_smtp_password");
			$config .= $this->define_string("mail_smtp_port");
			$config .= "\n";
	
			$config .= "/* Security policy */\n";
			$config .= $this->define_number("password_min_length");
			$config .= $this->define_number("login_fail_lock");
			$config .= $this->define_string("random_seed");
			$config .= $this->define_number("dist_inc_count");
			$config .= $this->define_number("dist_inc_no");
			$config .= "\n";

			$config .= "/* Language */\n";
			$config .= $this->define_string("default_language");

			$config .= "/* FFMPEG path */\n";
			$config .= $this->define_string("ffmpeg");
			$config .= $this->define_string("ffprobe");

			$config .= "/* remain records */\n";
			$config .= $this->define_bool("record_remain");

			fwrite($fp, $config);

			fclose($fp);

			// app/scripts/config.js
			if (_server_os() == 'LINUX') {
				_save_batch_ini();
			}

			return ERR_OK;

		}

		public function init_props($arr)
		{
			foreach($arr as $item) {
				$this->$item = null;
			}
		}

		public function __get($prop) {
			if ($prop == "props")
				return $this->_props;
			else
			{
				return isset($this->_props[$prop]) ? $this->_props[$prop] : null ;
			}
		}

		public function props($prop_names) {
			$props = array();

			foreach ($prop_names as $prop_name) {
				if (isset($this->_props[$prop_name]))
					$props[$prop_name] = $this->_props[$prop_name];
			}

			return $props;
		}

		public function __set($prop, $val) {
			if ($prop == "props") {
				if (is_array($val))
					$this->_props = $val;
			}
			else {
				$this->_props[$prop] = $val;
			}
		}

		public function __call($method, $params) {
			if (method_exists($this->_viewHelper, $method)) {
				call_user_func_array(array($this->_viewHelper, $method), $params);
			}
		}

		private function init_prop($prop, $init_val = null) {
			$const_name = strtoupper($prop);

			if (defined($const_name)) 
				$this->$prop = constant($const_name);
			else 
				$this->$prop = $init_val;

		}

		public function load($load_object, $ignores = array())
		{
			if (is_array($load_object))
				$load = (object) $load_object;
			else
				$load = $load_object;

			$_exist_prop = method_exists($load, "exist_prop");
			foreach ($this->_props as $field_name => $val)
			{
				if ($this->name_prefix)
					$l_field_name = $this->name_prefix . $field_name;
				else
					$l_field_name = $field_name;
				
				if (!in_array($field_name, $ignores)) {
					$exists = property_exists($load, $l_field_name) || 
						$_exist_prop && $load->exist_prop($l_field_name);
					if ($exists) {
						$this->$field_name = $load->$l_field_name;
					}
					else {
						// for single checkbox
						$l_field_name .= "_@@@";
						$exists = property_exists($load, $l_field_name) || 
							$_exist_prop && $load->exist_prop($l_field_name);
						if ($exists) {
							$this->$field_name = _arr2bits($load->$l_field_name);
						}
					}
				}
			}
		}

		public function define_string($prop, $comment = "")
		{
			if ($comment != "")
				$comment = "// " . $comment;
			return "define('" . strtoupper($prop) . "',		'" . $this->$prop . "');" . $comment. "\n";
		}

		public function define_number($prop, $comment = "")
		{
			if ($comment != "")
				$comment = "// " . $comment;
			$val = ($this->$prop == null) ? 0 : $this->$prop;
			return "define('" . strtoupper($prop) . "',		" . $val . ");" . $comment. "\n";
		}

		public function define_bool($prop, $comment = "")
		{
			if ($comment != "")
				$comment = "// " . $comment;
			return "define('" . strtoupper($prop) . "',		" . ($this->$prop == ENABLED ? "1" : "0") . ");" . $comment. "\n";
		}

		public function connect($create_db=false) {
			$conn = @mysqli_connect ($this->db_hostname, $this->db_user, $this->db_password, $this->db_name, $this->db_port);
			if (!$conn) {
				return ERR_NODB;
			}

			$this->conn = $conn;

			/*
			if ($create_db) {
				$this->query("DROP DATABASE IF EXISTS " . $this->db_name . ";");
				$this->query("CREATE DATABASE " . $this->db_name . " CHARSET=utf8 COLLATE=utf8_general_ci;");
			}
			*/

			$this->query("SET NAMES utf8;");

			@mysqli_select_db ($this->conn, $this->db_name);

			return ERR_OK;
		}

		public function query($sql) {
			$this->sql_result = mysqli_query($this->conn, $sql);
	 		return  $this->sql_result ? ERR_OK : ERR_SQL;
		}

		public function query_file($sqlpath) {
			$sqlfile = fopen($sqlpath, "r");
			$sql = fread($sqlfile, filesize($sqlpath));
			fclose($sqlfile);

			$sql = preg_replace('/\/\*([^*]+)\*\//', '', $sql);
			$sql = preg_replace('/\-\-([^\n]+)\n/', '', $sql);

			$sqls = preg_split('/;\r/', $sql);

			foreach($sqls as $sql) {
				$this->query($sql);
			}
		}

		public function parse_blacklist() {
			$blacklists = array();
			if ($this->blacklist != "")
			{
				$ll = @preg_split("/;/", $this->blacklist);
				foreach ($ll as $l)
				{
					$blacklists[] = preg_split("/,/", $l);
				}
			}
			$this->blacklists = $blacklists;
		}

		public function check_envir() {
			$min_php_ver = preg_split('/\./', MIN_PHP_VER);
			$php_ver = preg_split('/\./', phpversion());

			$this->require_php_ver = false;
			for ($i = 0; $i < 3; $i ++) 
			{
				if ($min_php_ver[$i] < $php_ver[$i]) {
					$this->require_php_ver = true;
					break;
				}
				else if ($min_php_ver[$i] > $php_ver[$i]) {
					$this->require_php_ver = false;
					break;
				}
				else {
					$this->require_php_ver = true;
				}
			}

			$this->installed_mysql = extension_loaded('mysql');
			$this->installed_mbstring = extension_loaded('mbstring');
			$this->installed_simplexml = extension_loaded('SimpleXML');
			$this->installed_gd = extension_loaded('gd');
		}
		
		function escape($txt) {
			return mysqli_escape_string($this->conn, $txt);
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/