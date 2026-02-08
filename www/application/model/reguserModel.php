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
		"reguserModel",			// model name
		"t_reguser",
		"reguser_id",
		array(
			"user_name",		// 真实姓名
			"user_type",		// 用户权限
			"country_id",		// 国家
			"password",			// 密码
			"sex",				// 性别
			"email",			// 电子邮箱
			"mobile",			// 手机号码
			"other_tel",		// 紧急联系方式
			"home_address",		// 家庭住址
			"introduction",		// 简介
			"d_title",			// 专家-职称
			"d_depart",			// 专家-所属科室
			"hospitals",		// 专家-所属医院
			"d_fee",			// 专家-会诊费
			"i_age",			// 翻译人员-译龄
			"diplomas",			// 资格证书
			"passports",		// 身份证件
			"languages",		// 精通语言
			"diseases",			// 疾病专长
			"status",
			"reject_note"),
		array("rand_id" => true,
			"operator_info" => true));

	class reguserModel extends model // 注册用户信息模型
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

		public function activate() 
		{
			$user = new userModel;
			$user->load($this);
			$same_user = userModel::get_model($this->reguser_id, true);
			if ($same_user) {
				// if same user_id already registered, then generate new user_id
				$user->user_id = null;
			}
			else {
				$user->user_id = $this->reguser_id;
			}
			$user->lock_flag = LOCK_OFF;
			$err = $user->insert();
			if ($err == ERR_OK) {
				if ($this->reguser_id) {
					$err = $this->remove(true);
					if ($err != ERR_OK)
						return $err;
				}

				$languages = explode(",", $this->languages);
				if (count($languages)) {
					ulangModel::save_languages($user->user_id, $languages);
				}
				$diseases = explode(",", $this->diseases);
				if (count($diseases)) {
					udiseaseModel::save_diseases($user->user_id, $diseases);
				}
				$hospitals = explode(",", $this->hospitals);
				if (count($hospitals)) {
					uhospitalModel::save_hospitals($user->user_id, $hospitals);
				}
				return $user;
			}
			return null;
		}

		public function get_diseases()
		{
			if (!_is_empty($this->diseases)) {
				$diseases = array();
				$udisease = new model;
				$err = $udisease->query("SELECT * FROM m_disease WHERE disease_id IN (" .$this->diseases . ") ", 
					array("order" => "disease_id ASC"));
				while ($err == ERR_OK)
				{
					$diseases[] = _l_model($udisease, "disease_name");
					$err = $udisease->fetch();
				}

				return implode(", ", $diseases);
			}

			return "";
		}

		public function get_languages()
		{
			if (!_is_empty($this->languages)) {
				$languages = array();
				$language = new model;
				$err = $language->query("SELECT * FROM m_language WHERE language_id IN (" .$this->languages . ") ", 
					array("order" => "language_id ASC"));
				while ($err == ERR_OK)
				{
					$languages[] = _l_model($language, "language_name");
					$err = $language->fetch();
				}

				return implode(", ", $languages);
			}

			return "";
		}

		public function get_hospitals()
		{
			if (!_is_empty($this->hospitals)) {
				$hospitals = array();
				$hospital = new model;
				$err = $hospital->query("SELECT * FROM m_hospital WHERE hospital_id IN (" .$this->hospitals . ") ", 
					array("order" => "sort ASC"));
				while ($err == ERR_OK)
				{
					$hospitals[] = _l_model($hospital, "hospital_name");
					$err = $hospital->fetch();
				}

				return implode(", ", $hospitals);
			}

			return "";
		}

		public static function get_user_name($reguser_id)
		{
			if ($reguser_id == null)
				return "";

			$user = static::get_model($reguser_id);
			if ($user == null)
				return "";
			else
				return $user->user_name;
		}

		static public function is_exist_by_mobile($mobile)
		{
			$user = new userModel;
			$where = "mobile=" . _sql($mobile);
			$err = $user->select($where);
			return $err == ERR_OK;
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
					
					$attach_url = $dir . $this->reguser_id . "_" . $path;
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

		public function avartar_id()
		{
			if ($this->reguser_id == null)
				return "none";
			return "r_" . $this->reguser_id;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/