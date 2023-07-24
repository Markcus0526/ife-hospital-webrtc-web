<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	_model(
		"hospitalModel",			// model name
		"m_hospital",
		"hospital_id",
		array(
			"hcountry_id",
			"hospital_name",	// 医院名称
			"hospital_name_l",	// 医院名称(多语言)
			"address",
			"sort"),
		array("auto_inc" => true, 
			"operator_info" => true));

	class hospitalModel extends model // 医院信息模型 
	{
		public function save()
		{
			if ($this->hospital_id == null) {
				$this->sort = static::get_last_sort_no($this->hcountry_id) + 1;
			}

			$err = parent::save();

			return $err;
		}

		public function get_sort_no()
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_hospital
				WHERE del_flag = 0 AND 
					hcountry_id = " . _sql($this->hcountry_id) . " AND
					sort <= " . _sql($this->sort);

			$no = $db->scalar($sql) - 1;
			if ($no < 0)
				$no = 0;
			return $no;
		}

		public static function get_last_sort_no($hcountry_id)
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_hospital
				WHERE del_flag = 0 AND 
					hcountry_id = " . _sql($hcountry_id);
			
			$no = $db->scalar($sql) - 1;
			if ($no < 0)
				$no = 0;
			return $no;
		}

		public function move_to($direct)
		{
			$org_sort = $this->sort;
			$sort_no = $this->get_sort_no();

			if ($direct == 1 || $direct == 2) {
				$last_sort_no = static::get_last_sort_no($this->hcountry_id);
			}

			switch($direct) {
				case -2: // top
					$new_sort_no = 0;
					break;
				case -1: // up
					$new_sort_no = $sort_no - 1;
					if ($new_sort_no < 0)
						$new_sort_no = 0;
					break;
				case 1: // down
					$new_sort_no = $sort_no + 1;
					if ($new_sort_no > $last_sort_no)
						$new_sort_no = $last_sort_no;
					break;
				case 2: // bottom
					$new_sort_no = $last_sort_no;
					break;
			}

			if ($new_sort_no == $org_sort)
				return ERR_OK;

			$this->sort = $new_sort_no;

			$err = $this->update();
			if ($err != ERR_OK)
				return $err;

			// refresh sort
			$hospital = new static;
			$where = " hospital_id!=" . _sql($this->hospital_id) . "
				AND hcountry_id=" . _sql($this->hcountry_id);

			$err = $hospital->select($where, array("order" => "sort ASC"));	

			$sort_no = 0;

			while($err == ERR_OK)
			{
				if ($sort_no == $new_sort_no) // target
					$sort_no++; // next

				$hospital->sort = $sort_no;
				$err = $hospital->update();
				if ($err != ERR_OK)
					return $err;

				$err = $hospital->fetch();

				$sort_no ++;
			}

			return ERR_OK;
		}

		public static function get_hospital_name($hospital_id)
		{
			$hospital = hospitalModel::get_model($hospital_id);
			if ($hospital) 
				return _l_model($hospital, "hospital_name");
			return "";
		}

		public static function all_tree($expanded_hcountry_id=null)
		{
			$hcountries = array();
			$hcountry = new hcountryModel;

			$order = "sort ASC";
			$counts = $hcountry->counts("");

			$where = "";

			$err = $hcountry->select($where,
				array("order" => $order));

			$expanded = false;
			while($err == ERR_OK)
			{
				$hospitals = array();
				$hospital = new hospitalModel;
				$err = $hospital->select("hcountry_id=" . _sql($hcountry->hcountry_id),
					array("order" => "sort ASC"));

				while($err == ERR_OK)
				{
					$hospitals[] = clone $hospital;
					$err = $hospital->fetch();
				}
				if (!$expanded && 
					$expanded_hcountry_id == $hcountry->hcountry_id) {
					$expanded = true;
					$hcountry->expanded = true;
				}
				else {
					$hcountry->expanded = false;	
				}
				$hcountry->hospitals = $hospitals;

				$hcountries[] = clone $hcountry;

				$err = $hcountry->fetch();
			}

			if (!$expanded && count($hcountries))
			{
				$hcountries[0]->expanded = true;	
			}

			return $hcountries;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/