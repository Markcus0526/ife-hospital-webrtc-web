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
		"privModel",			// model name
		"m_priv",
		"priv_id",
		array(
			"user_id",
			"priv_reg_user",	// 审核注册用户
			"priv_patients",	// 患者管理
			"priv_doctors",		// 专家管理
			"priv_interpreters",// 翻译管理
			"priv_hospitals",	// 医院管理
			"priv_chistory",	// 病历管理
			"priv_reserve",		// 会诊预约
			"priv_interviews",	// 会诊管理
			"priv_profile",		// 用户信息
			"priv_stats",		// 统计分析
			"priv_feedback",	// 用户反馈
			"priv_syscheck"),	// 系统检测
		array("auto_inc" => true,
			"operator_info" => true,
			"del_flag" => false));

	class privModel extends model // 权限信息模型
	{
		static public function from_user_id($user_id, $default_all = false, $disp_name=false)
		{
			$priv = new static;

			$err = $priv->select("user_id=" . _sql($user_id));
			if ($err == ERR_NODATA)
			{
				if ($default_all) {
					$priv->user_id = $user_id;
					$props = $priv->props;
					foreach ($props as $key => $value) {
						if ($key != "priv_id" && strstr($key, "priv_")) {
							$priv->$key = 0xFFFF;
						}
					}
				}
			}

			if (!$disp_name)
				return $priv;
			else {
				if (($priv->is_all_privs("priv_reg_user", CODE_PRIV_REG_USER)) &&
					($priv->is_all_privs("priv_patients", CODE_PRIV_PATIENTS)) &&
					($priv->is_all_privs("priv_doctors", CODE_PRIV_DOCTORS)) && 
					($priv->is_all_privs("priv_interpreters", CODE_PRIV_INTERPRETERS)) && 
					($priv->is_all_privs("priv_hospitals", CODE_PRIV_HOSPITALS)) &&  
					($priv->is_all_privs("priv_chistory", CODE_PRIV_CHISTORY)) && 
					($priv->is_all_privs("priv_reserve", CODE_PRIV_RESERVE)) && 
					($priv->is_all_privs("priv_interviews", CODE_PRIV_INTERVIEWS)) &&  
					($priv->is_all_privs("priv_profile", CODE_PRIV_PROFILE)) && 
					($priv->is_all_privs("priv_stats", CODE_PRIV_STATS)) &&  
					($priv->is_all_privs("priv_feedback", CODE_PRIV_FEEDBACK)) &&  
					($priv->is_all_privs("priv_syscheck", CODE_PRIV_SYSCHECK))
					) {
					return _l("所有权限");
				}
				$names = "";
				if ($priv->priv_reg_user)
					$names .= _l("审核注册用户") . " ";
				if ($priv->priv_patients)
					$names .= _l("患者管理") . " ";
				if ($priv->priv_doctors)
					$names .= _l("专家管理") . " ";
				if ($priv->priv_interpreters)
					$names .= _l("翻译管理") . " ";
				if ($priv->priv_hospitals)
					$names .= _l("医院管理") . " ";
				if ($priv->priv_chistory)
					$names .= _l("病历管理") . " ";
				if ($priv->priv_reserve)
					$names .= _l("预约会诊") . " ";
				if ($priv->priv_interviews)
					$names .= _l("会诊列表") . " ";
				if ($priv->priv_profile)
					$names .= _l("用户信息") . " ";
				if ($priv->priv_stats)
					$names .= _l("统计分析") . " ";
				if ($priv->priv_feedback)
					$names .= _l("用户反馈") . " ";
				if ($priv->priv_syscheck)
					$names .= _l("系统检测") . " ";

				return $names;
			}
		}

		public function is_all_privs($prop, $priv_code)
		{
			$my_priv = $this->$prop;
			$codes = _code_labels($priv_code);
			$all_priv = 0;
			foreach ($codes as $priv => $label) {
				$all_priv |= $priv;
			}

			return ($all_priv & $my_priv) == $all_priv;
		}

		public function get_privs()
		{
			return array(
				CODE_PRIV_REG_USER => $this->priv_reg_user,
				CODE_PRIV_PATIENTS => $this->priv_patients,
				CODE_PRIV_DOCTORS => $this->priv_doctors,
				CODE_PRIV_INTERPRETERS => $this->priv_interpreters,
				CODE_PRIV_HOSPITALS => $this->priv_hospitals,
				CODE_PRIV_CHISTORY => $this->priv_chistory,
				CODE_PRIV_RESERVE => $this->priv_reserve,
				CODE_PRIV_INTERVIEWS => $this->priv_interviews,
				CODE_PRIV_PROFILE => $this->priv_profile,
				CODE_PRIV_STATS => $this->priv_stats,
				CODE_PRIV_FEEDBACK => $this->priv_feedback,
				CODE_PRIV_SYSCHECK => $this->priv_syscheck
			);
		}

		static public function init_session()
		{
			$my_type = _my_type();
			switch ($my_type) {
				case UTYPE_ADMIN:
					$priv = static::from_user_id(_my_id(), true);
					if ($priv) {
						_my_priv($priv->get_privs());
					}
					break;
				case UTYPE_SUPER:
					$priv = new static;
					$p = $priv->get_privs();
					foreach ($p as $key => $value) {
						$p[$key] = 0xFFFF;
					}
					_my_priv($p);
					break;
				case UTYPE_PATIENT:
					$priv = new static;
					$p = $priv->get_privs();
					$p[CODE_PRIV_CHISTORY] = PRIV_DETAIL | PRIV_ADD | PRIV_EDIT | PRIV_DELETE;
					$p[CODE_PRIV_RESERVE] = PRIV_RESERVE;
					$p[CODE_PRIV_INTERVIEWS] = PRIV_IHISTORY | PRIV_CHISTORY | PRIV_PRESC | PRIV_ENTER;
					_my_priv($p);
					break;
				case UTYPE_DOCTOR:
					$priv = new static;
					$p = $priv->get_privs();
					$p[CODE_PRIV_CHISTORY] = PRIV_DETAIL;
					$p[CODE_PRIV_INTERVIEWS] = PRIV_CHISTORY | PRIV_PRESC | PRIV_ENTER;
					_my_priv($p);
					break;
				case UTYPE_INTERPRETER:
					$priv = new static;
					$p = $priv->get_privs();
					$p[CODE_PRIV_CHISTORY] = PRIV_DETAIL;
					$p[CODE_PRIV_INTERVIEWS] = PRIV_CHISTORY | PRIV_PRESC | PRIV_ENTER;
					_my_priv($p);
					break;

			}
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/