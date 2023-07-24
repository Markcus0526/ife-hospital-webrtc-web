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
		"ulangModel",		// model name
		"m_ulang",
		"ulang_id",
		array(
			"user_id",			// 用户ID
			"language_id"),		// 语言ID
		array("auto_inc" => true, 
			"operator_info" => true));

	class ulangModel extends model // 用户精通语言信息模型
	{
		static public function get_languages($user_id, $for_edit=false)
		{
			$languages = array();
			$ulang = new model;
			$err = $ulang->query("SELECT * FROM v_ulang WHERE user_id=" . _sql($user_id), 
				array("order" => "sort ASC"));
			while ($err == ERR_OK)
			{
				if ($for_edit) {
					$languages[] = $ulang->language_id;	
				}
				else {
					$languages[] = _l_model($ulang, "language_name");
				}
				$err = $ulang->fetch();
			}

			if ($for_edit)
				return $languages;
			else 
				return implode(", ", $languages);
		}

		static public function save_languages($user_id, $languages) 
		{
            $ids = array();

            if (is_string($languages))
            	$languages = preg_split("/,/", $languages);

			if (is_array($languages)) {
				foreach($languages as $language_id)
				{
					if ($language_id > 0) {
						$ulang = new ulangModel;
						$err = $ulang->select("user_id=" . _sql($user_id) . " AND 
							language_id=" . _sql($language_id));
						$ulang->user_id = $user_id;
						$ulang->language_id = $language_id;
						$err = $ulang->save();

						array_push($ids, $ulang->ulang_id);
					}
				}
			}

			$db = db::get_db();
			$sql = "DELETE FROM m_ulang WHERE user_id=" . _sql($user_id);
            if (count($ids) > 0) 
            	$sql .= " AND ulang_id NOT IN (" . implode(",", $ids) . ")";

            $db->execute($sql);
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/