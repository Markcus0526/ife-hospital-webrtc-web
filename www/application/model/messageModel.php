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
		"messageModel",				// model name
		"t_message",
		"message_id",
		array(
			"mobile",				// 手机号码
			"email",				// 电子邮箱
			"email_to_name",		// 收件人姓名
			"email_from_name",		// 发件人名称
			"content",				// 内容
			"email_title",			// 邮件题目
			"email_header",			// 邮件头题
			"sms_header",			// 短信头题
			"send_time"),			// 送信时刻
		array("auto_inc" => true,
			"del_flag" => false));

	class messageModel extends model // 通知信息模型
	{
		static public function push($user, $title, $content)
		{
			if ($user && $user->mobile) {
				$mobile = $user->mobile;

				$message = new static;

				$message->mobile = $mobile;
				$message->email = $user->email;
				$message->email_to_name = $user->user_name;
				$message->email_from_name = _sms_string($mobile, _ll(MAIL_FROMNAME));
				$message->content = _sms_string($mobile, $content);
				$message->email_title = _sms_string($mobile, $title);
				$message->email_header = $user->user_name . " : \n";
				$message->sms_header = "【" . _sms_string($mobile, _ll("MARKCUS远程医疗")) . "】";

				return $message->insert();	
			}
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/