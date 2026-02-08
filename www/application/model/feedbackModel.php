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
		"feedbackModel",			// model name
		"t_feedback",
		"feedback_id",
		array(
			"user_id",
			"user_type",
			"mobile",
			"email",
			"comment",
			"status"),	
		array("auto_inc" => true, 
			"operator_info" => true));

	class feedbackModel extends model // 反馈信息模型
	{
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/