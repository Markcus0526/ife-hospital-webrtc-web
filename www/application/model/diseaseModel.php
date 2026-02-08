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
		"diseaseModel",			// model name
		"m_disease",
		"disease_id",
		array(
			"disease_name",		// 病种名称
			"disease_name_l",	// 病种名称(多语言)
			"description",		// 病种简介
			"sort"),		// 
		array("auto_inc" => true, 
			"operator_info" => true));

	class diseaseModel extends model // 病种信息模型
	{
		public static function get_all($return_assoc_array=true)
		{
			$diseases = array();
			$disease = new static;

			$err = $disease->select("", array("order" => "sort ASC"));
            while ($err == ERR_OK)
            {
            	if ($return_assoc_array) {
	            	$diseases[] = $disease->props(array(
	            		"disease_id",
	            		"disease_name"
	            	));	
            	}
            	else {
            		$diseases[] = clone $disease;
            	}

            	$err = $disease->fetch();
            }

            return $diseases;
		}

		public static function get_disease_name($disease_id)
		{
			if ($disease_id == null)
				return "";
			
			global $g_diseases;
			if ($g_diseases == null)
				$g_diseases = array();

			if (isset($g_diseases[$disease_id]))
				return _l_model($g_diseases[$disease_id], "disease_name");

			$disease = static::get_model($disease_id);
			if ($disease == null)
				return "";
			else {
				$g_diseases[$disease_id] = $disease->props;
				return _l_model($disease, "disease_name");
			}
		}

		public function get_sort_no()
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_disease
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

			$sql = "SELECT COUNT(*) FROM m_disease
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
			$disease = new static;
			$where = " disease_id!=" . _sql($this->disease_id);

			$err = $disease->select($where, array("order" => "sort ASC"));	

			$sort_no = 0;

			while($err == ERR_OK)
			{
				if ($sort_no == $new_sort_no) // target
					$sort_no++; // next

				$disease->sort = $sort_no;
				$err = $disease->update();
				if ($err != ERR_OK)
					return $err;

				$err = $disease->fetch();

				$sort_no ++;
			}

			return ERR_OK;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/