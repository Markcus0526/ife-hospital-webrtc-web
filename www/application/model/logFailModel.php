<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2018/6/21									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/
		
	_model(
		"logFailModel",			// model name
		"l_logfail",
		"logfail_id",
		array(
			"user_id",			// 用户ID
			"fail_type",		// 登录失败类型
			"login_fails",		// 登录失败次数
			"login_date",		// 用户权限
			),
		array("auto_inc" => true,
			"operator_info" => false, 
			"del_flag" => false));

	class logFailModel extends model // 登录失败信息模型
	{	
		public static function get_fails($fail_type, $user_id, $date)
		{
			$db = db::get_db();
			$fails = $db->scalar("SELECT login_fails
				FROM l_logfail
				WHERE user_id="._sql($user_id)." AND 
					fail_type="._sql($fail_type)." AND
					login_date="._sql($date));
			if ($fails == null)
				$fails = 0;
				
			return $fails;
		}

		public static function inc_fail($fail_type, $user_id)
		{
			$today = _date();
			$fails = static::get_fails($fail_type, $user_id, $today);
			$fails++;

			$logfail = new static;
			$err = $logfail->select("user_id="._sql($user_id)." AND 
					fail_type="._sql($fail_type)." AND
					login_date="._sql($today));

			$logfail->user_id = $user_id;
			$logfail->fail_type = $fail_type;
			$logfail->login_fails = $fails;
			$logfail->login_date = $today;

			$err = $logfail->save();

			return $fails;
		}

		public static function clear($user_id)
		{
			$db = db::get_db();
			$db->execute("DELETE FROM l_logfail
				WHERE user_id="._sql($user_id));
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/