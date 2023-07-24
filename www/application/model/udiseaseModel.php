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
		"udiseaseModel",		// model name
		"m_udisease",
		"udisease_id",
		array(
			"user_id",			// 用户ID
			"disease_id"),		// 疾病ID

		array("auto_inc" => true, 
			"operator_info" => true));

	class udiseaseModel extends model // 用户疾病专长信息模型
	{
		static public function get_diseases($user_id, $for_edit=false, $disease_ids=null, &$match=false)
		{
			$diseases = array();
			$udisease = new model;
			$err = $udisease->query("SELECT * FROM v_udisease WHERE user_id=" . _sql($user_id), 
				array("order" => "disease_id ASC"));
			$match = false;
			while ($err == ERR_OK)
			{
				if ($for_edit) {
					$diseases[] = $udisease->disease_id;	
				}
				else {
					$diseases[] = _l_model($udisease, "disease_name");	
				}

				if (is_array($disease_ids))
				{
					foreach ($disease_ids as $disease_id) {
						if ($disease_id == $udisease->disease_id)
						{
							$match = true;
							break;
						}
					}
				}
				else {
					$match = true;
				}
				$err = $udisease->fetch();
			}

			if ($for_edit)
				return $diseases;
			else 
				return implode(", ", $diseases);
		}

		static public function save_diseases($user_id, $diseases) 
		{
            $ids = array();

            if (is_string($diseases))
            	$diseases = preg_split("/,/", $diseases);

			if (is_array($diseases)) {
				foreach($diseases as $disease_id)
				{
					if ($disease_id > 0) {
						$udisease = new udiseaseModel;
						$err = $udisease->select("user_id=" . _sql($user_id) . " AND 
							disease_id=" . _sql($disease_id));
						$udisease->user_id = $user_id;
						$udisease->disease_id = $disease_id;
						$err = $udisease->save();

						array_push($ids, $udisease->udisease_id);
					}
				}
			}

			$db = db::get_db();
			$sql = "DELETE FROM m_udisease WHERE user_id=" . _sql($user_id);
            if (count($ids) > 0) 
            	$sql .= " AND udisease_id NOT IN (" . implode(",", $ids) . ")";

            $db->execute($sql);
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/