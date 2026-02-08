<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/
		
	_model(
		"userModel",			// model name
		"m_user",
		"user_id",
		array(
			"user_name",		// 真实姓名
			"user_name_l",		// 真实姓名(多语言)
			"user_type",		// 用户权限
			"country_id",		// 国家
			"password",			// 密码
			"sex",				// 性别
			"email",			// 电子邮箱
			"mobile",			// 手机号码
			"other_tel",		// 紧急联系方式
			"home_address",		// 家庭住址
			"balance",			// 账户余额
			"introduction",		// 简介
			"d_title",			// 专家-职称
			"d_depart",			// 专家-所属科室
			"d_fee",			// 专家-会诊费
			"d_cunit",			// 专家-会诊费货币单位
			"i_age",			// 翻译人员-译龄
			"diplomas",			// 资格证书
			"passports",		// 身份证件
			"admin_priv",		// 管理员权限
			"lock_flag",
			"lock_time",
			"access_time"),
		array("rand_id" => true,
			"operator_info" => true));

	class userModel extends model // 用户信息模型
	{		
		public function new_id($field_name)
		{
			$country_id = $this->country_id;
			if ($country_id == null)
				$country_id = 1;
			$country_id = str_pad($country_id, 2, "0", STR_PAD_LEFT);
			if ($this->user_type == UTYPE_PATIENT)
				$my_type = "01";
			else if ($this->user_type == UTYPE_DOCTOR)
				$my_type = "02";
			else if ($this->user_type == UTYPE_INTERPRETER)
				$my_type = "03";
			else if ($this->user_type == UTYPE_ADMIN)
				$my_type = "05";
			else
				$my_type = "06"; // super
			// 2017(年)09(月)01(国家:中国01)01(类型:患者01、专家02、翻译03)0001(时间顺序)
			$prefix = date('Ym') . $country_id . $my_type;

			$min = $prefix * 10000;
			$max = $prefix * 10000 + 10000;

			$db = db::get_db();
			$no = $db->scalar("SELECT MAX(".$field_name.") % 10000 + 1 FROM " . $this->table . " WHERE ".$field_name.">" . _sql($min) . " AND ".$field_name."<" . _sql($max));

			if ($no == null)
				$no = 1;

			return $prefix * 10000 + $no;
		}

		public static function get_model($pkvals, $ignore_del_flag=false)
		{
			$model = new static;
			$err = $model->get($pkvals, $ignore_del_flag);
			if ($err == ERR_OK) 
				return $model;

			if (is_string($pkvals)) {
				$err = $model->select("mobile = " . _sql($pkvals));
				if ($err == ERR_OK)
					return $model;
			}

			return null;
		}

		public function login($auto_login = false)
		{
			global $_SERVER;
			$logined = ERR_FAILLOGIN;

			if ($this->mobile != "") {
				$user = new static;
				$err = $user->select("mobile=" . _sql($this->mobile));
				if ($err == ERR_OK) {
					if ($user->lock_flag == LOCK_DISABLE)
					{
						$this->country_id = $user->country_id; // for display admin mobile
						$logined = ERR_DISABLED_LOGIN;
					}
					else if ($user->lock_flag == LOCK_ON &&
						$user->is_lock_time_before_hours()) {
						$logined = ERR_LOCKED;
					}
					else if ($this->password != '') // 密码登录
					{
						if ($user->password == _password($this->password))
							$logined = ERR_OK;
						else {
							$fails = logFailModel::inc_fail(FAIL_PASSWORD, $user->user_id);
							if ($fails >= LOGIN_FAIL_LOCK) {
								$logined = $user->update_lock_flag(FAIL_PASSWORD);
							}
							else
								$logined = ERR_INVALID_PASSWORD;
						}
					}
					else if ($this->passkey != '') { // 验证码登录
						//$passkey = logPasskeyModel::get_passkey($this->mobile);
						//if ($this->passkey == $passkey)
						$logined = ERR_OK;
						//else 
						//	$logined = ERR_INVALID_PASSKEY;
					}
				}
				else {
					$logined = ERR_UNREGISTERED_MOBILE;
				}
			}
			else if ($auto_login) {
				// auto login
				$token = _auto_login_token();
				$s = preg_split("/\//", $token);
				if (count($s) == 2) {
					$user = new static;
					$err = $user->select("mobile=" . _sql($s[0]));
					if ($err == ERR_OK && $token == $user->auto_login_token())
						$logined = ERR_OK;
				}				
			}

			if ($logined == ERR_OK)
			{
				if ($auto_login) {
					_auto_login_token($user->auto_login_token());
				}
				else {
					$user->clear_lock();
					_auto_login_token("NOAUTO");
				}

				static::init_session_data($user);

				$this->load($user);

				_access_log("登录");
			}

			return $logined;
		}

		public static function init_session_data($user)
		{
			global $_SERVER;
			$user->access_count ++;
			$user->access_time = "##NOW()";
			$user->save();

			_my_type($user->user_type);
			_my_id($user->user_id);
			_my_mobile($user->mobile);
			_my_name($user->user_name);
			_country_id($user->country_id);
			_login_ip($_SERVER["REMOTE_ADDR"]);

			privModel::init_session();

			sessionModel::insert_session();

			logAccessModel::login();
		}

		public function auto_login_token() 
		{
			return $this->mobile . "/" . _hash($this->mobile . _ip() . $this->password);
		}

		public function logout()
		{
			logAccessModel::last_access();
			_access_log("退出登录");
			_session();
			_auto_login_token("NOAUTO");
		}

		public static function get_user_name($user_id)
		{
			if ($user_id == null)
				return "";

			global $g_users;
			if ($g_users == null)
				$g_users = array();

			if (isset($g_users[$user_id]))
				return $g_users[$user_id]["user_name"];

			$user = static::get_model($user_id);
			if ($user == null)
				return "";
			else {
				$g_users[$user_id] = $user->props;
				return $user->user_name;
			}
		}

		static public function is_exist_by_mobile($mobile, $user_id=null)
		{
			$user = new static;
			$where = "mobile=" . _sql($mobile);
			if (!_is_empty($user_id))
			{
				$where .= " AND user_id!=" . _sql($user_id);
			}
			$err = $user->select($where);
			return $err == ERR_OK;
		}

		static public function send_reset_password($mobile, $user_name)
		{
			$user = new static;
			$err = $user->select("mobile=" . _sql($mobile));
			if ($err != ERR_OK || $user->user_name != $user_name)
				return ERR_NOTFOUND_USER;
			if ($user->lock_flag == LOCK_DISABLE)
				return ERR_DISABLED_RESET;

			// new password
			$chars = '0123456789';
			$len = strlen($chars);
			$txt = '';
			for ($i = 0; $i < 5; $i++) {
				$txt .= $chars[rand(0, $len - 1)];
			}

			if (_send_sms($mobile, _ll("您的新密码是%s。请及时登录修改密码。", $txt)) == ERR_OK) {
            	$user->password = _password($txt);
            	$user->lock_flag = LOCK_OFF;
            	$user->lock_time = null;
            	$err = $user->update();

            	if ($err == ERR_OK)
            	{
            		logFailModel::clear($user->user_id);
            	}
			}
			else 
				$err = ERR_FAIL_SMS;

			return $err;
		}

		public function save_attaches($upload_id) 
		{
			$attaches = $this->$upload_id;
			$attaches = preg_split("/;/", $attaches);
			$new_attaches = array();

			foreach($attaches as $attach)
			{
				$pf = preg_split("/:/", $attach);
				$path = $pf[0]; $file_name = _safe_filename($pf[1]); $ext = _ext($file_name);
				if (substr($path, 0, 3) == "tmp") {
					$dir = ATTACH_URL . date('Y/m') . "/";

					$path = substr($path, 4);
					
					$attach_url = $dir . $this->user_id . "_" . $path;
					$real_path = DATA_PATH . $attach_url;
					@unlink($real_path);
					@_mkdir(DATA_PATH . $dir);
					@rename(TMP_PATH . $path, $real_path);

					if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp" || $ext == "gif" || $ext == "tiff") {
						// create thumbnail
						$thumb_path = $real_path . "_thumb.jpg";
						copy($real_path, $thumb_path);
						_resize_image($thumb_path, $ext, "jpg", THUMB_SIZE, THUMB_SIZE, RESIZE_CONTAIN);

					}

					array_push($new_attaches, $attach_url . ":" . $file_name . ":" . $pf[2]);
				}
				else if ($path != "") {
					array_push($new_attaches, $attach);
				}
			}

			$this->$upload_id = implode(";", $new_attaches);

			return ERR_OK;
		}

		public function remove_attaches($upload_id) 
		{
			$attaches = $this->$upload_id;
			$attaches = preg_split("/;/", $attaches);

			foreach($attaches as $attach)
			{
				$pf = preg_split("/:/", $attach);
				$path = $pf[0]; $file_name= $pf[1]; $ext = _ext($file_name);
				$real_path = DATA_PATH . $path;
				@unlink($real_path);
			}

			return ERR_OK;
		}

		public static function get_mobile($user_id)
		{
			if ($user_id) {
				$user = static::get_model($user_id);
				if ($user)
					return $user->mobile;
			}
			return null;
		}

		public static function get_admin_mobiles()
		{
			$mobiles = array();
			$user = new static;
			$err = $user->select("user_type=" . _sql(UTYPE_ADMIN) . " OR user_type=" . _sql(UTYPE_SUPER));
			while ($err == ERR_OK)
			{
				$mobiles[] = $user->mobile;
				$err = $user->fetch();
			}
			return $mobiles;
		}

		public static function get_nation_admin_mobiles($country_id)
		{
			$user = new static;
			$err = $user->select("user_type=" . _sql(UTYPE_ADMIN) . " AND country_id="._sql($country_id));
			while ($err == ERR_OK)
			{
				$mobiles[] = $user->mobile;
				$err = $user->fetch();
			}
			return $mobiles;
		}

		public static function get_nation_admin_emails($user_id=null)
		{
			$emails = array();
			if ($user_id == null)
				$user_id = _my_id();
			$mobiles = array();
			$user = static::get_model($user_id);
			if ($user) {
				$country_id = $user->country_id;
				$user = new static;
				$err = $user->select("user_type=" . _sql(UTYPE_ADMIN) . " AND country_id=" . $country_id);
				while ($err == ERR_OK)
				{
					if (!_is_empty($user->email))
						$emails[] = $user->email;
					$err = $user->fetch();
				}
			}
			return $emails;
		}

		public static function message_to_nation_admin($user_id, $ll_title, $ll_content)
		{
			$mobiles = array();
			$user = static::get_model($user_id);
			if ($user) {
				$country_id = $user->country_id;
				$user = new static;
				$err = $user->select("user_type=" . _sql(UTYPE_ADMIN) . " AND country_id=" . $country_id);
				while ($err == ERR_OK)
				{
					_push_message($user, $ll_title, $ll_content);
					$err = $user->fetch();
				}
			}
		}
		
		public function avartar_id()
		{
			if ($this->user_id == null)
				return "none";
			return $this->user_id;
		}

		public function set_user_name_l($new_user_name_l)
		{
			$user_name_l = json_decode($this->user_name_l, true);
			foreach ($new_user_name_l as $lang => $str) {
				$user_name_l[$lang] = $str;
			}
			$this->user_name_l = _json_encode($user_name_l);
		}

		public function update_lock_flag($fail_type)
		{
			$err = ERR_OK;
			if ($this->lock_flag == LOCK_DISABLE)
				return ERR_DISABLED_LOGIN;

			$date = _date();
			$pwd_fails = logFailModel::get_fails(FAIL_PASSWORD, $this->user_id, $date);
			$pky_fails = logFailModel::get_fails(FAIL_PASSKEY, $this->user_id, $date);

			$lock_flag = LOCK_OFF;
			if ($pwd_fails >= LOGIN_FAIL_LOCK &&
				$pky_fails >= LOGIN_FAIL_LOCK)
			{
				for ($d = 1; $d <LOGIN_FAIL_DISABLE; $d ++)
				{
					$date = _date_add(null, -$d);
					$pwd_fails = logFailModel::get_fails(FAIL_PASSWORD, $this->user_id, $date);
					$pky_fails = logFailModel::get_fails(FAIL_PASSKEY, $this->user_id, $date);

					if (!($pwd_fails >= LOGIN_FAIL_LOCK &&
						$pky_fails >= LOGIN_FAIL_LOCK))
					{
						$lock_flag = LOCK_ON; // 连续3次登录失败
						$err = ERR_LOCKED;
					}
				}
				if ($lock_flag != LOCK_ON) {
					$lock_flag = LOCK_DISABLE; // 连续3天登录失败
					$err = ERR_DISABLED_LOGIN;
				}
			}
			else if ($fail_type == FAIL_PASSWORD && $pwd_fails >= LOGIN_FAIL_LOCK) {
				$err = ERR_LOCK_PASSWORD;
			}
			else if ($fail_type == FAIL_PASSKEY && $pky_fails >= LOGIN_FAIL_LOCK) {
				$err = ERR_LOCK_PASSKEY;
			}

			$this->lock_flag = $lock_flag;
			if ($lock_flag == LOCK_OFF)
				$this->lock_time = null;
			else
				$this->lock_time = _date_time();
			$this->save();

			return $err;
		}

		public function clear_lock()
		{
			$this->lock_flag = LOCK_OFF;
			$this->lock_time = null;
			return $this->update(array("lock_flag", "lock_time"));
		}

		public function refresh_lock_flag()
		{
			if ($this->lock_flag == LOCK_ON)
			{
				if (!$this->is_lock_time_before_hours())
					$this->lock_flag = LOCK_OFF;
			}
		}

		public function is_lock_time_before_hours($hours = 24)
		{
			if ($this->lock_time != null) {
				$lock_time = _DateTime($this->lock_time);
				$now = _DateTime();
				$diff_hour = ($now->getTimestamp() - $lock_time->getTimestamp()) / 60 / 60;
				return $diff_hour < $hours;
			}
			return false;
		}

		static public function get_nation_admin_mobiles_for_forget($mobile, $user_name)
		{
			$user = new static;
			$err = $user->select("mobile=" . _sql($mobile));
			if ($err == ERR_OK)
			{
				return self::get_nation_admin_mobiles($user->country_id);	
			}
			else
				return null;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/