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
		"sessionModel",			// model name
		"l_session",
		array("session_id", "user_id"),
		array("mobile",
			"login_time",
			"access_time",
			"ip"),
		array("auto_inc" => false,
			"del_flag" => false));

	class sessionModel extends model 
	{
		static public function update_session()
		{
			$me = _user();
			if ($me != null) {
				$me->access_time = "##NOW()";
				$err = $me->save();

				$db = db::get_db();
				$my_id = _my_id();
				$session_id = session_id();
				$db->execute("UPDATE l_session SET access_time=NOW() WHERE session_id=" . _sql($session_id) . " AND user_id=" . _sql($my_id));
			}
		}

		static public function insert_session()
		{
			$my_id = _my_id();
			$session_id = session_id();
			if ($my_id != null) {
				$session = sessionModel::get_model(array($session_id, $my_id));
				if ($session != null)
					$session->remove(true);

				$session = new sessionModel;
				$session->session_id = $session_id;
				$session->user_id = $my_id;
				$session->mobile = _my_mobile();
				$session->login_time = "##NOW()";
				$session->access_time = "##NOW()";
				$session->ip = _ip();

				$err = $session->insert();
			}

			sessionModel::clear_old_session();
		}

		static public function clear_old_session()
		{
			$db = db::get_db();
			$sql = "DELETE FROM l_session WHERE DATEDIFF(NOW(), access_time) > 30"; 
			$db->query($sql);
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/