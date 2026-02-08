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
		"logBhistoryModel",			// model name
		"l_bhistory",
		"bhistory_id",
		array(
			"owner_id",
			"balance_type",
			"balance_date",
			"amount",
			"balance",
			"tran_num",
			"actor_id",
			"charge_mode",
			"charge_result"),

		array("auto_inc" => true, 
			"operator_info" => true));

	class logBhistoryModel extends model  // 账号消费充值历史信息模型
	{
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/