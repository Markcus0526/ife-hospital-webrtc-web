<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class stringController extends controller {
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

		public function index($page = 0, $size = 50) {
			$this->_subnavi_menu = "master_string";

			if ($this->upgrade == "1") {
				$this->upgrade_string();
			}

			$strings = array();
			$string = new stringModel;
			$w_and = array();
			
			$this->search = new reqsession("master_string");

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "string_id DESC";

			if ($this->search->query != "") {
				$like = _sql("%" . $this->search->query . "%");
				$w_and[] = "(string LIKE " . $like  . " OR string_l LIKE " . $like  . ")";
			}

			$where = implode(" AND ", $w_and);

			$this->counts = $string->counts($where);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$err = $string->select($where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$strings[] = clone $string;

				$err = $string->fetch();
			}

			$this->mStrings = $strings;
			$this->mString = new stringModel;
			$this->mLanguages = languageModel::get_all_code();
		}

		public function get_ajax()
		{
			$param_names = array("string_id");
			$this->check_required($param_names);
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$string_id = $params->string_id;
			$string = stringModel::get_model($string_id);

			if ($string == null)
				$this->check_error(ERR_NODATA);

			$this->finish(array("string" => $string->props), ERR_OK);
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

			if ($params->name_field == "string") {
				$string = stringModel::get_model($id);

				if ($string == null)
					$this->check_error(ERR_NODATA);

				$string->string_l = $name_l;

				$err = $string->save();
			}

			$this->finish(null, $err);
		}

		public function upgrade_string($datas)
		{
			if (_is_empty($datas))
				$datas = array("d_title", "d_depart", "introduction");
			else if (is_string($datas))
				$datas = array($datas);
			else if (!is_array($datas))
				return;

			$sqls = array();
			foreach ($datas as $data) {
				switch($data) {
				 	case "d_title":
						array_push($sqls, "SELECT DISTINCT d_title string FROM m_user 
							WHERE d_title IS NOT NULL");
						break;
					case "d_depart":
						array_push($sqls, "SELECT DISTINCT d_depart string FROM m_user 
							WHERE d_depart IS NOT NULL");
						break;
					case "introduction":
						array_push($sqls, "SELECT DISTINCT introduction string FROM m_user 
							WHERE introduction IS NOT NULL");
						break;
				}
			}

			foreach ($sqls as $sql) {
				$str = new model;
				$string = new stringModel;
				$err = $string->query($sql);
				while ($err == ERR_OK)
				{
					$sql = "SELECT string_id FROM m_string WHERE string=" . _sql($string->string);
					$err = $str->query($sql);
					if ($err == ERR_NODATA) {
						$string->string_id = null;
						$err = $string->insert();
					}

					$err = $string->fetch();
				}
			}

			_goto("string");
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/