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
		"settingsModel",			// model name
		"m_settings",
		"settings_id",
		array(
			"save_record_limit" // 会诊视频存储时间:*天
			),
		array("auto_inc" => true, 
			"operator_info" => false,
			"del_flag" => false));

	class settingsModel extends model // 系统设置模型
	{
		public static function get_config() {
			$settings = new static;
			$err = $settings->select();

			if ($settings == ERR_NODATA)
			{
				$settings->settings_id = 1;
				$settings->save_record_limit = 30;
			}
			return $settings;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/