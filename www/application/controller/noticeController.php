<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class noticeController extends controller {
		public function __construct() {
			parent::__construct();

			$this->_navi_menu = "sysman";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_SUPER);
					break;
			}
		}

		public function index($page = 0, $size = 20) {
			$this->_subnavi_menu = "sysman_notice";

			$notices = array();
			$notice = new noticeModel;
			$w_and = array();
			
			$this->search = new reqsession("sysman_notice");

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "notice_id DESC";

			$where = implode(" AND ", $w_and);

			$this->counts = $notice->counts($where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $notice->select($where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$notices[] = clone $notice;

				$err = $notice->fetch();
			}

			$this->mNotices = $notices;
			$this->mNotice = new noticeModel;
			$this->mLanguages = languageModel::get_all_code();
		}

		public function get_ajax()
		{
			$param_names = array("notice_id");
			$this->check_required($param_names);
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$notice_id = $params->notice_id;
			$notice = noticeModel::get_model($notice_id);

			if ($notice == null)
				$this->check_error(ERR_NODATA);

			$this->finish(array("notice" => $notice->props), ERR_OK);
		}

		public function save_ajax()
		{
			$param_names = array("notice_id", 
				"content");
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$notice_id = $params->notice_id;
			if ($notice_id == null) {
				$notice = new noticeModel;
			}
			else {
				$notice = noticeModel::get_model($notice_id);

				if ($notice == null)
					$this->check_error(ERR_NODATA);
			}

			$notice->load($params);

			$err = $notice->save();

			if ($err == ERR_OK)
				noticeModel::clear_cache();

			$this->finish(null, $err);
		}

		public function save_name_ajax()
		{
			$param_names = array("id", 
				"name_field",				// 字段名称
				"name_l");					// 多语言
			$this->check_required(array("id", "name_field"));
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$id = $params->id;
			$name_l = _json_encode($params->name_l);

			if ($params->name_field == "content") {
				$notice = noticeModel::get_model($id);

				if ($notice == null)
					$this->check_error(ERR_NODATA);

				$notice->content_l = $name_l;

				$err = $notice->save();

				if ($err == ERR_OK)
					noticeModel::clear_cache();
			}

			$this->finish(null, $err);
		}

		public function delete_ajax() {
			$param_names = array("notice_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$notice_id = $params->notice_id;

			$notice = noticeModel::get_model($notice_id);

			if ($notice == null)
				$this->check_error(ERR_NODATA);

			$err = $notice->remove();

			if ($err == ERR_OK)
				noticeModel::clear_cache();

			$this->finish(null, $err);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/