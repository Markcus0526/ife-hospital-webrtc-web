<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class userController extends controller {
		public function __construct(){
			parent::__construct();	

			$this->_navi_menu = "user";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				case "priv":
				case "admins":
					parent::check_priv($action, UTYPE_SUPER);
					break;
				case "detail":
					parent::check_priv($action, UTYPE_LOGINUSER);
					break;
				default:
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN);
					break;
			}
		}

		public function doctors($page = 0, $size = 10) {
			$this->_subnavi_menu = "user_doctors";
			$users = array();
			$user = new userModel;
			
			$my_type = _my_type();

			// filtering
			$this->search = new reqsession("user_doctors");
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "u.create_time DESC";

			$w_and = array();
			$w_and[] = "u.del_flag=0";
			if (!_is_empty($this->search->query)) {
				$like = _sql("%" . $this->search->query . "%");
				$w_and[] = "(u.user_name LIKE " . $like  . " OR u.user_id LIKE " . $like  . " OR u.mobile LIKE " . $like . ")";
			}
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "u.country_id=" . _sql($this->search->country_id);
			}

			$w_and[] = "u.user_type = " . UTYPE_DOCTOR . "";

			$this->where = "WHERE " . implode(" AND ", $w_and);

			$this->from = "FROM m_user u ";

			$this->counts = $user->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $user->query("SELECT u.user_id, u.user_name, u.sex, u.mobile, u.email, u.d_title, u.d_fee, u.d_cunit, u.country_id, u.lock_flag, u.lock_time " . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$user->hospitals = uhospitalModel::get_hospitals($user->user_id);
				$user->diseases = udiseaseModel::get_diseases($user->user_id, false);
				$user->refresh_lock_flag();

				$users[] = clone $user;

				$err = $user->fetch();
			}

			$this->mUsers = $users;
		}

		public function patients($mode="", $page = 0, $size = 10) {
			if ($mode == "chistory") {
				$this->_navi_menu = "chistory";
				$this->_subnavi_menu = "chistory_list";
				$reqsess_name = "chistory";
			}
			else {
				$this->_subnavi_menu = "user_patients";
				$reqsess_name = "user_patients";
			}
			$users = array();
			$user = new userModel;
			
			$my_type = _my_type();
			
			// filtering
			$this->search = new reqsession($reqsess_name);
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "u.create_time DESC";

			$w_and = array();
			$w_and[] = "u.del_flag=0";
			if (!_is_empty($this->search->query)) {
				$like = _sql("%" . $this->search->query . "%");
				$w_and[] = "(u.user_name LIKE " . $like  . " OR u.user_id LIKE " . $like  . " OR u.mobile LIKE " . $like . ")";
			}
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "u.country_id=" . _sql($this->search->country_id);
			}

			$w_and[] = "u.user_type = " . UTYPE_PATIENT . "";

			$this->where = "WHERE " . implode(" AND ", $w_and);

			$this->from = "FROM m_user u  ";

			$this->counts = $user->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $user->query("SELECT u.user_id, u.user_name, u.sex, u.mobile, u.email, u.country_id, u.lock_flag, u.lock_time " . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$user->languages = ulangModel::get_languages($user->user_id, false);
				$user->refresh_lock_flag();

				$users[] = clone $user;

				$err = $user->fetch();
			}

			$this->mUsers = $users;
			$this->mode = $mode;
		}

		public function select2_ajax() {
			$param_names = array("query", "user_type");
			$this->set_api_params($param_names);
			$params = $this->api_params;

			$my_type = _my_type();
			$users = array();
			$user = new userModel;

			$this->where = "u.del_flag=0";
			if ($this->query != null) {
				$like = _sql("%" . $params->query . "%");
				$this->where .= " AND (u.user_name LIKE " . $like  . " OR u.user_id LIKE " . $like  . " OR u.mobile LIKE " . $like . ")";
			}

			if ($params->user_type) {
				$this->where .= " AND u.user_type=" . _sql($params->user_type);
			}

			if ($my_type == UTYPE_ADMIN)
			{
				$this->where .= " AND u.country_id=" . _sql(_country_id());
			}

			$this->order = "u.user_id DESC";
			$sql = "SELECT u.user_id, u.user_name
				FROM m_user u";

			$err = $user->query($sql,
				array(
					"where" => $this->where,
					"order" => $this->order,
					"limit" => 100));
			while ($err == ERR_OK)
			{
				$users[] = array(
					"id" => $user->user_id,
					"text" => $user->user_id . " : " . $user->user_name
				);

				$err = $user->fetch();
			}

			$this->finish(array("options" => $users), ERR_OK);
		}

		public function interpreters($mode="", $page = 0, $size = 10) {
			$this->_subnavi_menu = "user_interpreters";
			$users = array();
			$user = new userModel;
			
			$my_type = _my_type();

			$select_mode = false;
			$sess_name = "user_interpreters";
			if (substr($mode, 0, 6) == "select")
			{
				$select_mode = true;
				$sess_name = "user_interpreters_select";

				$interp = new interpModel;

				$interview_id = substr($mode, 6);
				$interview = interviewModel::get_model($interview_id);
				if ($interview) {
					$interp->select("interview_id=" . _sql($interview_id));
				}

				$pl_id = $interp->planguage_id;
				$dl_id = $interp->dlanguage_id;
			}
			
			// filtering
			$this->search = new reqsession($sess_name);
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "u.create_time DESC";

			$w_and = array();
			$w_and[] = "u.del_flag=0";
			if (!_is_empty($this->search->query)) {
				$like = _sql("%" . $this->search->query . "%");
				$w_and[] = "(u.user_name LIKE " . $like  . " OR u.user_id LIKE " . $like  . " OR u.mobile LIKE " . $like . ")";
			}
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "u.country_id=" . _sql($this->search->country_id);
			}

			$w_and[] = "u.user_type = " . UTYPE_INTERPRETER . "";

			$this->where = "WHERE " . implode(" AND ", $w_and);

			$this->from = "FROM m_user u ";
			if ($select_mode) {
				$this->from .= " INNER JOIN m_ulang ul1 ON u.user_id=ul1.user_id AND ul1.language_id=" . _sql($pl_id);
				$this->from .= " INNER JOIN m_ulang ul2 ON u.user_id=ul2.user_id AND ul2.language_id=" . _sql($dl_id);
			}

			$this->counts = $user->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $user->query("SELECT u.user_id, u.user_name, u.sex, u.mobile, u.email, u.country_id, u.i_age, u.lock_flag, u.lock_time " . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$user->languages = ulangModel::get_languages($user->user_id, false);
				$user->refresh_lock_flag();

				$users[] = clone $user;

				$err = $user->fetch();
			}

			$this->mUsers = $users;
			$this->mConfig = new sysconfig;
			$this->mode = $mode;
			$this->select_mode = $select_mode;

			if ($select_mode)
				return "popup/";
		}

		public function admins($page = 0, $size = 10) {
			$this->_subnavi_menu = "user_admins";
			$users = array();
			$user = new userModel;
			
			// filtering
			$this->search = new reqsession("user_admins");

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "u.user_id ASC";

			$w_and = array();
			$w_and[] = "u.del_flag=0";
			if (!_is_empty($this->search->query)) {
				$like = _sql("%" . $this->search->query . "%");
				$w_and[] = "(u.user_name LIKE " . $like  . " OR u.user_id LIKE " . $like  . " OR u.mobile LIKE " . $like . ")";
			}
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "u.country_id=" . _sql($this->search->country_id);
			}

			$w_and[] = "u.user_type = " . UTYPE_ADMIN . "";

			$this->where = "WHERE " . implode(" AND ", $w_and);

			$this->from = "FROM m_user u  ";

			$this->counts = $user->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $user->query("SELECT u.user_id, u.user_name, u.sex, u.mobile, u.email, u.country_id, u.lock_flag, u.lock_time " . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$user->admin_privs = privModel::from_user_id($user->user_id, true, true);
				$user->refresh_lock_flag();
				
				$users[] = clone $user;

				$err = $user->fetch();
			}

			$this->mUsers = $users;
		}

		public function irating($user_id) {
			$user = userModel::get_model($user_id);
			if ($user == null)
				$this->check_error(ERR_NODATA);

			$this->mIratings = logIratingModel::stats($user_id);
		}

		public function detail($user_id, $from) {
			$user = userModel::get_model($user_id);
			if ($user == null)
				$this->check_error(ERR_NODATA);

			$my_id = _my_id();
			$my_type = _my_type();
			if ($from == 'd') {
				// 专家库 
				$exrate = exrateModel::get_last();
				if ($my_type == UTYPE_PATIENT) {
					$my_diseases = chistoryModel::disease_ids($my_id);
				}
				else {
					$my_diseases = null;	
				}
			}

			$user->country_name = countryModel::get_country_name($user->country_id);
			if ($user->user_type == UTYPE_DOCTOR) {
				$user->d_title_l = stringModel::get_string_l($user->d_title);
				$user->d_depart_l = stringModel::get_string_l($user->d_depart);
				$user->hospitals = uhospitalModel::get_hospitals($user->user_id);
				if ($from == 'd') {
					// 专家库 
					$match = false;
					$user->diseases = udiseaseModel::get_diseases($user->user_id, false, $my_diseases, $match);

					$user->show_d_fee = $match;

					if ($user->d_cunit == "rmb") {
						$user->ex_d_cunit = "usd";
						$user->ex_d_fee = _round($user->d_fee * $exrate->rmb_to_usd, 2);
					}
					else if ($user->d_cunit == "usd") {
						$user->ex_d_cunit = "rmb";
						$user->ex_d_fee = _round($user->d_fee * $exrate->usd_to_rmb, 2);
					}
				}
				else {
					$user->diseases = udiseaseModel::get_diseases($user->user_id, false);
				}
			}
			if (!_is_empty($user->introduction))
				$user->introduction_l = stringModel::get_string_l($user->introduction);
			$user->languages = ulangModel::get_languages($user->user_id, false);
			$user->refresh_lock_flag();

			if ($user->user_type == UTYPE_PATIENT) {
				$this->_subnavi_menu = "user_patients";
				if ($from == "up")
					$this->title = _l("患者信息");
				else
					$this->title = _l("患者详情");
			}
			if ($user->user_type == UTYPE_DOCTOR) {
				$this->_subnavi_menu = "user_doctors";
				if ($from == "ud")
					$this->title = _l("专家信息");
				else
					$this->title = _l("专家详情");

				if ($from == "d") {
					$this->_navi_menu = "interview";
					$this->_subnavi_menu = "interview_reserve";
				}
			}
			if ($user->user_type == UTYPE_INTERPRETER) {
				$this->_subnavi_menu = "user_interpreters";
				if ($from == "ui")
					$this->title = _l("翻译信息");
				else
					$this->title = _l("翻译详情");
			}
			if ($user->user_type == UTYPE_ADMIN) {
				$this->_subnavi_menu = "user_admins";
				if ($from == "ua")
					$this->title = _l("管理员信息");
				else {
					$this->check_error(ERR_NOPRIV);
				}
			}
			if (substr($from, 0, 1) == "i") {
				$this->_navi_menu = "interview";
				$this->_subnavi_menu = "interview_list";
			}
			$this->mUser = $user;
			$this->from = $from;
		}

		public function edit($user_id, $user_type, $from = null) {
			$this->_subnavi_menu = "profile_main";

			if ($user_id == null) {
				$user = new userModel;
				$user->user_type = $user_type;
				$user->hospitals = array();
				$user->languages = array();
			}
			else {
				$user = userModel::get_model($user_id);
				if ($user == null)
					$this->check_error(ERR_NODATA);

				if ($user->user_type == UTYPE_DOCTOR) {
					$user->hospitals = uhospitalModel::get_hospitals($user->user_id, true);
					$user->diseases = udiseaseModel::get_diseases($user->user_id, true);
				}
				$user->languages = ulangModel::get_languages($user->user_id, true);
			}
			$user->password = "";
			$user->refresh_lock_flag();

			if ($user->user_type == UTYPE_PATIENT) {
				$this->_subnavi_menu = "user_patients";
				$this->return_url = "user/patients";
				if ($from == "up" || $from == null) {
					$from = "up";
					if ($user_id == null)
						$this->title = _l("新增患者");
					else
						$this->title = _l("编辑患者信息");
				}
			}
			if ($user->user_type == UTYPE_DOCTOR) {
				$this->_subnavi_menu = "user_doctors";
				$this->return_url = "user/doctors";
				if ($from == "ud" || $from == null) {
					$from = "ud";
					if ($user_id == null)
						$this->title = _l("新增专家");
					else
						$this->title = _l("编辑专家信息");
				}
			}
			if ($user->user_type == UTYPE_INTERPRETER) {
				$this->_subnavi_menu = "user_interpreters";
				$this->return_url = "user/interpreters";
				if ($from == "ui" || $from == null) {
					$from = "ui";
					if ($user_id == null)
						$this->title = _l("新增翻译");
					else
						$this->title = _l("编辑翻译信息");
				}
			}
			if ($user->user_type == UTYPE_ADMIN) {
				$this->_subnavi_menu = "user_admins";
				$this->return_url = "user/admins";
				if ($from == "ua" || $from == null) {
					$from = "ua";
					if ($user_id == null)
						$this->title = _l("新增管理员");
					else
						$this->title = _l("编辑管理员信息");
				}
			}
			$this->mUser = $user;
			$this->from = $from;
		}

		public function save_ajax() {
			$param_names = array("user_id",
				"user_name", "country_id", "user_type", "password", 
				"sex", "mobile", "other_tel", "email", "avartar",
				"home_address", "introduction", "languages", "diseases",
				"d_title", "d_depart", "hospitals", 
				"d_fee", "d_cunit", "d_fee_usd", "d_fee_rmb",
				"i_age", "diplomas", "passports");
			$this->set_api_params($param_names);
			$params = $this->api_params;

			$this->start();

			$user_id = $params->user_id;

			if ($user_id == null)
				$user = new userModel;
			else {
				$user = userModel::get_model($user_id);
				if ($user == null)
					$this->check_error(ERR_NODATA);
			}
			
			if (userModel::is_exist_by_mobile($params->mobile, $user_id))
				$this->check_error(ERR_ALREADY_USING_MOBILE);

			$org_password = $user->password;
			$user->load($params);

			switch ($params->d_cunit) {
			 	case 'usd':
					$user->d_fee = $params->d_fee_usd;
			 		break;
			 	case 'rmb':
					$user->d_fee = $params->d_fee_rmb;
			 		break;
			}

			if (_is_empty($user->user_type)) 
				$user->user_type = UTYPE_PATIENT;

			if ($user->user_type == UTYPE_DOCTOR || $user->user_type == UTYPE_INTERPRETER) {
				if ($params->exist_prop("diplomas")) {
					$user->save_attaches("diplomas");
				}
			}
			if ($user->user_type == UTYPE_PATIENT || $user->user_type == UTYPE_DOCTOR || $user->user_type == UTYPE_INTERPRETER) {
				if ($params->exist_prop("passports")) {
					$user->save_attaches("passports");
				}
			}
			
			$user->country_id = countryModel::tel_num_to_country_id($user->mobile);
			if ($user->country_id == null)
				$user->country_id = 1; // 中国

			if (!_is_empty($params->password))
				$user->password = _password($params->password);
			else 
				$user->password = $org_password;

			$this->check_error($err = $user->save());

			// update hospitals
			if (!_is_empty($params->hospitals)) {
				uhospitalModel::save_hospitals($user->user_id, $params->hospitals);
			}
			// update languages
			if (!_is_empty($params->languages)) {
				ulangModel::save_languages($user->user_id, $params->languages);
			}
			// update diseases
			if ($user->user_type == UTYPE_DOCTOR) {
				if (is_array($params->diseases)) {
					udiseaseModel::save_diseases($user->user_id, $params->diseases);
				}
			}

			// change avartar
			if (substr($params->avartar, 0, 4) == 'tmp/') {
				$tmp_path = SITE_ROOT . $params->avartar;
				if (_resize_image($tmp_path, '', 'jpg', AVARTAR_SIZE, AVARTAR_SIZE, RESIZE_CROP)) {
					$avartar_path = AVARTAR_PATH . $user->user_id . ".png";
					if (rename($tmp_path, $avartar_path)) {
						_renew_avartar_cache_id();
					}
				}
			}

			if ($err == ERR_OK) {
				if ($user_id == null)
					_opr_log("新增用户成功 用户编号:" . $user->user_id);
				else
					_opr_log("用户信息更改(" . join(',', $user->dirties) . ")成功 用户编号:" . $user->user_id);
			}
								
			$this->finish(array("user_id" => $user->user_id), $err);
		}

		public function save_ifee_ajax() {
			$param_names = array("interpreter_fee");
			$this->set_api_params($param_names);
			$params = $this->api_params;

			$sysconfig = new sysconfig;
			$sysconfig->interpreter_fee = round($params->interpreter_fee, 3);

			$sysconfig->save();
								
			$this->finish(array(), ERR_OK);
		}

		public function delete_ajax() {
			$param_names = array("user_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$user_id = $params->user_id;

			$user = userModel::get_model($user_id);

			if ($user == null)
				$this->check_error(ERR_NODATA);

			$user->remove_attaches("diplomas");
			ulangModel::save_languages($user_id, "");
			udiseaseModel::save_diseases($user_id, "");

			$err = $user->remove();

			if ($err == ERR_OK) {
				_opr_log("用户删除成功 用户编号:" . $user_id);
			}

			$this->finish(null, $err);
		}

		public function unlock_ajax() {
			$param_names = array("user_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$user_id = $params->user_id;

			$user = userModel::get_model($user_id);

			if ($user == null)
				$this->check_error(ERR_NODATA);

			$err = $user->clear_lock();
			if ($err == ERR_OK) 
			{
				logFailModel::clear($user_id);
				_opr_log("用户回复成功 用户编号:" . $user_id);
			}

			$this->finish(null, $err);
		}

		public function priv($user_id) {
			$user = userModel::get_model($user_id);
			if ($user == null)
				$this->check_error(ERR_NODATA);

			if ($user->user_type != UTYPE_ADMIN)
				$this->check_error(ERR_NOPRIV);

			$user->priv = privModel::from_user_id($user_id, true);

			$this->mUser = $user;
		}

		public function priv_ajax() {
			$param_names = array("user_id", "priv_reg_user", "priv_patients", "priv_doctors", "priv_interpreters", "priv_hospitals", "priv_chistory", "priv_reserve", "priv_interviews", "priv_profile", "priv_stats", "priv_feedback", "priv_syscheck");
			$this->set_api_params($param_names);
			$this->check_required(array("user_id"));
			$params = $this->api_params;
			$this->start();

			$user_id = $params->user_id;
			$user = userModel::get_model($user_id);
			if ($user == null)
				$this->check_error(ERR_NODATA);

			if ($user->user_type != UTYPE_ADMIN)
				$this->check_error(ERR_NOPRIV);

			$priv = privModel::from_user_id($user_id, true);

			$priv->load($params);

			$err = $priv->save();
			if ($err == ERR_OK) 
			{
				_opr_log("用户权限设置成功 用户编号:" . $user_id);
			}


			$this->finish(null, $err);
		}

	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/