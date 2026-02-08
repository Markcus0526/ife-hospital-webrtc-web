<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/
	
	_model(
		"hcountryModel",				// model name
		"m_hcountry",
		"hcountry_id",
		array(
			"country_name",
			"country_name_l",
			"sort",
			"h_expand_flag"),
		array("auto_inc" => true, 
			"operator_info" => true));

	class hcountryModel extends model // 国家信息模型
	{
		public function get_sort_no()
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_hcountry
				WHERE del_flag = 0 AND 
					sort <= " . _sql($this->sort);

			$no = $db->scalar($sql) - 1;
			if ($no < 0)
				$no = 0;
			return $no;
		}

		public static function get_last_sort_no()
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_hcountry
				WHERE del_flag = 0";
			
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
				$last_sort_no = static::get_last_sort_no();
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
			$country = new hcountryModel;
			$where = " hcountry_id!=" . _sql($this->hcountry_id);

			$err = $country->select($where, array("order" => "sort ASC"));	

			$sort_no = 0;

			while($err == ERR_OK)
			{
				if ($sort_no == $new_sort_no) // target
					$sort_no++; // next

				$country->sort = $sort_no;
				$err = $country->update();
				if ($err != ERR_OK)
					return $err;

				$err = $country->fetch();

				$sort_no ++;
			}

			return ERR_OK;
		}

		public static function get_country_name($hcountry_id)
		{
			$country = hcountryModel::get_model($hcountry_id);
			if ($country) 
				return $country->country_name;
			return "";
		}

	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/