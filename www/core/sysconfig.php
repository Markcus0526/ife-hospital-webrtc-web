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
				"auto_language_from_ip",

				"time_zone",

				/* frontend setting */
				"frontend_app",

				/* db related */
				"db_hostname",
				"db_user",
				"db_password",
				"db_name",
				"db_port",

				/* frontend chat server related */
				"frontend_url",

				/* signaling chat server related */
				"scs_url",
				"ice_servers",

				/* sms related */
				"sms_enable",
				"sms_apikey",
				"sms_limittime",

				/* recording api */
				"recording_api",

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

				/* 安全策略 */
				"password_min_length",
				"password_strength",
				"login_fail_lock",
				"login_fail_disable",
				"random_seed",
				"dist_inc_count", // count of distributed servers
				"dist_inc_no", // no of the distributed server (0 ~ dist_inc_count - 1)

				"default_language",

				/* other */
				"admin_name", 
				"admin_password", 
				"admin_mobile", 
				"install_sample",

				/* 翻译费用(%) */
				"interpreter_fee",

				/* 页面刷新 */
				"refresh_interval",
				/* 会诊支付期限(分钟) */
				"interview_pay_limit",
				/* 会诊前提醒(分钟) */
				"interview_alarm_start",
				/* 患者能同意更改时间的期限(分钟) */
				"interview_accept_time_limit",
				/* 专家没在会诊开始*分钟内上线,自动结束会诊 */
				"interview_offline_limit",
				/* 专家离线后没在*分钟内上线,自动结束会诊 */
				"interview_reoffline_limit",
				/* 专家在会诊开始*分钟内仍没上线,温馨提示 */
				"interview_alarm_offline_limit",
				/* 翻译无违约金取消期限(时间) */
				"interview_i_cancel_limit",
				/* 会诊前*小时仍没有翻译接单 */
				"interview_no_interp_alarm_limit",
				/* 违约金 */
				"penalty_amount",
				/* 患者可以预约距当前时间*小时后的会诊时间 */
				"interview_patient_reservable_time_after_now",
				/* 专家可以更改至距当前时间*小时后的会诊时间 */
				"interview_doctor_changable_time_after_now",
				/* 专家可以设置距当前时间*小时后的会诊时间 */
				"interview_doctor_settable_time_after_now",

				/* 在线支付设置 */
				"debug_pay",
				/* 银联支付*/
				"chinapay_merchantid",
				"chinapay_signcert_pwd",
				/* PayPal支付*/
				"paypal_email_address",
				"paypal_identity_token",
				"paypal_cert_id",
				"paypal_ewp_private_key_pwd",
				"paypal_sandbox_client_id",
				"paypal_sandbox_client_secret",
				"paypal_client_id",
				"paypal_client_secret",

				/* 汇率查询API */
				"exrate_api_url",
				"exrate_api_key",
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
				"auto_language_from_ip" => true,

				"time_zone" => "GMT+800", // Asia/Shanghai

				"frontend_app" => "application",

				"db_hostname" => "localhost",
				"db_user" => "root",
				"db_name" => "teleclinic",
				"db_port" => 3306,

                "frontend_url" => "https://192.168.12.128",

				"scs_url" => "wss://192.168.12.128:18888",
				"ice_servers" => '[{url:"stun:stun.l.google.com:19302"}]',

				"recording_api" => "https://192.168.12.128/recording/",

				"sms_enable" => 1,
				"sms_apikey" => "",
				"sms_limittime" => 20,

				"mail_enable" => 1,
				"mail_fromname" => "3QC全球远程医疗会诊系统",
				"mail_smtp_server" => "mail",
				"mail_smtp_auth" => true,
				"mail_smtp_use_ssl" => true,
				"mail_smtp_port" => 25,

				"password_min_length" => "8",
				"password_strength" => "1",
				"login_fail_lock" => "3",
				"login_fail_disable" => "3",
				"random_seed" => "teleclinic",
				"dist_inc_count" => 2,
				"dist_inc_no" => 0,

				"default_language" => "zh-CN",

				"admin_name" => "超级管理员",
				"admin_mobile" => "0005",
				"install_sample" => true,

				"interpreter_fee" => 0,

				"refresh_interval" => 15,
				"interview_pay_limit" => 30,
				"interview_alarm_start" => 30,
				"interview_accept_time_limit" => 30,
				"interview_offline_limit" => 15,
				"interview_reoffline_limit" => 5,
				"interview_alarm_offline_limit" => 5,
				"interview_i_cancel_limit" => 24,
				"interview_no_interp_alarm_limit" => 24,
				"penalty_amount" => 100,
				"interview_patient_reservable_time_after_now" => 24,
				"interview_doctor_changable_time_after_now" => 24,
				"interview_doctor_settable_time_after_now" => 24,

				"debug_pay" => 1,
				"chinapay_merchantid" => "",
				"chinapay_signcert_pwd" => "",
				"paypal_email_address" => "",
				"paypal_identity_token" => "",
				"paypal_cert_id" => "",				
				"paypal_ewp_private_key_pwd" => "",
				"paypal_sandbox_client_id" => "",
				"paypal_sandbox_client_secret" => "",
				"paypal_client_id" => "",
				"paypal_client_secret" => "",

				"exrate_api_url" => "http://op.juhe.cn/onebox/exchange/query?key=",
				"exrate_api_key" => "",
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
			$config .= $this->define_bool("auto_language_from_ip");
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

            $config .= "/* Frontend server related */;\n";
            $config .= $this->define_string("frontend_url");

			$config .= "/* SCS(signaling chat server) related */;\n";
			$config .= $this->define_string("scs_url");
			$config .= $this->define_string("ice_servers");
			$config .= "\n";
			
            $config .= "/* Recording server related */;\n";
			$config .= $this->define_string("recording_api");
			$config .= "\n";

			$config .= "/* SMS related */;\n";
			$config .= $this->define_bool("sms_enable");
			$config .= $this->define_string("sms_apikey");
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
	
			$config .= "/* 安全策略 */\n";
			$config .= $this->define_number("password_min_length");
			$config .= $this->define_number("password_strength");
			$config .= $this->define_number("login_fail_lock");
			$config .= $this->define_number("login_fail_disable");
			$config .= $this->define_string("random_seed");
			$config .= $this->define_number("dist_inc_count");
			$config .= $this->define_number("dist_inc_no");
			$config .= "\n";

			$config .= "/* Language */\n";
			$config .= $this->define_string("default_language");

			$config .= "/* 翻译费用 */\n";
			$config .= $this->define_number("interpreter_fee");
			
			$config .= "/* 页面刷新 */\n";
			$config .= $this->define_number("refresh_interval");
			$config .= "/* 会诊支付期限(分钟) */\n";
			$config .= $this->define_number("interview_pay_limit");
			$config .= "/* 会诊前提醒(分钟) */\n";
			$config .= $this->define_number("interview_alarm_start");
			$config .= "/* 患者能同意更改时间的期限(分钟) */\n";
			$config .= $this->define_number("interview_accept_time_limit");
			$config .= "/* 专家没在会诊开始*分钟内上线,自动结束会诊 */\n";
			$config .= $this->define_number("interview_offline_limit");
			$config .= "/* 专家离线后没在*分钟内上线,自动结束会诊 */\n";
			$config .= $this->define_number("interview_reoffline_limit");
			$config .= "/* 专家在会诊开始*分钟内仍没上线,温馨提示 */\n";
			$config .= $this->define_number("interview_alarm_offline_limit");
			$config .= "/* 翻译无违约金取消期限(时间) */\n";
			$config .= $this->define_number("interview_i_cancel_limit");
			$config .= "/* 会诊前*小时仍没有翻译接单 */\n";
			$config .= $this->define_number("interview_no_interp_alarm_limit");
			$config .= "/* 违约金 */\n";
			$config .= $this->define_number("penalty_amount");

			$config .= "/* 患者可以预约距当前时间*小时后的会诊时间 */\n";
			$config .= $this->define_number("interview_patient_reservable_time_after_now");
			$config .= "/* 专家可以更改至距当前时间*小时后的会诊时间 */\n";
			$config .= $this->define_number("interview_doctor_changable_time_after_now");
			$config .= "/* 专家可以设置距当前时间*小时后的会诊时间 */\n";
			$config .= $this->define_number("interview_doctor_settable_time_after_now");

			$config .= "\n";

			$config .= "/* 在线支付设置 */\n";
			$config .= $this->define_number("debug_pay", "0:使用生产环境证书 1:使用测试环境证书 2:无使用在线支付，经常支付成功");
			$config .= "/* 银联在线支付设置 */\n";
			$config .= $this->define_string("chinapay_merchantid");
			$config .= $this->define_string("chinapay_signcert_pwd");
			$config .= "/* PayPal在线支付设置 */\n";
			$config .= $this->define_string("paypal_email_address");
			$config .= $this->define_string("paypal_identity_token");
			$config .= $this->define_string("paypal_cert_id");
			$config .= $this->define_string("paypal_ewp_private_key_pwd");
			$config .= $this->define_string("paypal_sandbox_client_id");
			$config .= $this->define_string("paypal_sandbox_client_secret");
			$config .= $this->define_string("paypal_client_id");
			$config .= $this->define_string("paypal_client_secret");
			$config .= "/* 汇率查询API */\n";
			$config .= $this->define_string("exrate_api_url");
			$config .= $this->define_string("exrate_api_key");

			fwrite($fp, $config);

			fclose($fp);

			_save_batch_ini();

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
			return "define('" . strtoupper($prop) . "',		'" . str_replace("'", "\'", $this->$prop) . "');" . $comment. "\n";
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