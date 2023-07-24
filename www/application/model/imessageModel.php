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
		"imessageModel",				// model name
		"t_imessage",
		"imessage_id",
		array(
			"interview_id",				// 会诊ID
			"user_id",					// 送信用户ID
			"user_type",				// 用户种别
			"content",					// 内容
			"read_flag",				// 阅读状态
			"no"),						// 内部判别码
		array("auto_inc" => true,
			"del_flag" => false));

	class imessageModel extends model // 讨论信息模型
	{
		static public function all($interview_id, $user_type)
		{
			$messages = array();
			$imessage = new static;
			$err = $imessage->select("interview_id=" . _sql($interview_id),
				array("order" => "imessage_id ASC"));
			while ($err == ERR_OK)
			{
				$imessage->unread = ($imessage->read_flag & $user_type) ? false : true;
				if ($imessage->user_type == UTYPE_PATIENT) {
					$imessage->user_type = "p";
					$imessage->user_name = _l("患者");
				}
				else if ($imessage->user_type == UTYPE_DOCTOR) {
					$imessage->user_type = "d";
					$imessage->user_name = _l("专家");
				}
				else if ($imessage->user_type == UTYPE_INTERPRETER) {
					$imessage->user_type = "i";
					$imessage->user_name = _l("翻译");
				}
				else {
					continue;
				}
				$messages[] = $imessage->props(array("user_type", "user_name", "content", 
					"unread", "no", "create_time"));
				$err = $imessage->fetch();
			}

			return $messages;
		}

		static public function clear($interview_id)
		{
			$db = db::get_db();

			return $db->execute("DELETE FROM t_imessage WHERE interview_id=" . _sql($interview_id));
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/