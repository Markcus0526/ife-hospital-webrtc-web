<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class reguserController extends controller {
		public function __construct(){
			parent::__construct();	

			$this->_navi_menu = "reguser";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN);
					break;
			}
		}

		public function doctors($status = RSTATUS_NONE, $page = 0, $size = 10) {
			$this->_subnavi_menu = "reguser_doctors";
			$regusers = array();
			$reguser = new reguserModel;
			
			$my_type = _my_type();
			
			// filtering
			$this->search = new reqsession("reguser_doctors");
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "u.reguser_id DESC";

			$w_and = array();
			$w_and[] = "u.del_flag=0";
			if (!_is_empty($this->search->query)) {
				$w_and[] = "u.user_name LIKE " . _sql("%" . $this->search->query . "%");
			}
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "u.country_id=" . _sql($this->search->country_id);
			}

			$w_and[] = "u.user_type=" . UTYPE_DOCTOR;
			$w_and[] = "u.status=" . _sql($status);

			$this->where = "WHERE " . implode(" AND ", $w_and);

			$this->from = "FROM t_reguser u ";

			$this->counts = $reguser->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $reguser->query("SELECT u.reguser_id, u.user_name, u.sex, u.mobile, u.email, u.d_title,
				u.hospitals, u.diseases, u.create_time, u.reject_note " . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$reguser->hospitals = $reguser->get_hospitals();
				$reguser->diseases = $reguser->get_diseases();

				$regusers[] = clone $reguser;

				$err = $reguser->fetch();
			}

			$this->mReguser = $regusers;
			$this->status = $status;
		}

		public function interpreters($status = RSTATUS_NONE, $page = 0, $size = 10) {
			$this->_subnavi_menu = "reguser_interpreters";
			$regusers = array();
			$reguser = new reguserModel;
			
			$my_type = _my_type();
			
			// filtering
			$this->search = new reqsession("reguser_interpreters");
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "u.reguser_id DESC";

			$w_and = array();
			$w_and[] = "u.del_flag=0";
			if (!_is_empty($this->search->query)) {
				$w_and[] = "u.user_name LIKE " . _sql("%" . $this->search->query . "%");
			}
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "u.country_id=" . _sql($this->search->country_id);
			}

			$w_and[] = "u.user_type=" . UTYPE_INTERPRETER;
			$w_and[] = "u.status=" . _sql($status);

			$this->where = "WHERE " . implode(" AND ", $w_and);

			$this->from = "FROM t_reguser u  ";

			$this->counts = $reguser->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $reguser->query("SELECT u.reguser_id, u.user_name, u.sex, u.mobile, u.email, u.country_id, u.i_age, u.languages, u.create_time, u.reject_note " . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$reguser->languages = $reguser->get_languages();

				$regusers[] = clone $reguser;

				$err = $reguser->fetch();
			}

			$this->mReguser = $regusers;
			$this->status = $status;
		}

		public function detail($reguser_id, $from) {
			$reguser = reguserModel::get_model($reguser_id);
			if ($reguser == null)
				$this->check_error(ERR_NODATA);

			$reguser->country_name = countryModel::get_country_name($reguser->country_id);
			if ($reguser->user_type == UTYPE_DOCTOR) {
				$reguser->d_title_l = stringModel::get_string_l($reguser->d_title);
				$reguser->d_depart_l = stringModel::get_string_l($reguser->d_depart);
				$reguser->hospitals = $reguser->get_hospitals();
				$reguser->diseases = $reguser->get_diseases();
				$reguser->introduction_l = stringModel::get_string_l($reguser->introduction);
			}
			$reguser->languages = $reguser->get_languages();

			if ($reguser->user_type == UTYPE_DOCTOR) {
				$this->_subnavi_menu = "reguser_doctors";
				if ($from == "ud")
					$this->title = _l("专家信息");
			}
			if ($reguser->user_type == UTYPE_INTERPRETER) {
				$this->_subnavi_menu = "reguser_interpreters";
				if ($from == "ui")
					$this->title = _l("翻译信息");
			}
			$this->mReguser = $reguser;
			$this->from = $from;
		}

		public function delete_ajax() {
			$param_names = array("reguser_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$reguser_id = $params->reguser_id;

			$reguser = reguserModel::get_model($reguser_id);

			if ($reguser == null)
				$this->check_error(ERR_NODATA);

			$reguser->remove_attaches("diplomas");
			ulangModel::save_languages($reguser_id, "");
			udiseaseModel::save_diseases($reguser_id, "");

			$err = $reguser->remove();

			$this->finish(null, $err);
		}

		public function accept_ajax()
		{
			$param_names = array("reguser_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$reguser = reguserModel::get_model($params->reguser_id);

			if ($reguser == null)
				$this->check_error(ERR_NODATA);

			$user = $reguser->activate();
			if ($user) {
				$err = ERR_OK;

				_push_message($reguser, _ll("注册审核通过提醒"), _ll("您注册的账号已通过审核。"));
				_opr_log("注册审核通过 新账号编号:" . $user->user_id);
			}
			else 
				$err = ERR_SQL;

			$this->finish(null, $err);
		}

		public function reject_ajax()
		{
			$param_names = array("reguser_id", "reject_note");
			$this->set_api_params($param_names);
			$this->check_required(array("reguser_id"));
			$params = $this->api_params;
			$this->start();

			$reguser = reguserModel::get_model($params->reguser_id);

			if ($reguser == null)
				$this->check_error(ERR_NODATA);

			$reguser->status = RSTATUS_REJECT;
			$reguser->reject_note = $params->reject_note;

			$err = $reguser->save();

			if ($err == ERR_OK)
			{
				_push_message($reguser, _ll("注册审核未通过提醒"), _ll("您注册的账号未通过审核。理由：%s", $reguser->reject_note));
				_opr_log("注册审核未通过 账号编号:" . $reguser->reguser_id);
			}

			$this->finish(null, $err);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/