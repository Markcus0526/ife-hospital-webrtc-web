<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class hospitalController extends controller {
		public function __construct() {
			parent::__construct();

			$this->_navi_menu = "master";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				case "select2_ajax":
					parent::check_priv($action, UTYPE_NONE);
					break;
				default:
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN);
					break;
			}
		}

		public function index($expand = 1, $page = 0, $size = 100) {
			$this->_subnavi_menu = "master_hospital";
			$countries = array();
			$country = new hcountryModel;
			
			$my_type = _my_type();
			
			$this->search = new reqsession("master_hospital");
			if ($my_type == UTYPE_ADMIN) {
				$this->search->country_id = _country_id();
			}

			$this->order = "sort ASC";

			$this->counts = $country->counts("");

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$w_and = array();
			if (!_is_empty($this->search->country_id)) {
				$w_and[] = "hcountry_id=" . _sql($this->search->country_id);
			}

			$where = implode(" AND ", $w_and);

			$err = $country->select($where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while($err == ERR_OK)
			{
				$country->expand = $expand;
				$hospitals = array();
				$hospital = new hospitalModel;

				$err = $hospital->select("hcountry_id=" . _sql($country->hcountry_id),
					array("order" => "sort ASC"));

				while($err == ERR_OK)
				{
					$hospitals[] = clone $hospital;
					$err = $hospital->fetch();
				}
				$country->hospitals = $hospitals;

				$countries[] = clone $country;

				$err = $country->fetch();
			}

			$this->expand = $expand;
			$this->mCountries = $countries;
			$this->mCountry = new hcountryModel;
			$this->mHospital = new hospitalModel;
			$this->mLanguages = languageModel::get_all_code();
		}

		public function get_ajax()
		{
			$param_names = array("hospital_id");
			$this->check_required($param_names);
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$hospital_id = $params->hospital_id;
			$hospital = hospitalModel::get_model($hospital_id);

			if ($hospital == null)
				$this->check_error(ERR_NODATA);

			$this->finish(array("hospital" => $hospital->props), ERR_OK);
		}

		public function save_ajax()
		{
			$param_names = array("hospital_id", 
				"hospital_name",				// 医院名称
				"address",						// 医院地址
				"hcountry_id");					// 国家名称
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$hospital_id = $params->hospital_id;
			if ($hospital_id == null) {
				$hospital = new hospitalModel;
			}
			else {
				$hospital = hospitalModel::get_model($hospital_id);

				if ($hospital == null)
					$this->check_error(ERR_NODATA);
			}

			$hospital->load($params);

			$err = $hospital->save();

			if ($err == ERR_OK && $hospital_id == null)
			{
				// move to top
				$err = $hospital->move_to(-2);
			}

			if ($err == ERR_OK) {
				if ($hospital_id == null)
					_opr_log("新增医院成功 医院编号:" . $hospital->hospital_id);
				else
					_opr_log("医院信息更改(" . join(',', $hospital->dirties) . ")成功 医院编号:" . $hospital->hospital_id);
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

			$hospital_id = $params->id;
			$name_l = _json_encode($params->name_l);

			$hospital = hospitalModel::get_model($hospital_id);

			if ($hospital == null)
				$this->check_error(ERR_NODATA);

			if ($params->name_field == "hospital_name") {
				$hospital->hospital_name_l = $name_l;
			}
			$err = $hospital->save();

			$this->finish(null, $err);
		}

		public function delete_ajax() {
			$param_names = array("hospital_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$hospital_id = $params->hospital_id;

			$hospital = hospitalModel::get_model($hospital_id);

			if ($hospital == null)
				$this->check_error(ERR_NODATA);

			$err = $hospital->remove();

			if ($err == ERR_OK) {
				_opr_log("医院删除成功 医院编号:" . $hospital_id);
			}

			$this->finish(null, $err);
		}

		public function move_to_ajax() {
			$param_names = array("hospital_id", 
				"direct");					// 移动方向
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$hospital_id = $params->hospital_id;
			$hospital = hospitalModel::get_model($hospital_id);

			if ($hospital == null)
				$this->check_error(ERR_NODATA);

			$err = $hospital->move_to($params->direct);

			$this->finish(null, $err);
		}

		public function select2_ajax() {
			$param_names = array("query", "hcountry_id", "doctor_mobile");
			$this->set_api_params($param_names);
			$this->check_required(array());
			$params = $this->api_params;

			$countries = array();
			$hospitals = array();
			$hospital = new hospitalModel;

			// if ($params->doctor_mobile != "")
			// {
			// 	$params->hcountry_id = countryModel::tel_num_to_country_id($params->doctor_mobile);
			// }

			$this->where = "h.del_flag=0 AND c.del_flag=0 ";
			if ($params->query != null) {
				$ss = _sql("%" . $params->query . "%");
				$this->where .= " AND h.hospital_name LIKE " . $ss;
			}
			// if ($params->hcountry_id != null) {
			// 	$this->where .= " AND h.hcountry_id=" . _sql($params->hcountry_id);	
			// }

			$this->order = "c.sort ASC, h.sort ASC";
			$sql = "SELECT h.hospital_id, h.hospital_name, h.hospital_name_l,
					c.hcountry_id, c.country_name, c.country_name_l
				FROM m_hospital h
				LEFT JOIN m_hcountry c ON h.hcountry_id=c.hcountry_id ";

			$err = $hospital->query($sql,
				array(
					"where" => $this->where,
					"order" => $this->order,
					"limit" => 100));
			$hcountry_id = null; $country_name = null;
			while ($err == ERR_OK)
			{
				if ($params->hcountry_id == null) {
					if ($hcountry_id != $hospital->hcountry_id) {
						if ($hcountry_id != null) {
							array_push($countries, array(
									"text" => $country_name,
									"children" => $hospitals
								));
						}
						$hcountry_id = $hospital->hcountry_id;
						$country_name = _l_model($hospital, "country_name");
						$hospitals = array();
					}
				}
				array_push($hospitals, array(
					"id" => $hospital->hospital_id,
					"text" => _l_model($hospital, "hospital_name"),
					"hcountry_id" => $hospital->hcountry_id
				));

				$err = $hospital->fetch();
			}

			if ($params->hcountry_id == null) {
				array_push($countries, array(
						"text" => $country_name,
						"children" => $hospitals
					));

				$this->finish(array("options" => $countries), ERR_OK);
			}
			else
				$this->finish(array("options" => $hospitals), ERR_OK);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/