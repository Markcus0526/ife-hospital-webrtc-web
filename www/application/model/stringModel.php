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
		"stringModel",			// model name
		"m_string",
		"string_id",
		array(
			"string", 		// 字符串
			"string_l"),	// 字符串(多语言)
		array("auto_inc" => true, 
			"operator_info" => false,
			"del_flag" => false));

	class stringModel extends model // 字符串信息模型
	{
		public static function get_string_l($string)
		{
			$str = new static;
			$err = $str->select("string=" . _sql($string));
			if ($err == ERR_OK) {
				return $str->string_l;
			}
			
			return null;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/