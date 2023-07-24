<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class vController extends controller {
		public function __construct(){
			parent::__construct();	

			$this->_navi_menu = "home";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_NONE);
					break;
			}
		}

		public function index() {
			exit;
		}

		// upload segment video
		public function p_ajax() {
			$param_names = array("interview_id", "user_type", "timestamp", "no");
			$this->set_api_params($param_names);
			$this->check_required($param_names);

			$params = $this->api_params;

			$interview_id = $params->interview_id;
			$user_type = $params->user_type;
			$timestamp = $params->timestamp / 1000; // javascript timestampe * 1000

			$dir = TMP_PATH . $interview_id . "/" . $user_type . "/" . $timestamp . "/";
			$path = $dir . sprintf("%05d", $params->no) . ".webm";

			@unlink($path);
			@_mkdir($dir);
			_upload("data", $path);

			$this->finish(null, ERR_OK);
		}

		// finish interview and start converting
		public function e_ajax() {
			$param_names = array("interview_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);

			$params = $this->api_params;

			$dir = TMP_PATH . $params->interview_id;

			if (is_dir($dir)) {
				$tm = time();
				$path = $dir . ".fin";

				_fwrite($path, $tm);
			}

			$this->finish(null, ERR_OK);
		}

		// remove unfinished video files
		public function c_ajax() {
			$param_names = array("interview_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);

			$params = $this->api_params;

			$dir = TMP_PATH . $params->interview_id;

			if (is_dir($dir)) {
				$tm = time();
				$path = $dir . ".clear";

				_fwrite($path, $tm);
			}

			$this->finish(null, ERR_OK);
		}

		// get status of converting
		public function s_ajax()
		{
			$param_names = array("interview_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);

			$params = $this->api_params;
			$interview_id = $params->interview_id;

			$video_url = _video_url($interview_id);
			$video_path = DATA_PATH . $video_url;

			$settings = settingsModel::get_config();			
			$recordable_date = _date_add(null, -$settings->save_record_limit, "Ymd");

			$db = db::get_db();
			$interview_date = $db->scalar("SELECT reserved_starttime FROM t_interview 
				WHERE interview_id=" . _sql($interview_id));

			$status = 0; // not exist
			if ($interview_date) {
				$interview_date = _date($interview_date, "Ymd");

				if ($recordable_date <= $interview_date) {
					if (filesize($video_path))
					{
						// complete
						$this->finish(array("status" => 10,
							"url"=> $video_url), ERR_OK);
					}
					else if (is_file(TMP_PATH . "$interview_id.fin"))
					{
						// converting
						$status = 2;
					}
					else if (is_dir(TMP_PATH . "$interview_id"))
					{
						// uploading from client
						$status = 1;
					}
				}
			}
			
			$this->finish(array("status" => $status), ERR_OK);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/