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
		"noticeModel",			// model name
		"m_notice",
		"notice_id",
		array(
			"content",		// 广播内容
			"content_l"),	// 广播内容(多语言)
		array("auto_inc" => true, 
			"operator_info" => false,
			"del_flag" => false));

	class noticeModel extends model // 广播信息模型
	{
		public static function get_all()
		{
			$notices = _session("notices");
			if ($notices == null) {
				$notices = array();
				$notice = new static;
				$err = $notice->select("", array("order" => "notice_id DESC"));
				while ($err == ERR_OK)
				{
					array_push($notices, array(
							"content" => $notice->content,
							"content_l" => $notice->content_l,
						));
					$err = $notice->fetch();
				}
				
				_session("notices", $notices);

				return $notices;	
			}
			else
				return $notices;
		}

		public static function clear_cache()
		{
			_session("notices", null);
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/