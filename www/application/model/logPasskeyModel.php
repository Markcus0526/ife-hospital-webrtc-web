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
		"logPasskeyModel",			// model name
		"l_passkey",
		"passkey_id",
		array(
			"phone_num",
			"passkey_text"),
		array("auto_inc" => true, 
			"operator_info" => true,
			"del_flag" => false));

	class logPasskeyModel extends model // 短信验证码信息模型
	{
		public static function get_passkey($phone_num)
		{
			static::clear_old();
			
			$passkey = new static;
			$err = $passkey->select("phone_num=" . _sql($phone_num));
			if ($err == ERR_NODATA)
				return null;

			return $passkey->passkey_text;
		}

		public static function send_passkey($phone_num)
		{
			$db = db::get_db();

			static::clear_old();
			
			$passkey = new static;
			$err = $passkey->select("phone_num=" . _sql($phone_num));

			$passkey->phone_num = $phone_num;
			$passkey->passkey_text = _generate_passkey_text(5);

			$err = $passkey->save();
			if ($err == ERR_OK) {
				$db->commit();
				return _send_sms($phone_num, _ll("您的验证码是%s。请在1分钟内填写。", $passkey->passkey_text));
			}

			return $err;
		}

		public static function clear_old()
		{
			// delete all record before past 1 minutes
			$db = db::get_db();
			$sql = "DELETE FROM l_passkey WHERE TIMEDIFF(NOW(), create_time) > '00:01:00'";

			return $db->execute($sql);
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/