<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class chistoryController extends controller {
		public function __construct() {
			parent::__construct();

			$this->_navi_menu = "chistory";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				case "edit":
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN | UTYPE_PATIENT);
					break;
				case "edit_trans":
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN | UTYPE_INTERPRETER);
					break;
				default:
					parent::check_priv($action, UTYPE_LOGINUSER);
					break;
			}
		}

		public function index($patient_id="", $page = 0, $size = 20) {
			$this->_subnavi_menu = "chistory_list";
			$chistorys = array();
			$chistory = new chistoryModel;
			
			$this->search = new reqsession("chistory_list");

			$my_type = _my_type();
			$where = "trans_flag=0 ";
			if ($my_type == UTYPE_PATIENT) {
				$patient_id = _my_id();
			}
			else if ($patient_id == "")
				_goto("user/patients/chistory");

			if ($patient_id)
				$where .= " AND user_id=" . _sql($patient_id);

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "create_time DESC";

			$this->counts = $chistory->counts($where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $chistory->select($where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$chistory->disease_name = diseaseModel::get_disease_name($chistory->disease_id);
				$chistorys[] = clone $chistory;

				$err = $chistory->fetch();
			}

			$this->mChistorys = $chistorys;
			$this->patient_id = $patient_id;
		}

		public function edit($patient_id="", $chistory_id = null) {
			$this->_subnavi_menu = "chistory_list";

			$my_type = _my_type();
			if ($my_type == UTYPE_PATIENT) {
				$patient_id = _my_id();
			}
			else if ($patient_id == "")
				_goto("user/patients/chistory");

			if ($chistory_id == null) {
				// insert
				$chistory = new chistoryModel;
				$chistory->user_id = $patient_id;
			}
			else {
				// edit
				$chistory = chistoryModel::get_model($chistory_id);
				if ($chistory == null)
					$this->check_error(ERR_NODATA);

				if (!$chistory->can_edit())
					$this->check_error(ERR_NOPRIV);

				$cavartar = _avartar_url($chistory->avartar_id(), false);
				if (file_exists(DATA_PATH . $cavartar))
					$chistory->cavartar = $cavartar;
			}

			$chistory->cattaches = $chistory->get_cattaches();

			$this->chistory_id = $chistory_id;
			$this->mChistory = $chistory;
			$this->mChistory->guide = _fread(SITE_ROOT.'/resource/txt/guide.'._lang().'.txt');
			$this->patient_id = $patient_id;
		}

		public function edit_trans($patient_id, $org_id, $interview_id) {
			$this->_navi_menu = "interview";
			$this->_subnavi_menu = "interview_list";

			$my_type = _my_type();

			$org = chistoryModel::get_model($org_id);
			if ($org == null)
				$this->check_error(ERR_NODATA);
			
			$interview = interviewModel::get_model($interview_id);
			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($my_type == UTYPE_INTERPRETER)
			{
				if ($interview->interpreter_id != _my_id())
					$this->check_error(ERR_NOPRIV);
			}
			
			$chistory = new chistoryModel;
			$err = $chistory->select("trans_flag=1 AND
				user_id=" . _sql($patient_id) . " AND 
				org_id=" . _sql($org_id) . " AND 
				interview_id=" . _sql($interview_id));

			if ($err == ERR_OK) {
				if (!$chistory->can_edit())
					$this->check_error(ERR_NOPRIV);
			}

			$chistory->user_id = $patient_id;
			$chistory->trans_flag = 1;
			$chistory->org_id = $org_id;
			$chistory->interview_id = $interview_id;
			$chistory->patient_sex = $org->patient_sex;
			$chistory->birthday = $org->birthday;
			$chistory->disease_id = $org->disease_id;
			$chistory->disease_name = diseaseModel::get_disease_name($chistory->disease_id);
			$chistory->cattaches = $chistory->get_cattaches();

			$cavartar = _avartar_url($org->avartar_id(), false);
			$chistory->cavartar = $cavartar;

			$this->chistory_id = $chistory->chistory_id;
			$this->mChistory = $chistory;
			$this->mChistory->guide = _fread(SITE_ROOT.'/resource/txt/guide.'._lang().'.txt');
			$this->patient_id = $patient_id;

			return "main/chistory/chistory_edit";
		}

		public function detail($patient_id="", $chistory_id = null, $from_id = null) {
			$this->_subnavi_menu = "chistory_list";

			$my_type = _my_type();
			if ($my_type == UTYPE_PATIENT) {
				$patient_id = _my_id();
			}
			else if ($patient_id == "")
				_goto("user/patients/chistory");

			if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER)
			{
				$this->_navi_menu = "interview";
				$this->_subnavi_menu = "interview_list";
			}

			$chistory = chistoryModel::get_model($chistory_id);
			if ($chistory == null)
				$this->check_error(ERR_NODATA);
			$chistory->disease_name = diseaseModel::get_disease_name($chistory->disease_id);

			if (substr($from_id, 0, 1) == "i") {
				interviewModel::browse_chistory(substr($from_id, 1));
			}

			$chistory->cattaches = $chistory->get_cattaches();

			$this->chistory_id = $chistory_id;
			$this->mChistory = $chistory;
			$this->from_id = $from_id;
			$this->mChistory->guide = _fread(SITE_ROOT.'/resource/txt/guide.'._lang().'.txt');
			$this->patient_id = $patient_id;
		}

		public function save_ajax()
		{
			$param_names = array("chistory_id", 
				"user_id",
				"trans_flag",
				"interview_id",
				"org_id",
				"post_flag",
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
				"note",							// 其它补充
				"cavartar");
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$my_type = _my_type();
			$chistory_id = $params->chistory_id;
			if ($chistory_id == null) {
				$chistory = new chistoryModel;
				if ($my_type == UTYPE_PATIENT)
					$params->user_id = _my_id();
			}
			else {
				$chistory = chistoryModel::get_model($chistory_id);

				if ($chistory == null)
					$this->check_error(ERR_NODATA);
			}

			$chistory->load($params);

			if ($chistory->trans_flag == null)
			{
				$chistory->trans_flag = 0;
			}

			$err = $chistory->save();

			if ($err == ERR_OK) {
				// save attaches
				$chistory->save_attaches("passports");
				$i = 0;
				do {
					$cattach_files_field = "cattach_files_" . $i;
					if (_is_empty($this->$cattach_files_field))
						break;

					$cattach_id_field = "cattach_id_" . $i;
					$dtemplate_id_field = "dtemplate_id_" . $i;

					$cattach_id = $this->$cattach_id_field;

					$cattach = null;
					if (!_is_empty($cattach_id))
					{
						$cattach = cattachModel::get_model($cattach_id);
					}

					if ($cattach == null) {
						$cattach = new cattachModel;
					}

					$cattach->chistory_id = $chistory->chistory_id;
					$cattach->dtemplate_id = $this->$dtemplate_id_field;
					$cattach->files = $this->$cattach_files_field;
					$cattach->save_attaches();

					$err = $cattach->save();

					$i ++;
				}
				while (true);

				$err = $chistory->save();
			}

			if ($err == ERR_OK) {
				// change avartar
				if (substr($params->cavartar, 0, 4) == 'tmp/') {
					$tmp_path = SITE_ROOT . $params->cavartar;
					if (_resize_image($tmp_path, '', 'jpg', AVARTAR_SIZE, AVARTAR_SIZE, RESIZE_CROP)) {
						$avartar_path = AVARTAR_PATH . $chistory->avartar_id() . ".png";
						if (rename($tmp_path, $avartar_path)) {
							_renew_avartar_cache_id();
						}
					}
				}

				if ($chistory->trans_flag && $chistory->post_flag)
				{
					$interview = interviewModel::get_model($params->interview_id);
					if ($interview)
					{
						$interview->trans_chistory_id = $chistory->chistory_id;
						if ($interview->status == ISTATUS_OPENED) {
							$interview->sub_status |= SISTATUS_D_MUST_ACCEPT_PATIENT;
						}
						$err = $interview->save();

						if ($err == ERR_OK)
						{
							logIhistoryModel::log($interview, IHTYPE_UPLOAD_T_CHISTORY, _my_name());
						}
					}
				}
			}

			if ($err == ERR_OK) {
				if ($params->chistory_id == null)
					_opr_log("新增病历成功 病历编号:" . $chistory->chistory_id);
				else
					_opr_log("病历更改(" . join(',', $chistory->dirties) . ")成功 病历编号:" . $chistory->chistory_id);
			}

			$this->finish(null, $err);
		}

		public function delete_ajax() {
			$param_names = array("chistory_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$chistory_id = $params->chistory_id;

			$chistory = chistoryModel::get_model($chistory_id);

			if ($chistory == null)
				$this->check_error(ERR_NODATA);

			$chistory->remove_attaches();

			$err = $chistory->remove();

			if ($err == ERR_OK) {
				_opr_log("病历删除成功 病历编号:" . $chistory_id);
			}

			$this->finish(null, $err);
		}

		public function select2_ajax() {
			$param_names = array("query", "user_id");
			$this->set_api_params($param_names);
			$this->check_required(array());
			$params = $this->api_params;

			$my_type = _my_type();
			$chistorys = array();
			$chistory = new chistoryModel;

			$this->where = "c.del_flag=0 AND trans_flag=0 ";
			if ($this->query != null) {
				$ss = _sql("%" . $params->query . "%");
				$this->where .= " AND c.chistory_name LIKE " . $ss;
			}

			if ($my_type == UTYPE_PATIENT) {
				$this->where .= " AND c.user_id=" . _sql(_my_id());
			}
			else if ($params->user_id) {
				$this->where .= " AND c.user_id=" . _sql($params->user_id);
			}

			$this->order = "c.chistory_id DESC";
			$sql = "SELECT c.chistory_id, c.chistory_name 
				FROM t_chistory c";

			$err = $chistory->query($sql,
				array(
					"where" => $this->where,
					"order" => $this->order,
					"limit" => 100));
			while ($err == ERR_OK)
			{
				array_push($chistorys, array(
					"id" => $chistory->chistory_id,
					"text" => $chistory->chistory_name
				));

				$err = $chistory->fetch();
			}

			$this->finish(array("options" => $chistorys), ERR_OK);
		}

		public function get_ajax()
		{
			$param_names = array("chistory_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;

			$chistory = chistoryModel::get_model($params->chistory_id);
			if ($chistory == null)
				$this->check_error(ERR_NODATA);

			$chistory->patient_sex_name = _code_label(CODE_SEX, $chistory->patient_sex);
			$chistory->disease_name = diseaseModel::get_disease_name($chistory->disease_id);

			$this->finish(array("chistory" => $chistory->props), ERR_OK);
		}

		public function get_cattaches_ajax()
		{
			$param_names = array("chistory_id", "disease_id");
			$this->set_api_params($param_names);
			$this->check_required(array("disease_id"));
			$params = $this->api_params;

			$chistory_id = $params->chistory_id;
			$disease_id = $params->disease_id;

			$chistory = new chistoryModel;
			if ($chistory_id != null) {
				$err = $chistory->select("chistory_id=" . _sql($chistory_id));
				$this->check_error($err);
			}

			$cattaches = $chistory->get_cattaches($disease_id);

			$this->finish(array("cattaches" => $cattaches), ERR_OK);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/