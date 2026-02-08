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
		"interpModel",			// model name
		"t_interp",
		"interp_id",
		array(
			"interview_id",
			"planguage_id",		// 患者精通语言
			"dlanguage_id"),	// 专家精通语言

		array("auto_inc" => true));

	class interpModel extends model // 会诊翻译信息模型
	{
		public static function message_to_all_interpreter($interview_id, $ll_title, $ll_content)
		{
			$mobiles = array();

			$interp = new static;

			$err = $interp->select("interview_id=" . _sql($interview_id));
			while ($err == ERR_OK)
			{
				$pl_id = $interp->planguage_id;
				$dl_id = $interp->dlanguage_id;

				$user = new userModel;

				$sql = "SELECT u.* 
					FROM m_user u 
					INNER JOIN m_ulang ul1 ON u.user_id=ul1.user_id 
						AND ul1.language_id=" . _sql($pl_id) . "
					INNER JOIN m_ulang ul2 ON u.user_id=ul2.user_id
						AND ul2.language_id=" . _sql($dl_id) . "
					WHERE u.user_type=" . _sql(UTYPE_INTERPRETER);

				$err = $user->query($sql);
				while ($err == ERR_OK) {
					_push_message($user, $ll_title, $ll_content);
					$err = $user->fetch();
				}

				$err = $interp->fetch();
				// only one record for interview vs interp
				break;
			}
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/