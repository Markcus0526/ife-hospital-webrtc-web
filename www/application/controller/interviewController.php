<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class interviewController extends controller {
		public function __construct() {
			parent::__construct();

			$this->_navi_menu = "interview";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				case "upload_prescription_ajax":
				case "accept_patient_ajax":
					parent::check_priv($action, UTYPE_DOCTOR);
					break;
				case "upload_trans_prescription_ajax":
					parent::check_priv($action, UTYPE_INTERPRETER);
					break;
				case "interp_list":
				case "bid_ajax":
					parent::check_priv($action, UTYPE_INTERPRETER);
					break;
				case "play":
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN);
					break;
				case "change_cost":
					parent::check_priv($action, UTYPE_SUPER);
					break;
				default:
					parent::check_priv($action, UTYPE_LOGINUSER);
					break;
			}
		}

		public function index($page = 0, $size = 20) {
			$this->_subnavi_menu = "interview_list";
			$interviews = array();
			$interview = new interviewModel;

			$my_type = _my_type();
			
			$this->search = new reqsession("interview_list");
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			$my_type = _my_type();
			$w_and = array();
			$w_and[] = "i.del_flag=0";

			// filtering
			// 1.check priv
			if ($my_type == UTYPE_PATIENT) {
				$w_and[] = "i.patient_id=" . _sql(_my_id());
			}
			else if ($my_type == UTYPE_DOCTOR) {
				$w_and[] = "i.doctor_id=" . _sql(_my_id());
				$w_and[] = "NOT(i.sub_status<=>" . SISTATUS_CANCEL_BEFORE_PAY .")";
			}
			else if ($my_type == UTYPE_INTERPRETER) {
				$w_and[] = "i.interpreter_id=" . _sql(_my_id());
				$w_and[] = "NOT(i.sub_status<=>" . SISTATUS_CANCEL_BEFORE_PAY .")";
			}

			// 2.from - to date
			if ($this->search->from_date == null) {
				$this->search->from_date = _date(null, 'Y-01-01');
			}
			if ($this->search->to_date == null) {
				$this->search->to_date = _date_add(null, 31);
			}
			if ($this->search->query != "") {
				$like = _sql("%" . $this->search->query . "%");
				$w_and[] = "(i.interview_id LIKE " . $like  . " OR p.user_name LIKE " . $like  . " OR d.user_name LIKE " . $like . " OR n.user_name LIKE " . $like . ")";
			}
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "p.country_id=" . _sql($this->search->country_id);
			}

			$w_and[] = "i.reserved_starttime>=" . _sql($this->search->from_date);
			$w_and[] = "i.reserved_starttime<=" . _sql($this->search->to_date . " 23:59:59");

			// 3. status
			if ($this->search->istatuses == null) {
				$istatuses = array(ISTATUS_OPENED, ISTATUS_PROGRESSING, ISTATUS_UNFINISHED, ISTATUS_FINISHED, ISTATUS_CANCELED);
				if ($my_type != UTYPE_DOCTOR && $my_type != UTYPE_INTERPRETER) {
					array_push($istatuses, ISTATUS_NONE);
				}
				if ($my_type != UTYPE_DOCTOR) {
					array_push($istatuses, ISTATUS_WAITING);
				}
				if ($my_type != UTYPE_INTERPRETER) {
					array_push($istatuses, ISTATUS_PAYED);
				}
				$this->search->istatuses = $istatuses;
			}
			else if (is_string($this->search->istatuses)) {
				$istatuses = explode(",", $this->search->istatuses);
				$this->search->istatuses = $istatuses;
			}
			if (count($this->search->istatuses)) {
				$w_and[] = "i.status IN (" . implode(",", $this->search->istatuses) . ")";	
			}

			$this->where = " WHERE " . implode(" AND ", $w_and);

			$this->from = " FROM t_interview i 
				LEFT JOIN m_user p ON i.patient_id=p.user_id
				LEFT JOIN m_user d ON i.doctor_id=d.user_id
				LEFT JOIN m_user n ON i.interpreter_id=n.user_id";

			$this->search->istatuses = implode(",", $this->search->istatuses);

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "i.interview_id DESC";

			$this->counts = $interview->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $interview->query("SELECT i.*, 
					p.user_name patient_name, p.user_name_l patient_name_l, 
					d.user_name doctor_name, d.user_name_l doctor_name_l,
					n.user_name interpreter_name, n.user_name_l interpreter_name_l
					" . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			$exist_state_none = false; // [待付款]
			while ($err == ERR_OK)
			{
				$interview->disease_name = diseaseModel::get_disease_name($interview->disease_id);

				$interview->interp_lang_names = languageModel::get_language_names(array($interview->planguage_id, $interview->dlanguage_id));

				// 状态更新
				$interview->refresh_status();

				// 历史
				$interview->logs = logIhistoryModel::get_logs($interview->interview_id);

				$interview->edit_trans_chistory_id = null;
				if ($interview->status == ISTATUS_NONE)
				{
					// 存在[待付款]的会诊
					$exist_state_none = true;
				}
				else if ($interview->can_insert_trans_chistory())
				{
					$chistory = new chistoryModel;
					$err = $chistory->select("trans_flag=1 AND
						user_id=" . _sql($interview->patient_id) . " AND 
						org_id=" . _sql($interview->chistory_id) . " AND 
						interview_id=" . _sql($interview->interview_id));

					if ($err == ERR_OK)
					{
						$interview->edit_trans_chistory_id = $chistory->chistory_id;
					}

				}

				$interviews[] = clone $interview;

				$err = $interview->fetch();
			}

			if ($exist_state_none) {
				$exrate = exrateModel::get_last();

				if ($exrate) {
					for ($i = 0; $i < count($interviews); $i ++)
					{
						if ($interviews[$i]->cunit == "rmb") {
							$interviews[$i]->ex_cunit = "usd";
							$interviews[$i]->ex_cost = _round($interviews[$i]->cost * $exrate->rmb_to_usd, 2);
						}
						else if ($interviews[$i]->cunit == "usd") {
							$interviews[$i]->ex_cunit = "rmb";
							$interviews[$i]->ex_cost = _round($interviews[$i]->cost * $exrate->usd_to_rmb, 2);
						}
					}
				}
			}

			$this->mInterviews = $interviews;
			$this->mCInterview = new interviewModel;
			$this->mCInterview->cancel_cause_id = CCAUSE_OTHER;

			if ($my_type == UTYPE_DOCTOR)
			{
				$this->mAInterview = new interviewModel;
				$this->mAInterview->cancel_cause_id = CCAUSE_OTHER;
			}

			if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER)
				$this->mTotals = interviewModel::totals($this->search);

			$this->addjs("js/bootstrap-daterangepicker/moment.min.js");
			$this->addjs("js/bootstrap-daterangepicker/daterangepicker.js");
			$this->addjs("js/bootstrap-daterangepicker/daterangepicker." . _lang() . ".js");
		}

		public function interp_list($page = 0, $size = 20) 
		{
			$this->_subnavi_menu = "interview_interp_list";
			$interviews = array();
			$interview = new interviewModel;
			
			$this->search = new reqsession("interview_interp_list");

			$my_type = _my_type();
			$my_language_ids = ulangModel::get_languages(_my_id(), true);
			$w_and = array();

			// filtering
			$w_and[] = "i.del_flag=0";
			$w_and[] = "i.interpreter_id IS NULL";
			$w_and[] = "i.need_interpreter=1";

			// 2.from - to date
			if ($this->search->from_date == null) {
				$this->search->from_date = _date(null, 'Y-01-01');
			}
			if ($this->search->to_date == null) {
				$this->search->to_date = _date_add(null, 31);
			}

			if ($this->search->from_time == null) {
				$this->search->from_time = "00:00";
			}
			else
				$this->search->from_time = _normalize_time($this->search->from_time);
			if ($this->search->to_time == null) {
				$this->search->to_time = "23:59";
			}
			else
				$this->search->to_time = _normalize_time($this->search->to_time);

			$w_and[] = "i.reserved_starttime>=" . _sql($this->search->from_date);
			$w_and[] = "i.reserved_starttime<=" . _sql($this->search->to_date . " 23:59:59");

			if ($this->search->from_time != "00:00") {
				$w_and[] = "TIME(i.reserved_starttime)>=" . _sql($this->search->from_time);
			}

			if ($this->search->to_time != "23:59") {
				$w_and[] = "TIME(i.reserved_starttime)<=" . _sql($this->search->to_time);
			}
			$w_and[] = "i.status=" . _sql(ISTATUS_PAYED);
			if (count($my_language_ids) > 0) {
				$w_and[] = "l.dlanguage_id IN (" . implode(",", $my_language_ids) . ")";
				$w_and[] = "l.planguage_id IN (" . implode(",", $my_language_ids) . ")";
			}

			$this->from = "FROM t_interview i 
					INNER JOIN t_interp l ON i.interview_id=l.interview_id ";
			$this->where = "WHERE " . implode(" AND ", $w_and);

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "i.interview_id DESC";

			$this->counts = $interview->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $interview->query("SELECT i.*, l.planguage_id, l.dlanguage_id " . $this->from . $this->where,
				array(
					"order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$interview->patient_name = userModel::get_user_name($interview->patient_id);
				$interview->doctor_name = userModel::get_user_name($interview->doctor_id);

				$interview->interp_lang_names = languageModel::get_language_names(array($interview->planguage_id, $interview->dlanguage_id));

				// 状态更新
				$interview->refresh_status();

				$interviews[] = clone $interview;

				$err = $interview->fetch();
			}

			$this->mInterviews = $interviews;

			$this->addjs("js/bootstrap-daterangepicker/moment.min.js");
			$this->addjs("js/bootstrap-daterangepicker/daterangepicker.js");
			$this->addjs("js/bootstrap-daterangepicker/daterangepicker." . _lang() . ".js");

		}

		public function reserve($doctor_id = null) {
			$this->_subnavi_menu = "interview_reserve";
			// insert
	        $now = new DateTime(); 
	        $tomorrow = $now->modify("+" . INTERVIEW_PATIENT_RESERVABLE_TIME_AFTER_NOW . " hours");
			$interview = new interviewModel;
			$interview->doctor_id = $doctor_id;
			$interview->need_interpreter = YN_YES;
			$interview->date = $tomorrow;
			$interview->startDate = $tomorrow;

			$doctor = userModel::get_model($interview->doctor_id);	
			if ($doctor == null)
				$this->check_error(ERR_NODATA);

			$interview->doctor_languages = ulangModel::get_languages($interview->doctor_id);

			// cost
			$interview->d_cost = $doctor->d_fee;
			$interview->i_cost = _round($doctor->d_fee * INTERPRETER_FEE / 100, 2);
			$interview->cunit = $doctor->d_cunit;

			$exrate = exrateModel::get_rate($interview->cunit);
			if ($exrate) {
				$interview->ex_cunit = $exrate[0];
				$interview->ex_rate = $exrate[1];
			}

			$this->mInterview = $interview;
			$this->mDoctor = $doctor;

			$this->addjs("js/bootstrap-wizard/jquery.bootstrap.wizard.min.js");
		}

		public function re_reserve($interview_id, $doctor_id = null) {
			$this->_subnavi_menu = "interview_list";
			$interview = interviewModel::get_model($interview_id);
			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($interview->status != ISTATUS_WAITING)
				$this->check_error(ERR_NOPRIV);

	        $now = new DateTime(); 
	        $tomorrow = $now->modify("+" . INTERVIEW_PATIENT_RESERVABLE_TIME_AFTER_NOW . " hours");
			$interview->doctor_id = $doctor_id;
			$interview->date = $tomorrow;
			$interview->startDate = $tomorrow;

			$chistory = chistoryModel::get_model($interview->chistory_id);
			if ($chistory) {
				$interview->chistory_name = $chistory->chistory_name;
				$interview->patient_name = $chistory->patient_name;
				$interview->patient_sex = $chistory->patient_sex;
				$interview->patient_birthday = $chistory->birthday;
				$interview->disease_name = diseaseModel::get_disease_name($chistory->disease_id);
				$interview->planguage_name = languageModel::get_language_name($interview->planguage_id);
			}
			else 
				$this->check_error(ERR_NODATA);

			$doctor = userModel::get_model($interview->doctor_id);	
			if ($doctor == null)
				$this->check_error(ERR_NODATA);

			$interview->doctor_languages = ulangModel::get_languages($interview->doctor_id);

			// cost
			$interview->d_cost = $doctor->d_fee;
			$interview->i_cost = _round($doctor->d_fee * INTERPRETER_FEE / 100, 2);
			$interview->cunit = $doctor->d_cunit;

			$exrate = exrateModel::get_rate($interview->cunit);
			if ($exrate) {
				$interview->ex_cunit = $exrate[0];
				$interview->ex_rate = $exrate[1];
			}

			$this->interview_id = $interview_id;
			$this->mInterview = $interview;
			$this->mDoctor = $doctor;

			$this->addjs("js/bootstrap-wizard/jquery.bootstrap.wizard.min.js");
		}

		public function detail($interview_id = null) {
			$this->_subnavi_menu = "interview_list";

			$interview = interviewModel::get_model($interview_id);
			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$this->interview_id = $interview_id;
			$this->mInterview = $interview;
		}

		public function prescription($interview_id = null, $trans_flag=null) {
			$this->_subnavi_menu = "interview_list";

			$interview = interviewModel::get_model($interview_id);
			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$dlanguage = languageModel::get_model($interview->dlanguage_id);

			if (_lang() == $dlanguage->language_code) {
				$chistory = chistoryModel::get_model($interview->trans_chistory_id);
				if ($chistory)
					$interview->patient_name = $chistory->patient_name;	
			}

			if (_is_empty($interview->patient_name)) {
				$chistory = chistoryModel::get_model($interview->chistory_id);
				if ($chistory)
					$interview->patient_name = $chistory->patient_name;	
			}

			$doctor = userModel::get_model($interview->doctor_id);
			if ($doctor) {
				$interview->doctor_name = $doctor->user_name;
				$interview->doctor_name_l = $doctor->user_name_l;
			}

			$this->mInterview = $interview;
			$this->trans_flag = $trans_flag;
		}

		public function save_ajax()
		{
			$param_names = array("interview_id",
				"patient_id", 					// 患者ID
				"doctor_id",					// 专家ID
				"reserved_starttime",			// 会诊开始预定时刻
				"reserved_endtime",				// 会诊完毕预定时刻
				"chistory_id",					// 病历
				"planguage_id",					// 患者精通语言
				"need_interpreter",				// 是否需要翻译
			);
			$this->set_api_params($param_names);
			$this->check_required(array(
				"doctor_id",					// 专家ID
				"reserved_starttime",			// 会诊开始预定时刻
				"reserved_endtime",				// 会诊完毕预定时刻
				"chistory_id",					// 病历
				"planguage_id"					// 患者精通语言
			));
			$params = $this->api_params;
			$this->start();

			$my_type = _my_type();
			$interview_id = $params->interview_id;
			if ($interview_id == null) {
				// 预约
				$interview = new interviewModel;
				if ($my_type == UTYPE_PATIENT) {
					// set self
					$params->patient_id = _my_id();
				}
				else if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER)
				{
					//$params->patient_id = $params->patient_id;
				}
				else 
					$this->check_error(ERR_NOPRIV);
			}
			else {
				$interview = interviewModel::get_model($interview_id);

				if ($interview == null)
					$this->check_error(ERR_NODATA);
			}

			$ihtype = null;

			if ($interview->status == null || 
				$interview->status == ISTATUS_WAITING) {
				$doctor = userModel::get_model($params->doctor_id);
				if (!$doctor || !($doctor->user_type == UTYPE_DOCTOR))
					$this->check_error(ERR_NODATA);

				if ($interview->status == null) {
					// 新预约
					$interview->status = ISTATUS_NONE;
					$ihtype = IHTYPE_RESERVED;
				}
				else if ($interview->status == ISTATUS_WAITING) {
					// 重新预约
					$interview->status = ISTATUS_OPENED;
					$interview->sub_status |= SISTATUS_D_MUST_ACCEPT_PATIENT;
					$ihtype = IHTYPE_RERESERVED;
				}

				$interview->cunit = $doctor->d_cunit;
				$interview->d_cost = $doctor->d_fee;
				if (!$params->need_interpreter) {
					$interview->i_cost = _round($doctor->d_fee * INTERPRETER_FEE / 100, 2);
				}
				$interview->cost = $interview->d_cost - $interview->i_cost;
				if ($interview->cost < 0)
					$interview->cost = 0;
			}

			$interview->load($params);

			$interview->interview_seconds = 0;

			// validate
			$this->check_error($interview->check_already());
			$this->check_error($interview->check_dtime());

			$dlanguage_ids = ulangModel::get_languages($interview->doctor_id, true);
			if (count($dlanguage_ids)) {
				$interview->dlanguage_id = $dlanguage_ids[0];
			}

			$err = $interview->save();
			$this->check_error($err);

			$err = $interview->save_interp($interview->planguage_id, $dlanguage_ids);
			$this->check_error($err);

			if ($ihtype !== null) {
				// insert
				logIhistoryModel::log($interview, $ihtype);
			}

			if ($err == ERR_OK)
			{
				if ($interview_id == null) 
					_opr_log("新增预约成功 会诊开始预定:".$params->reserved_starttime." 预约编号:" . $interview->interview_id);
				else
					_opr_log("预约更改(" . join(',', $interview->dirties) . ")成功 预约编号:" . $interview->interview_id);
			}

			$this->finish(array("interview_id" => $interview->interview_id), $err);
		}

		public function change_time($interview_id) {
			$this->_subnavi_menu = "interview_list";

			$interview = interviewModel::get_model($interview_id);
			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if (_my_id() != $interview->doctor_id)
				$this->check_error(ERR_NOPRIV);

			if (!($interview->status == ISTATUS_PAYED ||
				$interview->status == ISTATUS_OPENED ||
				$interview->status == ISTATUS_UNFINISHED))
				$this->check_error(ERR_NOPRIV);

			$reserved_date = _date($interview->reserved_starttime);
			$min_date = _time_add(null, INTERVIEW_DOCTOR_CHANGABLE_TIME_AFTER_NOW, 'Y-m-d');
			if ($reserved_date < $min_date)
				$reserved_date = $min_date;
			$interview->reserved_date = $reserved_date;

			$this->interview_id = $interview_id;
			$this->mInterview = $interview;
		}

		public function change_time_ajax()
		{
			$param_names = array("interview_id",
				"reserved_starttime",			// 会诊开始预定时刻
				"reserved_endtime",				// 会诊完毕预定时刻
				"doctor_sign"
			);
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$interview_id = $params->interview_id;
			$my_id = _my_id();
			$interview = interviewModel::get_model($interview_id);
			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($my_id != $interview->doctor_id)
				$this->check_error(ERR_NOPRIV);

			if (!($interview->status == ISTATUS_PAYED ||
				$interview->status == ISTATUS_OPENED ||
				$interview->status == ISTATUS_UNFINISHED))
				$this->check_error(ERR_NOPRIV);

			$org_reserved_datetime = _date($interview->reserved_starttime, "Y-m-d H:i") . "-" . _date($interview->reserved_endtime, "H:i");

			$interview->org_reserved_starttime = $interview->reserved_starttime;
			$interview->load($params);
			$interview->change_time_at = _date_time();

			if ($interview->status == ISTATUS_UNFINISHED)
			{	
				// 未完成
				if ($interview->need_interpreter &&
					$interview->doctor_starttime == null &&
					$interview->interpreter_starttime == null)
				{
					// 由于专家和翻译原因更改会诊时间
					$log_data = 2;
				}
				else {
					// 由于专家原因更改时间
					// 由于翻译原因更改时间
					$log_data = 1;
				}			
				$interview->status = ISTATUS_OPENED;
				$interview->interview_starttime = null;
				$interview->interview_endtime = null;
				$interview->interview_seconds = null;
				$interview->patient_status = null;
				$interview->patient_starttime = null;
				$interview->patient_leavetime = null;
				$interview->patient_ip = null;
				$interview->doctor_status = null;
				$interview->doctor_starttime = null;
				$interview->doctor_leavetime = null;
				$interview->doctor_ip = null;
				$interview->interpreter_status = null;
				$interview->interpreter_starttime = null;
				$interview->interpreter_leavetime = null;
				$interview->interpreter_ip = null;
			}
			else {
				$log_data = null;
			}

			// validate
			$this->check_error($interview->check_already());

			$err = $interview->update();

			if ($err == ERR_OK) {
				logIhistoryModel::log($interview, IHTYPE_CHANGED_TIME, $org_reserved_datetime, $log_data);

				$dtime = new dtimeModel;
				$dtime->remove_where("doctor_id=" . _sql($my_id) . " AND start_time=" . _sql($interview->org_reserved_starttime), true);
			}

			if ($err == ERR_OK)
			{
				_opr_log("预约更改时间成功 预约编号:" . $interview_id);
			}

			$this->finish(null, $err);
		}

		public function delete_ajax() {
			$param_names = array("interview_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$interview_id = $params->interview_id;

			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$interview->remove_attaches();

			$err = $interview->remove();

			if ($err == ERR_OK)
			{
				_opr_log("预约删除成功 预约编号:" . $interview_id);
			}

			$this->finish(null, $err);
		}

		public function invite_ajax() {
			$param_names = array("interview_id", "interpreter_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_id = _my_id();
			$my_type = _my_type();

			$interview_id = $params->interview_id;

			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$interpreter = userModel::get_model($params->interpreter_id);

			if ($interpreter == null)
				$this->check_error(ERR_NODATA);
			
			if (!$interview->can_invite())
				$this->check_error(ERR_NOPRIV);

			if ($interview->interpreter_id == $params->interpreter_id)
				$this->check_error(ERR_INVITE_SAME);
			
			$interview->interpreter_id = $params->interpreter_id;

			$old_status = $interview->status;
			if ($interview->status != ISTATUS_UNFINISHED)
				$interview->status = ISTATUS_OPENED;

			$err = $interview->update();
			$this->check_error($err);

			logIhistoryModel::log($interview, IHTYPE_INVITE_INTERP, $interpreter->user_name, $old_status);

			if ($err == ERR_OK)
			{
				_opr_log("指派翻译成功 预约编号:" . $interview_id." 翻译编号:".$interview->interpreter_id);
			}

			$this->finish(null, $err);
		}

		public function accept_patient_ajax() {
			$param_names = array("interview_id", "accept", "reject_cause_id", "reject_cause_note");
			$this->set_api_params($param_names);
			$this->check_required(array("interview_id", "accept"));
			$params = $this->api_params;
			$this->start();

			$interview_id = $params->interview_id;

			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);
			if ($interview->status != ISTATUS_OPENED)
				$this->check_error(ERR_NOPRIV);

			if ($params->accept == 1) {
				// 接受患者
				$interview->sub_status &= (~SISTATUS_D_MUST_ACCEPT_PATIENT);
				$err = $interview->update(array("sub_status"));

				if ($err == ERR_OK)
				{
					logIhistoryModel::log($interview, IHTYPE_ACCEPTED_PATIENT, _my_name());

					_opr_log("专家接受患者成功 预约编号:" . $interview_id);
				}
			}
			else {
				// 拒绝患者
				if ($params->reject_cause_id == RCAUSE_OTHER)
					$cause = $params->reject_cause_note;
				else
					$cause = _code_label(CODE_RCAUSE, $params->reject_cause_id);

				$interview->load($params);
				$interview->status = ISTATUS_WAITING;
				$interview->sub_status &= (~SISTATUS_D_MUST_ACCEPT_PATIENT);
				$interview->reserved_starttime = null;
				$interview->doctor_id = null;
				$interview->cost = null;
				$interview->d_cost = null;
				$interview->i_cost = null;
				$interview->cunit = null;
				$err = $interview->update();

				if ($err == ERR_OK)
				{
					logIhistoryModel::log($interview, IHTYPE_REJECTED_PATIENT, _my_name(), $cause);

					_opr_log("专家拒绝患者成功 预约编号:" . $interview_id);
				}
			}

			$this->finish(null, $err);
		}

		public function cancel_ajax() {
			$param_names = array("interview_id", "cancel_cause_id", "cancel_cause_note");
			$this->set_api_params($param_names);
			$this->check_required(array("interview_id", "cancel_cause_id"));
			$params = $this->api_params;
			$this->start();

			$my_id = _my_id();
			$my_type = _my_type();

			$interview_id = $params->interview_id;

			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($params->cancel_cause_id == CCAUSE_OTHER)
				$cause = $params->cancel_cause_note;
			else
				$cause = _code_label(CODE_CCAUSE, $params->cancel_cause_id);

			$penalty = 0;
			$refund_note = null;
			if ($my_id == $interview->patient_id) {
				if ($interview->status == ISTATUS_NONE)
					$ihtype = IHTYPE_PATIENT_CANCELED;
				else {
					$ihtype = IHTYPE_PATIENT_PAY_CANCELED;
					$penalty = $interview->cost;
					$refund_note = _l("患者取消订单");
				}
			}
			else if ($my_id == $interview->doctor_id){
				$ihtype = IHTYPE_DOCTOR_CANCELED;
				$refund_note = _l("专家取消会诊");
			}
			else if ($my_id == $interview->interpreter_id) {
				$ihtype = IHTYPE_INTERP_CANCELED;
				if ($interview->status == ISTATUS_OPENED) {
					$now = _DateTime(); 
					$limit_time = _DateTime($this->reserved_starttime);
					$diff = floor(($limit_time->getTimestamp() - $now->getTimestamp()) / 3600);
					$penalty = 0;
					if ($diff < INTERVIEW_I_CANCEL_LIMIT)
					{
						$penalty = $interview->cost;
					}

					$interview->status = ISTATUS_PAYED;
					$interview->interpreter_id = null;

					$err = $interview->update();
					if ($err == ERR_OK) {
						logIhistoryModel::log($interview, $ihtype, $cause, _cunit($interview->cunit) . $penalty);
					}

					$this->finish(null, $err);
				}
				else 
					$this->check_error(ERR_NOPRIV);
			}
			else if ($my_type == UTYPE_ADMIN || $my_type == UTYPE_SUPER) 
				$ihtype = IHTYPE_ADMIN_CANCELED;
			else 
				$this->check_error(ERR_NOPRIV);

			switch ($interview->status) {
				case ISTATUS_NONE:
				case ISTATUS_PAYED:
				case ISTATUS_OPENED:
					$interview->status = ISTATUS_CANCELED;
					if ($ihtype == IHTYPE_PATIENT_CANCELED)
						$interview->sub_status = SISTATUS_CANCEL_BEFORE_PAY;
					$interview->load($params);
					$err = $interview->update();
					if ($err == ERR_OK) {
						logIhistoryModel::log($interview, $ihtype, $cause, _cunit($interview->cunit) . $penalty);

						// if ($refund_note){
						// 	$interview->refund_penalty($penalty, $refund_note);
						// }
					}
					break;

				default:
					$err = ERR_OK;
					break;
			}
					
			_opr_log("取消订单成功 预约编号:" . $interview_id);

			$this->finish(null, $err);
		}

		public function close_ajax() {
			$param_names = array("interview_id", "cancel_cause_note");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_id = _my_id();
			$my_type = _my_type();

			$interview_id = $params->interview_id;

			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$cause = $params->cancel_cause_note;
			$interview->status = ISTATUS_CANCELED;
			$interview->load($params);
			$err = $interview->update();
			if ($err == ERR_OK) {
				logIhistoryModel::log($interview, IHTYPE_CLOSE, $cause);
			}

			_opr_log("关闭订单成功 预约编号:" . $interview_id);

			$this->finish(null, $err);
		}

		public function refund_ajax() {
			$param_names = array("interview_id", "refund_amount", "refund_note");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_id = _my_id();
			$my_type = _my_type();

			$interview_id = $params->interview_id;

			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$err = $interview->refund($params->refund_amount, $params->refund_note);

			$this->finish(null, $err);
		}


		public function bid_ajax() {
			$param_names = array("interview_id", "reserved_starttime");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_id = _my_id();

			$interview_id = $params->interview_id;

			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($interview->status == ISTATUS_CANCELED)
				$this->check_error(ERR_CANCELED_INTERVIEW);
			else if ($interview->status == ISTATUS_CANCELED)
				$this->check_error(ERR_FINISHED_INTERVIEW);
			else if (!($interview->status == ISTATUS_PAYED || $interview->status == ISTATUS_OPENED))
				$this->check_error(ERR_NOPRIV);

			if ($interview->interpreter_id)
				$this->check_error(ERR_ALREADY_BID_INTERVIEW); // already bid

			if (_date_time($interview->reserved_starttime) != _date_time($params->reserved_starttime))
				$this->check_error(ERR_ALREADY_CHANGED_TIME); // already change time

			$interview->interpreter_id = $my_id;
			$interview->status = ISTATUS_OPENED;
			$err = $interview->update(array("interpreter_id", "status"));

			if ($err == ERR_OK) {
				logIhistoryModel::log($interview, IHTYPE_INTERP_ACCEPT, _my_name());
				_opr_log("翻译接单成功 预约编号:" . $interview_id." 翻译编号:".$my_id);
			}

			$this->finish(null, $err);
		}

		public function upload_prescription_ajax()
		{
			$param_names = array("interview_id", "prescription");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$interview_id = $params->interview_id;
			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$interview->prescription = $params->prescription;
			$interview->trans_prescription = null;
			$interview->save_attaches("prescription");

			$err = $interview->update(array("prescription", "trans_prescription"));

			if ($err == ERR_OK) {
				// insert
				logIhistoryModel::log($interview, IHTYPE_UPLOAD_PRESCRIPT);
				_opr_log("上传第二诊疗意见成功 预约编号:" . $interview_id);
			}

			$this->finish(array("interview_id" => $interview->interview_id), $err);
		}

		public function upload_trans_prescription_ajax()
		{
			$param_names = array("interview_id", "trans_prescription");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$interview_id = $params->interview_id;
			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$interview->trans_prescription = $params->trans_prescription;
			$interview->save_attaches("trans_prescription");

			$err = $interview->update("trans_prescription");

			if ($err == ERR_OK) {
				// insert
				logIhistoryModel::log($interview, IHTYPE_UPLOAD_T_PRESCRIPT, _my_name());
				_opr_log("上传翻译版第二诊疗意见成功 预约编号:" . $interview_id);
			}

			$this->finish(array("interview_id" => $interview->interview_id), $err);
		}

		public function doctors($re_interview_id=null, $page = 0, $size = 20) 
		{
			$this->_subnavi_menu = "interview_reserve";
			$doctors = array();
			$doctor = new userModel;

			$my_id = _my_id();
			$my_type = _my_type();

			if ($my_type == UTYPE_PATIENT) {
				$my_diseases = chistoryModel::disease_ids($my_id);
			}
			else {
				$my_diseases = null;	
			}
			$exrate = exrateModel::get_last();
			
			$this->search = new reqsession("interview_doctors");

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "u.user_id ASC";

			$from = "FROM m_user u ";
			$join_hospital = false;

			$where = "u.del_flag=0 AND u.d_fee>0 AND u.user_type=" . UTYPE_DOCTOR;
			if (!_is_empty($this->search->hospital_id)) {
				$from .= " LEFT JOIN v_uhospital h ON u.user_id=h.user_id";
				$join_hospital = true;
				$where .= " AND h.hospital_id=" . _sql($this->search->hospital_id);
			}
			if (!_is_empty($this->search->hcountry_id)) {
				if (!$join_hospital)
					$from .= " LEFT JOIN v_uhospital h ON u.user_id=h.user_id";
				$where .= " AND h.hcountry_id=" . _sql($this->search->hcountry_id);
			}
			if (!_is_empty($this->search->disease_id)) {
				$from .= " LEFT JOIN m_udisease ud ON u.user_id=ud.user_id";
				$where .= " AND ud.disease_id=" . _sql($this->search->disease_id);
			}
			$this->counts = $doctor->scalar("SELECT COUNT(DISTINCT u.user_id) " . $from, 
				array("where" => $where));

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$select = "SELECT DISTINCT u.user_id, u.user_name, u.d_title, u.mobile, u.d_fee, u.d_cunit ";
			$err = $doctor->query($select . $from,
				array("where" => $where,
					"order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$match = false;
				$doctor->d_title_l = stringModel::get_string_l($doctor->d_title);
				$doctor->diseases = udiseaseModel::get_diseases($doctor->user_id, false, $my_diseases, $match);

				$doctor->show_d_fee = $match;

				if ($doctor->d_cunit == "rmb") {
					$doctor->ex_d_cunit = "usd";
					$doctor->ex_d_fee = _round($doctor->d_fee * $exrate->rmb_to_usd, 2);
				}
				else if ($doctor->d_cunit == "usd") {
					$doctor->ex_d_cunit = "rmb";
					$doctor->ex_d_fee = _round($doctor->d_fee * $exrate->usd_to_rmb, 2);
				}

				$doctors[] = clone $doctor;

				$err = $doctor->fetch();
			}

			$this->mHcountries = hospitalModel::all_tree($this->search->hcountry_id);
			$this->mDiseases = diseaseModel::get_all(false);
			$this->mDoctors = $doctors;
			$this->re_interview_id = $re_interview_id;
		}

		public function pay($interview_id)
		{
			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			$pay_time = date('YmdHis');
			$front_respond_url = _abs_url("interview/pay_finish/".$interview_id."/".$pay_time);
			$back_respond_url = _abs_url("pay/respond/".$interview_id."/".$pay_time);

			$exrate = exrateModel::get_last();

			if ($exrate) {
				if ($interview->cunit == "rmb") {
					$interview->ex_cunit = "usd";
					$interview->ex_cost = _round($interview->cost * $exrate->rmb_to_usd, 2);

					$rmb_cost = $interview->cost;
					$usd_cost = $interview->ex_cost;
				}
				else if ($interview->cunit == "usd") {
					$interview->ex_cunit = "rmb";
					$interview->ex_cost = _round($interview->cost * $exrate->usd_to_rmb, 2);

					$rmb_cost = $interview->ex_cost;
					$usd_cost = $interview->cost;
				}
			}
			
			$this->pay_check_url = _abs_url("interview/pay_check/$interview_id");
			$this->pay_finish_url = _abs_url("interview");
			
			// chinapay button
			$chinapay = paybase::get_from_pay_id('chinapay');
			$this->chinapay_button = $chinapay->get_button($interview_id, $rmb_cost, $front_respond_url."/chinapay/".base64_encode($rmb_cost), $back_respond_url."/chinapay/".base64_encode($rmb_cost), $pay_time);

			// paypal button
			$paypal = paybase::get_from_pay_id('paypal');
			$this->paypal_button = $paypal->get_button($interview_id, $usd_cost, $front_respond_url."/paypal/".base64_encode($usd_cost), $back_respond_url."/paypal/".base64_encode($usd_cost), $pay_time);

			$interview->contract = _fread(SITE_ROOT.'/resource/txt/contract_interview.'._lang().'.txt');

			$this->mInterview = $interview;
		}
		
		public function pay_check($interview_id) {
			$interview = interviewModel::get_model($interview_id);
			$this->finish(array("payed" => $interview && $interview->is_payed()), ERR_OK);
		}
		
		public function pay_finish($interview_id, $pay_time, $pay_id, $cost)
		{
			$paymentId = $_GET['paymentId'];
			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);
			if (!$interview->is_payed()) {
				$err = $interview->pay($pay_id, $pay_time, $cost, $paymentId);
				$this->check_error($err);
			}
			$this->mInterview = $interview;

			_opr_log("支付$paymentId 成功 预约编号:" . $interview_id);

			if (DEBUG_PAY == 2) // 无使用在线支付，经常支付成功
				_goto("interview");
		}

		public function room($interview_id, $quality="m")
		{
			$interview = interviewModel::get_model($interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($interview->status != ISTATUS_OPENED && $interview->status != ISTATUS_PROGRESSING) {
				_goto("interview");
			}

			$interview->refresh_status();
			
			if ($interview->before_starttime)
				$this->check_error(ERR_BEFORE_STARTTIME);

			$my_type = _my_type();
			$my_id = _my_id();
			
			// update IPs
			if ($interview->patient_id == $my_id) {
				$interview->patient_ip = _ip();
			}
			else if ($interview->doctor_id == $my_id) {
				$interview->doctor_ip = _ip();
			}
			else if ($interview->interpreter_id == $my_id) {
				$interview->interpreter_ip = _ip();
			}
			else if ($my_type != UTYPE_ADMIN && $my_type != UTYPE_SUPER) {
				$this->check_error(ERR_NOPRIV);
			}

			$err = $interview->update();

			$dlanguage = languageModel::get_model($interview->dlanguage_id);

			$chistory_id = $interview->chistory_id;
			if (_lang() == $dlanguage->language_code &&
				$interview->need_interpreter &&
				$interview->trans_chistory_id)
			{
				$chistory_id = $interview->trans_chistory_id;
			}
			$chistory = chistoryModel::get_model($chistory_id);
			if ($chistory)
				$interview->patient_name = $chistory->patient_name;
			$doctor = userModel::get_model($interview->doctor_id);
			if ($doctor) {
				$interview->doctor_name = $doctor->user_name;
				$interview->doctor_name_l = $doctor->user_name_l;
			}
			$interpreter = userModel::get_model($interview->interpreter_id); 
			if ($interpreter) {
				$interview->interpreter_name = $interpreter->user_name;
				$interview->interpreter_name_l = $interpreter->user_name_l;
			}
			$interview->quality = $quality;

			$this->mInterview = $interview;

			$this->mImessages = imessageModel::all($interview_id, _my_type());

			$this->addjs("js/webrtc/RecordRTC.min.js");
			$this->addjs(_js_url("js/room"));

			return "room/";
		}

		public function enter_ajax()
		{
			$param_names = array("interview_id", "user_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_type = _my_type();
			$my_id = _my_id();

			$interview = interviewModel::get_model($params->interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($interview->status != ISTATUS_OPENED && $interview->status != ISTATUS_PROGRESSING)
				$this->check_error(ERR_NOPRIV);

			$interview->refresh_status();
			
			if ($interview->before_starttime)
				$this->check_error(ERR_BEFORE_STARTTIME);

			$err = $interview->start($my_id, $my_type);
			$this->check_error($err);

			$this->finish(array("doctor_offline_yet" => $interview->doctor_offline_yet), ERR_OK);
		}

		public function leave_ajax()
		{
			$param_names = array("interview_id", "user_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_type = _my_type();
			$my_id = _my_id();

			$interview = interviewModel::get_model($params->interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($interview->status != ISTATUS_OPENED && $interview->status != ISTATUS_PROGRESSING && $interview->status != ISTATUS_FINISHED && $interview->status != ISTATUS_CANCELED)
				$this->check_error(ERR_NOPRIV);

			$err = $interview->leave($my_id);
			$this->check_error($err);

			$this->finish(null, ERR_OK);
		}

		public function finish_ajax()
		{
			$param_names = array("interview_id", "users");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_type = _my_type();
			$my_id = _my_id();

			$interview = interviewModel::get_model($params->interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($interview->status != ISTATUS_OPENED && $interview->status != ISTATUS_PROGRESSING)
				$this->check_error(ERR_NOPRIV);

			$err = $interview->finish($my_id);
			$this->check_error($err);

			if (RECORDING_API != '')
			{
				if ($interview->is_must_record()) {
					// start converting of video
					$url = RECORDING_API . "api/v/e?interview_id=" . $params->interview_id;
					_open_url($url);
				}
				else {
					// clear temporary video
					$url = RECORDING_API . "api/v/c?interview_id=" . $params->interview_id;
					_open_url($url);
				}
			}

			$this->finish(null, ERR_OK);
		}

		public function send_message_ajax()
		{
			$param_names = array("interview_id", "content", "no");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_id = _my_id();
			$interview = interviewModel::get_model($params->interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($interview->status != ISTATUS_PROGRESSING)
				$this->check_error(ERR_NOPRIV);

			if (!($interview->patient_id == $my_id ||
				$interview->doctor_id == $my_id ||
				$interview->interpreter_id == $my_id))
				$this->check_error(ERR_NOPRIV);

			$imessage = new imessageModel;

			$imessage->interview_id = $params->interview_id;
			$imessage->user_id = $my_id;
			$imessage->user_type = _my_type();
			$imessage->content = $params->content;
			$imessage->read_flag = $imessage->user_type;
			$imessage->no = $params->no;

			$err = $imessage->save();

			$this->finish(null, $err);
		}

		public function change_cost_ajax()
		{
			$param_names = array("interview_id", "cost");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$my_id = _my_id();
			$interview = interviewModel::get_model($params->interview_id);

			if ($interview == null)
				$this->check_error(ERR_NODATA);

			if ($interview->status != ISTATUS_NONE)
				$this->check_error(ERR_NOPRIV);

			$interview->cost = $params->cost;
			$err = $interview->update("cost");

			if ($err == ERR_OK) {
				logIhistoryModel::log($interview, IHTYPE_CHANGED_COST, _cunit($interview->cunit) . $params->cost);
			}

			$this->finish(null, $err);
		}

		public function play($interview_id)
		{
			$this->interview_id = $interview_id;
			return "popup/";
		}

		public function status_ajax()
		{
			$param_names = array("interview_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$interview = interviewModel::get_model($params->interview_id);
			if ($interview)
				$this->finish(array("status" => $interview->status), ERR_OK);
			else
				$this->check_error(ERR_NODATA);
		}

		public function save_user_name_ajax()
		{
			$param_names = array(
				"patient_id", "patient_name_l",
		        "doctor_id", "doctor_name_l",
		        "interpreter_id", "interpreter_name_l"
	        );
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$patient_id = $params->patient_id;
			$user = userModel::get_model($patient_id);
			if ($user) 
			{
				$user->set_user_name_l($params->patient_name_l);
				$user->save();
			}

			$doctor_id = $params->doctor_id;
			$user = userModel::get_model($doctor_id);
			if ($user) 
			{
				$user->set_user_name_l($params->doctor_name_l);
				$user->save();
			}

			$interpreter_id = $params->interpreter_id;
			$user = userModel::get_model($interpreter_id);
			if ($user) 
			{
				$user->set_user_name_l($params->interpreter_name_l);
				$user->save();
			}

			$this->finish(null, ERR_OK);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/