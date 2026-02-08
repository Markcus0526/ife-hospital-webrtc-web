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
		"logAccessModel",			// model name
		"l_access",
		"access_id",
		array("user_id",
			"mobile",
			"login_time",
			"access_time"),
		array("auto_inc" => true,
			"del_flag" => false));

	class logAccessModel extends model 
	{
		static public function login()
		{
			$log_access = new logAccessModel;

			$log_access->user_id = _my_id();
			$log_access->mobile = _my_mobile();
			$log_access->login_time = "##NOW()";

			$err = $log_access->save();
			if ($err == ERR_OK)
			{
				_session("access_id", $log_access->access_id);
			}
		}

		static public function last_access()
		{
			$log_access = logAccessModel::get_model(_session("access_id"));
			if ($log_access) {
				$log_access->access_time = "##NOW()";

				$log_access->save();
			}
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/