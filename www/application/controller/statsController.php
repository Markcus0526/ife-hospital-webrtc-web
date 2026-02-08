<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class statsController extends controller {
		public function __construct(){
			parent::__construct();	

			$this->_navi_menu = "stats";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN);
					break;
			}
		}

		private function load_search()
		{
			$my_type = _my_type();

			// filtering
			$this->search = new reqsession("stats_index");
			$this->search->utypes = array(UTYPE_INTERPRETER, UTYPE_DOCTOR);
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			// 2.from - to date
			if ($this->search->from_date == null) {
				$this->search->from_date = _date(null, 'Y-01-01');
			}
			if ($this->search->to_date == null) {
				$this->search->to_date = _date_add(null, 31);
			}

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "u.user_id ASC";

			$w_and = array();
			$w_and[] = "u.del_flag=0 ";

			if (!_is_empty($this->search->query)) {
				$like = _sql("%" . $this->search->query . "%");
				$w_and[] = "(u.user_name LIKE " . $like  . " OR u.user_id LIKE " . $like  . " OR u.mobile LIKE " . $like . ")";
			}
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "u.country_id=" . _sql($this->search->country_id);
			}
			if (!_is_empty($this->search->user_type)) {
				$w_and[] = "u.user_type=" . _sql($this->search->user_type);
			}
			else {
				$w_and[] = "u.user_type IN (" . implode(",", $this->search->utypes) . ")";	
			}

			$this->where = "WHERE " . implode(" AND ", $w_and);

			$this->from = "FROM m_user u 
				LEFT JOIN t_interview i ON (i.patient_id=u.user_id OR i.doctor_id=u.user_id OR i.interpreter_id=u.user_id) ";
			$this->from .= " AND i.del_flag=0 AND i.status=" . ISTATUS_FINISHED;
			$this->from .= " AND i.reserved_starttime>=" . _sql($this->search->from_date);
			$this->from .= " AND i.reserved_starttime<=" . _sql($this->search->to_date . " 23:59:59");

			$this->group = "u.user_id, u.user_type, u.user_name, u.mobile, u.country_id";

		}

		public function index($page = 0, $size = 10) {
			$this->_subnavi_menu = "stats_main";
			$users = array();
			$user = new userModel;

			$this->load_search();	

			$this->counts = $user->scalar("SELECT COUNT(*) FROM (SELECT u.user_id " . $this->from . $this->where . " GROUP BY " . $this->group . ") a");		
			
			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $user->query("SELECT u.user_id, u.user_type, u.user_name, u.mobile, u.email, u.country_id, COUNT(interview_id) counts, SUM(interview_seconds) seconds " . $this->from . $this->where,
				array("order" => $this->order,
					"group" => $this->group,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				if ($user->seconds == null)
					$user->seconds = 0;
				$users[] = clone $user;

				$err = $user->fetch();
			}

			$this->mUsers = $users;

			$this->addjs("js/bootstrap-daterangepicker/moment.min.js");
			$this->addjs("js/bootstrap-daterangepicker/daterangepicker.js");
			$this->addjs("js/bootstrap-daterangepicker/daterangepicker." . _lang() . ".js");
		}

		public function excel()
		{
			include 'plugins/phpexcel/PHPExcel.php';
			$excel = PHPExcel_IOFactory::load(SITE_ROOT . "resource/xls/stats.xlsx");
    	    $excel->setActiveSheetIndex(0);

			$user = new userModel;

			$this->load_search();

			$err = $user->query("SELECT u.user_id, u.user_type, u.user_name, u.mobile, u.email, u.country_id, COUNT(interview_id) counts, SUM(interview_seconds) seconds " . $this->from . $this->where,
				array("order" => $this->order,
					"group" => $this->group,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));
			$row = 2; 
	        $excel->getActiveSheet()->setCellValue("B".$row, _l("次数与时长"));
	        $row ++; 
	        $excel->getActiveSheet()->setCellValue("B".$row, _l("序号"));
	        $excel->getActiveSheet()->setCellValue("C".$row, _l("国家"));
	        $excel->getActiveSheet()->setCellValue("D".$row, _l("角色"));
	        $excel->getActiveSheet()->setCellValue("E".$row, _l("姓名"));
	        $excel->getActiveSheet()->setCellValue("F".$row, _l("手机号"));
	        $excel->getActiveSheet()->setCellValue("G".$row, _l("电子邮箱"));
	        $excel->getActiveSheet()->setCellValue("H".$row, _l("会诊次数"));
	        $excel->getActiveSheet()->setCellValue("I".$row, _l("会诊时长"));

			$row ++; $i = 1;
			while ($err == ERR_OK)
			{
		        $excel->getActiveSheet()->setCellValue("B".$row, "$i");
		        $excel->getActiveSheet()->setCellValue("C".$row, countryModel::get_country_name($user->country_id));
		        $excel->getActiveSheet()->setCellValue("D".$row, _code_label(CODE_UTYPE, $user->user_type));
		        $excel->getActiveSheet()->setCellValue("E".$row, $user->user_name);
		        $excel->getActiveSheet()->setCellValue("F".$row, $user->mobile);
		        $excel->getActiveSheet()->setCellValue("G".$row, $user->email);
		        $excel->getActiveSheet()->setCellValue("H".$row, $user->counts);
		        $excel->getActiveSheet()->setCellValue("I".$row, _seconds_label($user->seconds, ''));

				$err = $user->fetch();
				$row ++; $i ++;
			}

			$path = _tmp_path("xlsx");
            _erase_old(TMP_PATH);
            try { 	
                $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
                $writer->save($path);
            }catch(Exception $e) {
                //showErrorMessage(msgPRINTFILEError, $current_url);
            }

            $sz = filesize($path);
            $fp = fopen($path, "rb");
            if ($fp) {
                ob_end_clean();
                header('Content-type: application/vnd.ms-excel');
                $file_name = "stats.xls";
                if (ISIE7 || ISIE8) {
                    header('Content-Disposition: attachment; filename=' . urlencode($file_name)); //プロファイル
                }
                if (ISSafari) {
                    header('Content-Disposition: attachment; filename="' . $file_name . '"'); //プロファイル
                }
                else {
                    header('Content-Disposition: attachment; filename="' . $file_name . '"; filename*=UTF-8\'\'' . urlencode($file_name)); //プロファイル
                }
                header("Content-Length: " . $sz);
                fpassthru($fp);
            }

            exit;
		}

		public function detail($user_id)
		{
			$this->_subnavi_menu = "stats_main";
			$interviews = array();
			$interview = new interviewModel;

			$user = userModel::get_model($user_id);
			if ($user == null)
				$this->check_error(ERR_NODATA);
			
			// filtering
			$this->search_index = new reqsession("stats_index");
			$this->search = new reqsession("stats_detail");

			// 2.from - to date
			if ($this->search_index->from_date == null) {
				$this->search_index->from_date = _date(null, 'Y-01-01');
			}
			if ($this->search_index->to_date == null) {
				$this->search_index->to_date = _date_add(null, 31);
			}

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "i.interview_id ASC";

			$w_and = array();
			$w_and[] = "i.del_flag=0 AND i.status=" . ISTATUS_FINISHED;
			$w_and[] = "i.reserved_starttime>=" . _sql($this->search_index->from_date);
			$w_and[] = "i.reserved_starttime<=" . _sql($this->search_index->to_date . " 23:59:59");
			$w_and[] = "(i.patient_id=" . _sql($user_id) . " OR i.doctor_id=" . _sql($user_id) . " OR i.interpreter_id=" . _sql($user_id) . ")";

			$this->where = " WHERE " . implode(" AND ", $w_and);

			$this->from = " FROM t_interview i 
				LEFT JOIN m_user p ON i.patient_id=p.user_id
				LEFT JOIN m_user d ON i.doctor_id=d.user_id
				LEFT JOIN m_user n ON i.interpreter_id=n.user_id ";

			$this->counts = $interview->scalar("SELECT COUNT(*) " . $this->from . $this->where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $interview->query("SELECT i.*, p.user_name patient_name, d.user_name doctor_name, n.user_name interpreter_name " . $this->from . $this->where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$interview->logs = logIhistoryModel::get_logs($interview->interview_id);

				$interviews[] = clone $interview;

				$err = $interview->fetch();
			}

			$this->mInterviews = $interviews;
			$this->user_id = $user_id;
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/