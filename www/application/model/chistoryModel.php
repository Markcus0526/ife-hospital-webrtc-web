<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/
	
	_model(
		"chistoryModel",					// model name
		"t_chistory",
		"chistory_id",
		array(
			"user_id",
			"trans_flag",					// 0:原版 1：翻译版
			"interview_id",					// trans_flag=1:相关会诊ID trans_flag=0:null
			"org_id",						// trans_flag=1:原版病历ID trans_flag=0:null
			"post_flag",					// trans_flag=1:post_flag=1:已提交
			"chistory_name",				// 病历名称
			"patient_name",					// 患者-姓名
			"patient_sex",					// 患者-性别
			"birthday",						// 患者-出生日期
			"home_address",					// 患者-家庭住址
			"passports",					// 患者-身份证件
			"disease_id",					// 疾病种类
			"want_resolve_problem",			// 远程会诊想要解决的具体问题
			"sensitive_medicine",			// 已知的过敏药物
			"smoking_drinking",				// 吸烟，饮酒史
			"chronic_disease",				// 长期的慢性疾病
			"family_disease",				// 相关的家族病史
			"note"),						// 其它补充
		array("rand_id" => true,
			"operator_info" => true));

	class chistoryModel extends model // 病历信息模型
	{
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
					
					$attach_url = $dir . "c" . $this->chistory_id . "_" . $path;
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

		public function remove_attaches($upload_id=null)
		{
			if ($upload_id == null) {
				$this->remove_attaches("passports");

				$cattach = new cattachModel;
				$err = $cattach->select("chistory_id=" . _sql($this->chistory_id));
				while ($err == ERR_OK)
				{
					$cattach->remove_attaches();
					$cattach->remove();
					$err = $cattach->fetch();
				}
			}
			else {
				$attaches = $this->$upload_id;
				$attaches = preg_split("/;/", $attaches);

				foreach($attaches as $attach)
				{
					$pf = preg_split("/:/", $attach);
					$path = $pf[0];
					if ($path != "") {
						$real_path = DATA_PATH . $path;
						@unlink($real_path);
					}
				}
			}
		}

		public function avartar_id()
		{
			$id = $this->chistory_id;
			if ($this->trans_flag)
				$id = $this->org_id;

			if ($id == null)
				return "none";
			return "c_" . $id;
		}

		public static function disease_ids($user_id)
		{
			$disease_ids = array();
			$chistory = new static;

			$err = $chistory->select("user_id=" . _sql($user_id));
			while ($err == ERR_OK)
			{
				$disease_ids[] = $chistory->disease_id;
				$err = $chistory->fetch();
			}

			return $disease_ids;
		}

		public function get_cattaches($disease_id=null)
		{
			if ($disease_id == null)
				$disease_id = $this->disease_id;

			$cattaches = array();
			$dtemplate = new dtemplateModel;
			$err = $dtemplate->select("disease_id=" . _sql($disease_id), 
				array("sort ASC"));

			while($err == ERR_OK)
			{
				$cattach = new cattachModel;
				if ($this->chistory_id != null) {
					$err = $cattach->select("chistory_id=" . _sql($this->chistory_id) . " AND 
						dtemplate_id=" . _sql($dtemplate->dtemplate_id));
				}

				$cattach->chistory_id = $this->chistory_id;
				$cattach->dtemplate_id = $dtemplate->dtemplate_id;
				$cattach->dtemplate_name = _l_model($dtemplate, "dtemplate_name");
				$cattach->template_file = _l_model_2($dtemplate, "template_file");

				$cattaches[] = $cattach->props;

				$err = $dtemplate->fetch();
			}

			return $cattaches;
		}

		public function can_edit()
		{
			$my_type = _my_type();
			$my_id = _my_id();

			if ($my_type == UTYPE_PATIENT) {
				if ($my_id != $this->user_id)
					return false;

				if ($this->trans_flag)
					return false;

				$interview = new interviewModel;
				$err = $interview->select("chistory_id=" . _sql($this->chistory_id));

				while($err == ERR_OK)
				{
					if ($interview->status >= ISTATUS_WAITING &&
						$interview->status <= ISTATUS_UNFINISHED)
						return false;
					$err = $interview->fetch();
				}
			}
			else if ($my_type == UTYPE_INTERPRETER) {
				if (!$this->trans_flag)
					return false;

				if ($this->post_flag)
					return false;
			}

			return true;
		}

		public function can_view_address_passport()
		{
			$my_type = _my_type();

			if ($this->trans_flag == 1)
			{
				return false;
			}
			else {
				return !($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER);
			}
		}

		public function can_edit_delete()
		{
			$my_type = _my_type();
			if ($my_type == UTYPE_PATIENT) {
				$my_id = _my_id();
				$db = db::get_db();
				$sql = "SELECT COUNT(*) FROM t_interview 
					WHERE chistory_id=" . _sql($this->chistory_id) . " AND 
						patient_id=" . _sql($my_id) . " AND 
						status >= " . ISTATUS_PAYED;
				$cnt = $db->scalar($sql);

				return $cnt == 0;
			}
			else
				return true;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/