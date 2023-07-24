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
		"dtimeModel",			// model name
		"t_dtime",
		"dtime_id",
		array(
			"doctor_id",		// 专家
			"start_time",		// 开始时刻
			"state"),			// 1:预约可能
		array("auto_inc" => true, 
			"operator_info" => true,
			"del_flag" => false));

	class dtimeModel extends model // 专家时间信息模型
	{
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/