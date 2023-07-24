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
		"uhospitalModel",		// model name
		"m_uhospital",
		"uhospital_id",
		array(
			"user_id",			// 用户ID
			"hospital_id"),		// 医院ID
		array("auto_inc" => true, 
			"operator_info" => false,
			"del_flag" => false));

	class uhospitalModel extends model // 用户所属医院信息模型
	{
		static public function get_hospitals($user_id, $for_edit=false)
		{
			$hospitals = array();
			$uhospital = new model;
			$err = $uhospital->query("SELECT * FROM v_uhospital WHERE user_id=" . _sql($user_id), 
				array("order" => "sort ASC"));
			while ($err == ERR_OK)
			{
				if ($for_edit) {
					$hospitals[] = $uhospital->hospital_id;	
				}
				else {
					$hospitals[] = _l_model($uhospital, "hospital_name");
				}
				$err = $uhospital->fetch();
			}

			if ($for_edit)
				return $hospitals;
			else 
				return implode(", ", $hospitals);
		}

		static public function save_hospitals($user_id, $hospitals) 
		{
            $ids = array();
            if (is_string($hospitals))
            	$hospitals = preg_split("/,/", $hospitals);

			if (is_array($hospitals)) {
				foreach($hospitals as $hospital_id)
				{
					if ($hospital_id > 0) {
						$uhospital = new uhospitalModel;
						$err = $uhospital->select("user_id=" . _sql($user_id) . " AND 
							hospital_id=" . _sql($hospital_id));
						$uhospital->user_id = $user_id;
						$uhospital->hospital_id = $hospital_id;
						$err = $uhospital->save();

						array_push($ids, $uhospital->uhospital_id);
					}
				}
			}

			$db = db::get_db();
			$sql = "DELETE FROM m_uhospital WHERE user_id=" . _sql($user_id);
            if (count($ids) > 0) 
            	$sql .= " AND uhospital_id NOT IN (" . implode(",", $ids) . ")";

            $db->execute($sql);
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/