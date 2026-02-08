<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class hcountryController extends controller {
		public function __construct() {
			parent::__construct();

			$this->_navi_menu = "master";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_SUPER | UTYPE_ADMIN);
					break;
			}
		}

		// hcountry
		public function set_h_expand_flag_ajax()
		{
			$param_names = array("hcountry_id", 
				"h_expand_flag");					// 默认展开
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$hcountry_id = $params->hcountry_id;
			$hcountry = hcountryModel::get_model($hcountry_id);

			if ($hcountry == null)
				$this->check_error(ERR_NODATA);

			$hcountry->load($params);

			$err = $hcountry->update("h_expand_flag");

			if ($params->h_expand_flag) {
				$db = db::get_db();
				$err = $db->execute("UPDATE m_hcountry SET h_expand_flag=null WHERE hcountry_id!=" . _sql($hcountry_id));
			}

			$this->finish(null, $err);
		}

		public function get_ajax()
		{
			$param_names = array("hcountry_id");
			$this->check_required($param_names);
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$hcountry_id = $params->hcountry_id;
			$hcountry = hcountryModel::get_model($hcountry_id);

			if ($hcountry == null)
				$this->check_error(ERR_NODATA);

			$this->finish(array("hcountry" => $hcountry->props), ERR_OK);
		}

		public function save_ajax()
		{
			$param_names = array("hcountry_id", 
				"o_hcountry_id",
				"country_name");					// 国家名称
			$this->set_api_params($param_names);
			$this->check_required(array("hcountry_id"));
			$params = $this->api_params;
			$this->start();

			$hcountry_id = $params->hcountry_id;
			$o_hcountry_id = $params->o_hcountry_id;
			$hcountry = hcountryModel::get_model($hcountry_id);

			if ($o_hcountry_id == null) {
				// new
				if ($hcountry != null)
					$this->check_error(ERR_ALREADY_HCOUNTRY);

				$hcountry = new hcountryModel;
				$hcountry->load($params);
				$hcountry->sort = hcountryModel::get_last_sort_no() + 1;
				$err = $hcountry->insert();

				if ($err == ERR_OK)
				{
					// move to top
					$err = $hcountry->move_to(-2);
				}
			}
			else if ($o_hcountry_id == $hcountry_id) {
				// update
				$hcountry = hcountryModel::get_model($hcountry_id);

				if ($hcountry == null)
					$this->check_error(ERR_NODATA);

				$hcountry->load($params);

				$err = $hcountry->save();
			}
			else {
				// change primary key
				if ($hcountry != null)
					$this->check_error(ERR_ALREADY_HCOUNTRY);

				$db = db::get_db();
				$sql = "UPDATE m_hcountry 
					SET hcountry_id=" . _sql($hcountry_id) .  ",
						country_name=" . _sql($params->country_name) . "
					WHERE hcountry_id=" . _sql($o_hcountry_id);
				$err = $db->execute($sql);
				
				$sql = "UPDATE m_hospital 
					SET hcountry_id=" . _sql($hcountry_id) .  "
					WHERE hcountry_id=" . _sql($o_hcountry_id);
				$err = $db->execute($sql);
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

			$hcountry_id = $params->id;
			$name_l = _json_encode($params->name_l);

			$hcountry = hcountryModel::get_model($hcountry_id);

			if ($hcountry == null)
				$this->check_error(ERR_NODATA);

			if ($params->name_field == "country_name") {
				$hcountry->country_name_l = $name_l;
			}
			$err = $hcountry->save();

			$this->finish(null, $err);
		}

		public function delete_ajax() {
			$param_names = array("hcountry_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$hcountry_id = $params->hcountry_id;

			$hcountry = hcountryModel::get_model($hcountry_id);

			if ($hcountry == null)
				$this->check_error(ERR_NODATA);

			$err = $hcountry->remove(true);

			if ($err == ERR_OK)
			{
				$db = db::get_db();
				$db->execute("UPDATE m_hospital SET del_flag=1 WHERE hcountry_id=" . _sql($hcountry_id));

			}

			$this->finish(null, $err);
		}

		public function move_to_ajax() {
			$param_names = array("hcountry_id", 
				"direct");					// 移动方向
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$hcountry_id = $params->hcountry_id;
			$hcountry = hcountryModel::get_model($hcountry_id);

			if ($hcountry == null)
				$this->check_error(ERR_NODATA);

			$err = $hcountry->move_to($params->direct);

			$this->finish(null, $err);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/