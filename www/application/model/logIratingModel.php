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
		"logIratingModel",				// model name
		"l_irating",
		"irating_id",
		array(
			"interview_id",				// 会诊ID
			"user_id",					// 评价ID
			"interpreter_id",			// 翻译用户ID
			"rate"),					// 评价等级
		array("auto_inc" => true,
			"del_flag" => false));

	class logIratingModel extends model // 翻译评价信息模型
	{
		static public function stats($interpreter_id)
		{
			$iratings = array();
			$iratings[IRATE_UNSATISFY] = 0;
			$iratings[IRATE_SATISFY] = 0;
			$iratings[IRATE_VERYSATISFY] = 0;
			$irating = new model;
			$sql = "SELECT rate, COUNT(irating_id) counts 
				FROM l_irating
				WHERE interpreter_id=" . _sql($interpreter_id) . "
				GROUP BY rate";
			$err = $irating->query($sql);

			while ($err == ERR_OK)
			{
				$iratings[$irating->rate] = $irating->counts;
				$err = $irating->fetch();
			}

			return $iratings;
		}

		static public function set_rate($interview_id, $interpreter_id, $rate)
		{
			$my_id = _my_id();
			$irating = new logIratingModel;
			$err = $irating->select("interview_id=" . _sql($interview_id) . 
				" AND user_id=" . _sql($my_id) . 
				" AND interpreter_id=" . _sql($interpreter_id));

			$irating->interview_id = $interview_id;
			$irating->user_id = $my_id;
			$irating->interpreter_id = $interpreter_id;
			$irating->rate = $rate;

			return $irating->save();

		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/