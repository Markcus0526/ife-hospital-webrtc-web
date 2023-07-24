<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class dtimeController extends controller {
		public function __construct() {
			parent::__construct();

			$this->_navi_menu = "dtime";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				case "list_ajax":
					parent::check_priv($action, UTYPE_LOGINUSER);
					break;
				default:
					parent::check_priv($action, UTYPE_DOCTOR);
					break;
			}
		}

		public function index($date=null) {
			$this->_subnavi_menu = "dtime_list";
			if ($date == null)
				$date = _time_add(null, INTERVIEW_DOCTOR_SETTABLE_TIME_AFTER_NOW, 'Y-m-d');
			$this->date = _date($date, "Y-m-d");
			$this->weekday = _date($date, "w");	
		}

		private function push_dtime(&$dtimes, $start_time, $state, $interview_id)
		{
			$target_date = _date($start_time);
			$d_start = _DateTime($start_time);
			$d_end = _DateTime($start_time)->modify("1 hour");
			$dtime = array(
				"target_date" => $target_date,
				"start_time" => _date_time($d_start),
				"end_time" => _date_time($d_end),
				"start_time_label" => _time($d_start),
				"end_time_label" => _time($d_end),
				"start_hour" => _time($d_start, "G"),
				"weekday" => _date($target_date, "w"),
				"state" => $state		
			);

			if ($state == TSTATE_RESERVED || $state == TSTATE_DISABLE || $state == TSTATE_UNRESERVABLE)
				$dtime["interview_id"] = $interview_id;

			for ($i = 0; $i < count($dtimes); $i ++)
			{
				if ($dtimes[$i]["start_time"] == $dtime["start_time"]) {
					$dtime["o_state"] = $dtimes[$i]["state"];
					if (isset($dtimes[$i]["o_state"]))
						$dtime["o_state"] = $dtimes[$i]["o_state"];
					$dtimes[$i] = $dtime;
					return;
				}
			}

			$dtimes[] = $dtime;
		}

		public function list_ajax() {
			$param_names = array("date", "doctor_id", "reserve", "change_reserve");
			$this->set_api_params($param_names);
			$params = $this->api_params;

			$my_type = _my_type();
			$date = _date($params->date);

			$week_start = _week_start($date);
			if ($params->reserve) {
				// 患者或管理员从会诊预约网页
				$start_date = max($date, _time_add(null, INTERVIEW_PATIENT_RESERVABLE_TIME_AFTER_NOW, 'Y-m-d')) . " 00:00:00";
				$min_start_date = _date_time(_DateTime()->modify(INTERVIEW_PATIENT_RESERVABLE_TIME_AFTER_NOW . ' hour'));
				$end_date = $date . " 23:59:59";
			}
			else if ($params->change_reserve){
				// 更改时间
				$start_date = max($week_start, _time_add(null, INTERVIEW_DOCTOR_CHANGABLE_TIME_AFTER_NOW, 'Y-m-d')) . " 00:00:00";
				$min_start_date = _date_time(_DateTime()->modify(INTERVIEW_DOCTOR_CHANGABLE_TIME_AFTER_NOW . ' hour'));
				$end_date = _week_end($date) . " 23:59:59";
			}
			else {
				// 时间管理
				$start_date = max($week_start, _time_add(null, INTERVIEW_DOCTOR_SETTABLE_TIME_AFTER_NOW, 'Y-m-d')) . " 00:00:00";
				$min_start_date = _date_time(_DateTime()->modify(INTERVIEW_DOCTOR_SETTABLE_TIME_AFTER_NOW . ' hour'));
				$end_date = _week_end($date) . " 23:59:59";
			}

			$doctor_id = $params->doctor_id;
			if ($my_type == UTYPE_DOCTOR && $doctor_id == null)
				$doctor_id = _my_id();

			// 可预约
			$dtimes = array(); 
			$sel_dtimes = array();
			$dtime = new dtimeModel;
			$where = "doctor_id=" . _sql($doctor_id) . 
				" AND start_time>=" . _sql($start_date) . 
				" AND start_time<=" . _sql($end_date);

			$err = $dtime->select($where, array("order" => "start_time ASC"));
			while ($err == ERR_OK) {
				$target_date = _date($dtime->start_time);
				if ($params->reserve && 
					$min_start_date >= $dtime->start_time)
				{
					$dtime->state = TSTATE_UNRESERVABLE;
				}

				if ($target_date == $date) {
					$this->push_dtime($sel_dtimes, $dtime->start_time, $dtime->state, null);
				}
				$this->push_dtime($dtimes, $dtime->start_time, $dtime->state, null);
				$err = $dtime->fetch();
			}

			// 已预约
			$interview = new interviewModel;
			$where = "status IN (" . implode(",", array(ISTATUS_NONE, ISTATUS_PAYED, ISTATUS_OPENED, ISTATUS_PROGRESSING)) . ")" .
				" AND doctor_id=" . _sql($doctor_id) . 
				" AND reserved_starttime>=" . _sql($start_date) . 
				" AND reserved_starttime<=" . _sql($end_date);
			$err = $interview->select($where, array("order" => "reserved_starttime ASC"));
			while ($err == ERR_OK) {
				$target_date = _date($interview->reserved_starttime);
				$state = TSTATE_RESERVED;
				if ($params->reserve) {
					if ($my_type == UTYPE_PATIENT || $my_type == UTYPE_ADMIN || $min_start_date >= $interview->reserved_starttime)
					{
						$state = TSTATE_UNRESERVABLE;
					}
				}

				if ($target_date == $date) {
					$this->push_dtime($sel_dtimes, $interview->reserved_starttime,$state , $interview->interview_id);
				}
				$this->push_dtime($dtimes, $interview->reserved_starttime, $state, $interview->interview_id);
				$err = $interview->fetch();
			}

			if (!$params->reserve)
			{
				$start_date = max($week_start . " 00:00:00", $min_start_date); 
			}

			$this->finish(array(
				"week_start" => $week_start,
				"start_date" => $start_date,
				"end_date" => $end_date,
				"dtimes" => $dtimes,
				"sel_dtimes" => $sel_dtimes,
				"clickable_start_date" => $min_start_date
			), ERR_OK);
		}

		public function state_ajax()
		{
			$param_names = array("date", "state", "start_time");
			$this->set_api_params($param_names);
			$params = $this->api_params;

			if ($params->date != "")
				$params->start_time = _date($params->date) . " " . _minutes2str($this->start_time) . ":00";
			$params->doctor_id = _my_id();

			if ($params->start_time && $params->state == TSTATE_DISABLE)
			{
				$interview = new interviewModel;
				$err = $interview->select('reserved_starttime=' . _sql($params->start_time) . 
					' AND doctor_id=' . _sql($params->doctor_id) .
					' AND status IN (' . implode(',', array(ISTATUS_PAYED, ISTATUS_OPENED, ISTATUS_PROGRESSING)) . ')');
				if ($err == ERR_OK)
				{
					$this->check_error(ERR_ALREADY_RESERVED_DTIME);
				}
			}

			$dtime = new dtimeModel;
			$where = "doctor_id=" . _sql($params->doctor_id) .  
				" AND start_time=" . _sql($params->start_time);

			$err = $dtime->select($where);

			if ($err == ERR_OK && $params->state == TSTATE_DISABLE) {
				// remove
				$err = $dtime->remove(true);
			}
			else {
				// insert update
				$dtime->load($params);

				$err = $dtime->save();
			}

			if ($err == ERR_OK)
			{
				_opr_log("专家时间更改成功 date:" . $params->date);
			}

			$this->finish(null, $err);
		}

	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/