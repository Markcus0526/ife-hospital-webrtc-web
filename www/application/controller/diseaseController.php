<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class diseaseController extends controller {
		public function __construct() {
			parent::__construct();

			$this->_navi_menu = "master";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_SUPER);
					break;
			}
		}

		public function index($expand = 1, $page = 0, $size = 50) {
			$this->_subnavi_menu = "master_disease";
			$diseases = array();
			$disease = new diseaseModel;
			
			$this->search = new reqsession("master_disease");

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "sort ASC, disease_id DESC";

			$this->counts = $disease->counts("");

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$my_type = _my_type();
			$where = "";

			$err = $disease->select($where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$disease->expand = $expand;
				$dtemplates = array();
				$dtemplate = new dtemplateModel;

				$err = $dtemplate->select("disease_id=" . _sql($disease->disease_id),
					array("order" => "sort ASC"));

				while($err == ERR_OK)
				{
					$dtemplates[] = clone $dtemplate;
					$err = $dtemplate->fetch();
				}
				$disease->dtemplates = $dtemplates;

				$diseases[] = clone $disease;

				$err = $disease->fetch();
			}

			$this->expand = $expand;
			$this->mDiseases = $diseases;
			$this->mDisease = new diseaseModel;
			$this->mDtemplate = new dtemplateModel;
			$this->mLanguages = languageModel::get_all_code();
		}

		public function get_ajax()
		{
			$param_names = array("disease_id");
			$this->check_required($param_names);
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$disease_id = $params->disease_id;
			$disease = diseaseModel::get_model($disease_id);

			if ($disease == null)
				$this->check_error(ERR_NODATA);

			$this->finish(array("disease" => $disease->props), ERR_OK);
		}

		public function save_ajax()
		{
			$param_names = array("disease_id", 
				"disease_name",					// 病种名称
				"description");					// 病种简介
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$disease_id = $params->disease_id;
			if ($disease_id == null) {
				$disease = new diseaseModel;
				$disease->sort = diseaseModel::get_last_sort_no() + 1;
			}
			else {
				$disease = diseaseModel::get_model($disease_id);

				if ($disease == null)
					$this->check_error(ERR_NODATA);
			}

			$disease->load($params);

			$err = $disease->save();

			if ($err == ERR_OK && $disease_id == null)
			{
				// move to top
				$err = $disease->move_to(-2);
			}

			if ($err == ERR_OK) {
				if ($disease_id == null)
					_opr_log("新增病种成功 病种编号:" . $disease->disease_id);
				else
					_opr_log("病种信息更改(" . join(',', $disease->dirties) . ")成功 病种编号:" . $disease->disease_id);
			}

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

			if ($params->name_field == "disease_name") {
				$disease = diseaseModel::get_model($id);

				if ($disease == null)
					$this->check_error(ERR_NODATA);

				$disease->disease_name_l = $name_l;

				$err = $disease->save();
			}
			else if ($params->name_field == "dtemplate_name") {
				$dtemplate = dtemplateModel::get_model($id);

				if ($dtemplate == null)
					$this->check_error(ERR_NODATA);

				$dtemplate->dtemplate_name_l = $name_l;
				
				$err = $dtemplate->save();
			}

			$this->finish(null, $err);
		}

		public function move_to_ajax() {
			$param_names = array("disease_id", 
				"direct");					// 移动方向
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$disease_id = $params->disease_id;
			$disease = diseaseModel::get_model($disease_id);

			if ($disease == null)
				$this->check_error(ERR_NODATA);

			$err = $disease->move_to($params->direct);

			$this->finish(null, $err);
		}

		public function delete_ajax() {
			$param_names = array("disease_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$disease_id = $params->disease_id;

			$disease = diseaseModel::get_model($disease_id);

			if ($disease == null)
				$this->check_error(ERR_NODATA);

			$err = $disease->remove();

			if ($err == ERR_OK) {
				_opr_log("病种删除成功 病种编号:" . $disease_id);
			}

			$this->finish(null, $err);
		}

		public function save_dtemplate_ajax()
		{
			$param_names = array(
				"dtemplate_id",
				"disease_id", 
				"dtemplate_name",
				"template_file");	
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$dtemplate_id = $params->dtemplate_id;
			if ($dtemplate_id == null) {
				$dtemplate = new dtemplateModel;

				$this->check_required(array("disease_id"));

				$dtemplate->sort = dtemplateModel::get_last_sort_no($params->disease_id) + 1;
			}
			else {
				$dtemplate = dtemplateModel::get_model($dtemplate_id);

				if ($dtemplate == null)
					$this->check_error(ERR_NODATA);
			}

			$dtemplate->load($params);

			$err = $dtemplate->save();

			if ($err == ERR_OK && $dtemplate_id == null)
			{
				// move to top
				$err = $dtemplate->move_to(-2);
			}

			if ($err == ERR_OK) {
				if ($dtemplate_id == null)
					_opr_log("新增病历模板成功 病历模板编号:" . $dtemplate->dtemplate_id);
				else
					_opr_log("病历模板信息更改(" . join(',', $dtemplate->dirties) . ")成功 病历模板编号:" . $disease->dtemplate_id);
			}

			$this->finish(null, $err);
		}

		public function get_dtemplate_ajax()
		{
			$param_names = array("dtemplate_id");
			$this->check_required($param_names);
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$dtemplate_id = $params->dtemplate_id;
			$dtemplate = dtemplateModel::get_model($dtemplate_id);

			if ($dtemplate == null)
				$this->check_error(ERR_NODATA);

			$this->finish(array("dtemplate" => $dtemplate->props), ERR_OK);
		}

		public function delete_dtemplate_ajax() {
			$param_names = array("dtemplate_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$dtemplate_id = $params->dtemplate_id;

			$dtemplate = dtemplateModel::get_model($dtemplate_id);

			if ($dtemplate == null)
				$this->check_error(ERR_NODATA);

			$err = $dtemplate->remove();
			
			if ($err == ERR_OK) {
				_opr_log("病历模板删除成功 病历模板编号:" . $dtemplate_id);
			}

			$this->finish(null, $err);
		}

		public function move_to_dtemplate_ajax() {
			$param_names = array("dtemplate_id", 
				"direct");					// 移动方向
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$dtemplate_id = $params->dtemplate_id;
			$dtemplate = dtemplateModel::get_model($dtemplate_id);

			if ($dtemplate == null)
				$this->check_error(ERR_NODATA);

			$err = $dtemplate->move_to($params->direct);

			$this->finish(null, $err);
		}

		public function upload_template_ajax() {
			$param_names = array("dtemplate_id", 
				"template_file_l");
			$this->set_api_params($param_names);
			$params = $this->api_params;

			$this->start();

			$dtemplate_id = $params->dtemplate_id;
			$template_file_l = $params->template_file_l;
			$dtemplate = dtemplateModel::get_model($dtemplate_id);

			if ($dtemplate == null)
				$this->check_error(ERR_NODATA);

			$dtemplate->template_file_l = $template_file_l;

			foreach ($template_file_l as $lang => $f) {
				$dtemplate->save_attaches($lang);
			}

			$dtemplate->template_file = _json_encode($dtemplate->template_file_l);

			$err = $dtemplate->save();

			$this->finish(null, $err);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/