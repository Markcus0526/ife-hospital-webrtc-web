<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class feedbackController extends controller {
		public function __construct(){
			parent::__construct();	

			$this->_navi_menu = "feedback";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN);
					break;
			}
		}

		public function index($page = 0, $size = 10) {
			$this->_subnavi_menu = "feedback_list";
			$feedbacks = array();
			$feedback = new feedbackModel;
			
			$my_type = _my_type();
			
			// filtering
			$this->search = new reqsession("feedback_list");
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "feedback_id DESC";

			$w_and = array();
			// 2.from - to date
			if ($this->search->from_date == null) {
				$this->search->from_date = _date(null, 'Y-01-01');
			}
			if ($this->search->to_date == null) {
				$this->search->to_date = _date_add(null, 31) . " 23:59:59";
			}

			$this->from = "FROM t_feedback f 
				LEFT JOIN m_user u ON f.user_id=u.user_id ";

			$w_and[] = "f.create_time>=" . _sql($this->search->from_date);
			$w_and[] = "f.create_time<=" . _sql($this->search->to_date . " 23:59:59");

			if ($this->search->user_type) {
				$w_and[] = "f.user_type=" . _sql($this->search->user_type);	
			}
			if ($this->search->status !== null) {
				$w_and[] = "f.status=" . _sql($this->search->status);
			}
			if ($this->search->country_id) {
				$w_and[] = "u.country_id=" . _sql($this->search->country_id);
			}

			$this->where = "WHERE " . implode(" AND ", $w_and);

			$this->counts = $feedback->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $feedback->query("SELECT f.*, u.user_name, u.country_id " . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$feedbacks[] = clone $feedback;

				$err = $feedback->fetch();
			}

			$this->mFeedbacks = $feedbacks;

			$this->addjs("js/bootstrap-daterangepicker/moment.min.js");
			$this->addjs("js/bootstrap-daterangepicker/daterangepicker.js");
			$this->addjs("js/bootstrap-daterangepicker/daterangepicker." . _lang() . ".js");
		}

		public function read_ajax()
		{
			$param_names = array("feedback_ids");
			$this->set_api_params($param_names);
			$params = $this->api_params;

			foreach($params->feedback_ids as $feedback_id) {
				$feedback = feedbackModel::get_model($feedback_id);
				if ($feedback == null)
					$this->check_error(ERR_NODATA);

				$feedback->status = FSTATUS_READ;

				$err = $feedback->save();
			}
								
			$this->finish(null, ERR_OK);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/