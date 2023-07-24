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
		"logIhistoryModel",			// model name
		"l_ihistory",
		"ihistory_id",
		array(
			"interview_id",
			"ihistory_type",	
			"data1",
			"data2",
			"data3",
			"data4"),	

		array("auto_inc" => true, 
			"operator_info" => true,
			"del_flag" => false));

	class logIhistoryModel extends model // 会诊状态历史信息模型
	{
		public static function log($interview, $ihistory_type, $data1=null, $data2=null, $data3=null, $data4=null)
		{
			$log = new static;

			$log->interview_id = $interview->interview_id;
			$log->ihistory_type = $ihistory_type;
			$log->data1 = $data1;
			$log->data2 = $data2;
			$log->data3 = $data3;
			$log->data4 = $data4;

			$err = $log->insert();
			if ($err == ERR_OK)
			{
				$log->push_message($interview);
			}

			return $err;
		}

		public function push_message($interview)
		{
			switch($this->ihistory_type) {
				case IHTYPE_RESERVED:
					break;
				case IHTYPE_CHANGED_COST:
					break;
				case IHTYPE_EXPIRED_PAY:
					break;
				case IHTYPE_PAYED:
					if ($interview->need_interpreter) {
						// 所有符合该翻译类型的翻译
						$interview->notify_to_all_interp();
					}
					else {
						_push_message($interview->patient_id, 
							_ll("会诊已生效提醒"),
							_ll("您的会诊申请已生效，时间：{0} {1}-{2}，请提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
						_push_message($interview->doctor_id, 
							_ll("会诊已生效提醒"),
							_ll("您有新的会诊预约，时间：{0} {1}-{2}，请提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
						_push_message($interview->doctor_id, 
							_ll("病历已上传提醒"),
							_ll("会诊编号为%s的患者病历已上传，请您及时查看。", $interview->interview_id));
					}
					break;
				case IHTYPE_INTERP_ACCEPT:
					_push_message($interview->patient_id, 
						_ll("会诊已生效提醒"),
						_ll("您的会诊申请已生效，时间：{0} {1}-{2}，请提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					_push_message($interview->doctor_id, 
						_ll("会诊已生效提醒"),
						_ll("您有新的会诊预约，时间：{0} {1}-{2}，请提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					_push_message($interview->interpreter_id, 
						_ll("成功接单提醒"),
						_ll("您已成功接单，时间：{0} {1}-{2}，请及时查看并翻译病历，提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					break;
				case IHTYPE_INVITE_INTERP:
					if ($this->data2 != ISTATUS_OPENED) {
						_push_message($interview->patient_id, 
						 	_ll("会诊已生效提醒"),
						 	_ll("您的会诊申请已生效，时间：{0} {1}-{2}，请提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
						_push_message($interview->doctor_id, 
						 	_ll("会诊已生效提醒"),
						 	_ll("您有新的会诊预约，时间：{0} {1}-{2}，请提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					}
					_push_message($interview->interpreter_id, 
						_ll("成功接单提醒"),
						_ll("您已成功接单，时间：{0} {1}-{2}，请及时查看并翻译病历，提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					break;
				case IHTYPE_BROWSED_IHISTORY:
					_push_message($interview->patient_id, 
						_ll("查看病历提醒"),
						_ll("您预约的专家已查看您的病历。"));
					break;
				case IHTYPE_START_INTERVIEW:
					break;
				case IHTYPE_FINISHED_INTERVIEW:
					break;
				case IHTYPE_CANCELED_INTERVIEW:
					if ($interview->need_interpreter && 
						$interview->patient_starttime == null && 
						$interview->doctor_starttime == null &&
						$interview->interpreter_starttime == null) {
						// 专家、患者和翻译都没有按时上线
						_push_message($interview->patient_id, 
							_ll("患者没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
						_push_message($interview->doctor_id, 
							_ll("专家没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
						_push_message($interview->interpreter_id, 
							_ll("翻译没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
					}
					else if ($interview->need_interpreter && 
						$interview->patient_starttime == null && 
						$interview->interpreter_starttime == null) {
						// 患者和翻译没有在规定时间内上线
						_push_message($interview->patient_id, 
							_ll("患者没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
						_push_message($interview->doctor_id, 
							_ll("患者和翻译没在规定时间内上线提醒"),
							_ll("由于患者和翻译没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
						_push_message($interview->interpreter_id, 
							_ll("翻译没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
					}
					else if ($interview->patient_starttime == null && 
						$interview->doctor_starttime == null) {
						// 专家和患者没有在规定时间内上线
						_push_message($interview->patient_id, 
							_ll("患者没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
						_push_message($interview->doctor_id, 
							_ll("专家没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
						if ($interview->need_interpreter) {
							_push_message($interview->interpreter_id, 
								_ll("专家和患者没在规定时间内上线提醒"),
								_ll("由于专家和患者没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
						}
					}
					else {
						// 只有患者没在规定时间内上线
						_push_message($interview->patient_id, 
							_ll("患者没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）已失效。", $interview->interview_id));
					}
					break;
				case IHTYPE_UNFINISHED_INTERVIEW:
					if ($interview->need_interpreter &&
						$interview->doctor_starttime == null &&
						$interview->interpreter_starttime == null) {
						// 专家和翻译未按时登录会诊室
						_push_message($interview->patient_id, 
							_ll("专家和翻译没在规定时间内上线提醒"),
							_ll("由于专家和翻译没在规定时间内上线，本次会诊（编号：%s）未完成，请您耐心等候专家更改会诊时间通知。", $interview->interview_id));
						_push_message($interview->doctor_id, 
							_ll("专家和翻译没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）未完成，请您及时登录系统更改会诊时间。", $interview->interview_id));
						_push_message($interview->interpreter_id, 
							_ll("专家和翻译没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）未完成，请您耐心等候系统通知。", $interview->interview_id));
						userModel::message_to_nation_admin($interview->patient_id,
							_ll("专家和翻译没在规定时间内上线提醒"), 
							_ll("由于专家和翻译没在规定时间内上线，本次会诊（编号：%s）未完成，请您及时登录系统查看并跟进。", $interview->interview_id));
					}
					else if ($interview->doctor_starttime == null) {
						// 专家未按时登录会诊室
						_push_message($interview->patient_id, 
							_ll("专家没在规定时间内上线提醒"),
							_ll("由于专家没在规定时间内上线，本次会诊（编号：%s）未完成，请您耐心等候专家更改会诊时间通知。", $interview->interview_id));
						_push_message($interview->doctor_id, 
							_ll("专家没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）未完成，请您及时登录系统更改会诊时间。", $interview->interview_id));
						if ($interview->need_interpreter) {
							_push_message($interview->interpreter_id, 
								_ll("专家没在规定时间内上线提醒"),
								_ll("由于专家没在规定时间内上线，本次会诊（编号：%s）未完成，请您耐心等候专家更改会诊时间通知。", $interview->interview_id));
						}
						userModel::message_to_nation_admin($interview->patient_id,
							_ll("专家没在规定时间内上线提醒"), 
							_ll("由于专家没在规定时间内上线，本次会诊（编号：%s）未完成，请您及时登录系统查看并跟进。", $interview->interview_id));
					}
					else if ($interview->need_interpreter &&
						$interview->interpreter_starttime == null) {
						// 翻译未按时登录会诊室
						_push_message($interview->patient_id, 
							_ll("翻译没在规定时间内上线提醒"),
							_ll("由于翻译没在规定时间内上线，本次会诊（编号：%s）未完成，请您耐心等候专家更改会诊时间通知。", $interview->interview_id));
						_push_message($interview->doctor_id, 
							_ll("翻译没在规定时间内上线提醒"),
							_ll("由于翻译没在规定时间内上线，本次会诊（编号：%s）未完成，请您及时登录系统更改会诊时间。", $interview->interview_id));
						_push_message($interview->interpreter_id, 
							_ll("翻译没在规定时间内上线提醒"),
							_ll("由于您没在规定时间内上线，本次会诊（编号：%s）未完成，请您耐心等候系统通知。", $interview->interview_id));
						userModel::message_to_nation_admin($interview->patient_id,
							_ll("翻译没在规定时间内上线提醒"), 
							_ll("由于翻译没在规定时间内上线，本次会诊（编号：%s）未完成，请您及时登录系统查看并跟进。", $interview->interview_id));
					}
					break;
				case IHTYPE_UPLOAD_PRESCRIPT:
					if ($interview->need_interpreter) {
						_push_message($interview->interpreter_id, 
							_ll("第二诊疗意见已上传提醒"),
							_ll("会诊编号为%s的第二诊疗意见已上传，请您及时查看并翻译。", $interview->interview_id));
					}
					else {
						_push_message($interview->patient_id, 
							_ll("第二诊疗意见已上传提醒"),
							_ll("会诊编号为%s的第二诊疗意见已上传，请您及时查看。", $interview->interview_id));
					}
					break;
				case IHTYPE_REFUNDED:
					break;
				case IHTYPE_CLOSE:
					break;
				case IHTYPE_CHANGED_TIME:
					_push_message($interview->patient_id, 
						_ll("专家更改时间提醒"),
						_ll("会诊编号为%s的会诊时间已被更改为{1} {2}-{3}，请提前做好会诊准备。", $interview->interview_id, _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					if ($this->data2 == 2) {
						// 由于专家和翻译原因更改会诊时间
						_push_message($interview->doctor_id,
							_ll("专家更改时间提醒"), 
							_ll("会诊编号为%s的会诊时间已被更改为{1} {2}-{3}，请提前做好会诊准备。", $interview->interview_id, _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					}
					if ($interview->need_interpreter) {
						_push_message($interview->interpreter_id,
							_ll("专家更改时间提醒"), 
							_ll("会诊编号为%s的会诊时间已被更改为{1} {2}-{3}，请提前做好会诊准备。", $interview->interview_id, _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					}
					if ($this->data2 == 1 ||
						$this->data2 == 2) {
						// 由于专家原因更改时间
						// 由于翻译原因更改时间
						userModel::message_to_nation_admin($interview->patient_id,
							_ll("专家更改时间提醒"), 
							_ll("会诊编号为%s的会诊时间已被更改为{1} {2}-{3}，请提前做好会诊准备。", $interview->interview_id, _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					}
					break;
				case IHTYPE_DOCTOR_CANCELED:
					break;
				case IHTYPE_PATIENT_CANCELED:
					if ($interview->status == ISTATUS_PAYED ||
						$interview->status == ISTATUS_OPENED) {
						_push_message($interview->doctor_id, 
							_ll("患者取消订单提醒"),
							_ll("会诊编号为%s的会诊已被患者取消。", $interview->interview_id));
						_push_message($interview->interpreter_id, 
							_ll("患者取消订单提醒"),
							_ll("会诊编号为%s的会诊已被患者取消。", $interview->interview_id));
					}
					break;
				case IHTYPE_INTERP_CANCELED:
					break;
				case IHTYPE_ADMIN_CANCELED:
					break;
				case IHTYPE_PATIENT_PAY_CANCELED:
					_push_message($interview->doctor_id,
						_ll("患者取消订单提醒"), 
						_ll("会诊编号为%s的会诊已被患者取消。", $interview->interview_id));
					_push_message($interview->interpreter_id,
						_ll("患者取消订单提醒"), 
						_ll("会诊编号为%s的会诊已被患者取消。", $interview->interview_id));
					break;
				case IHTYPE_NO_INTERPRETER:
					_push_message($interview->patient_id, 
						_ll("无翻译接单提醒"),
						_ll("由于无翻译接单，本次会诊（编号：%s）已失效，该订单已付金额将原路退回至您的账户。", $interview->interview_id));
					_push_message($interview->doctor_id, 
						_ll("无翻译接单提醒"),
						_ll("由于无翻译接单，本次会诊（编号：%s）已失效。", $interview->interview_id));
					break;
				case IHTYPE_UPLOAD_T_CHISTORY:
					_push_message($interview->doctor_id, 
						_ll("病历已上传提醒"),
						_ll("会诊编号为%s的患者病历已上传，请您及时查看。", $interview->interview_id));
					break;
				case IHTYPE_UPLOAD_T_PRESCRIPT:
					_push_message($interview->patient_id, 
						_ll("第二诊疗意见已上传提醒"),
						_ll("会诊编号为%s的第二诊疗意见已上传，请您及时查看。", $interview->interview_id));
					break;
				case IHTYPE_REJECTED_PATIENT:
					_push_message($interview->patient_id, 
						_ll("专家拒绝接受患者提醒"),
						_ll("会诊编号为%s的预约申请已被专家拒绝（理由：%s），请您及时登录系统重新预约。", $interview->interview_id, $this->data2));
					userModel::message_to_nation_admin($interview->patient_id,
						_ll("专家拒绝接受患者提醒"), 
						_ll("会诊编号为%s的预约申请已被专家拒绝（理由：%s），请您及时登录系统查看并跟进。", $interview->interview_id, $this->data2));
					break;
				case IHTYPE_RERESERVED:
					_push_message($interview->patient_id, 
						_ll("会诊已生效提醒"),
						_ll("您的会诊申请已生效，时间：{0} {1}-{2}，请提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					_push_message($interview->doctor_id, 
						_ll("会诊已生效提醒"),
						_ll("您有新的会诊预约，时间：{0} {1}-{2}，请提前做好会诊准备。", _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					_push_message($interview->interpreter_id,
						_ll("患者更改时间提醒"), 
						_ll("会诊编号为%s的会诊时间已被更改为{1} {2}-{3}，请提前做好会诊准备。", $interview->interview_id, _ll_date($interview->reserved_starttime), _ll_time($interview->reserved_starttime), _ll_time($interview->reserved_endtime)));
					break;
			}
		}

		public function message()
		{
			switch($this->ihistory_type) {
				case IHTYPE_RESERVED:
					$t = _l("预约会诊"); break;
				case IHTYPE_CHANGED_COST:
					$t = _l("会诊费变更为%s", $this->data1); break;
				case IHTYPE_EXPIRED_PAY:
					$t = _l("%s分钟内未付款", $this->data1); break;
				case IHTYPE_PAYED:
					$t = _l("%s成功支付", _l($this->data1)); break;
				case IHTYPE_INTERP_ACCEPT:
					$t = _l("翻译接单 %s", $this->data1); break;
				case IHTYPE_INVITE_INTERP:
					$t = _l("指派翻译 %s", $this->data1); break;
				case IHTYPE_BROWSED_IHISTORY:
					$t = _l("专家已查看病历"); break;
				case IHTYPE_START_INTERVIEW:
					$t = _l("开始会诊"); break;
				case IHTYPE_FINISHED_INTERVIEW:
					$t = _l("结束会诊"); break;
				case IHTYPE_UPLOAD_PRESCRIPT:
					$t = _l("专家上传第二诊疗意见"); break;
				case IHTYPE_REFUNDED:
					$t = _l("已退款 退款金额：%s\n理由：%s", $this->data1, $this->data2); break;
				case IHTYPE_CLOSE:
					$t = _l("订单已关闭\n理由：%s", $this->data1); break;
				case IHTYPE_CHANGED_TIME:
					$t = _l("专家更改时间 原会诊时间：%s", $this->data1); break;
				case IHTYPE_DOCTOR_CANCELED:
					$t = _l("专家取消会诊\n理由：%s", $this->data1); break;
				case IHTYPE_PATIENT_CANCELED:
					$t = _l("患者取消预约\n理由：%s", $this->data1); break;
				case IHTYPE_INTERP_CANCELED:
					$t = _l("翻译取消订单\n理由：%s", $this->data1); break;
				case IHTYPE_ADMIN_CANCELED:
					$t = _l("管理员取消预约\n理由：%s", $this->data1); break;
				case IHTYPE_PATIENT_PAY_CANCELED:
					$t = _l("患者取消预约 违约金: %s\n理由：%s", $this->data2, $this->data1); break;
				case IHTYPE_NO_INTERPRETER:
					$t = _l("无翻译接单"); break;
				case IHTYPE_UPLOAD_T_CHISTORY:
					$t = _l("翻译%s上传翻译版病历", $this->data1); break;
				case IHTYPE_UPLOAD_T_PRESCRIPT:
					$t = _l("翻译%s上传翻译版第二诊疗意见", $this->data1); break;
				case IHTYPE_CANCELED_INTERVIEW:
					$my_type = _my_type();
					$need_interpreter = ($this->data1 == "1");
					$p_online = ($this->data2 == "1");
					$d_online = ($this->data3 == "1");
					$i_online = ($this->data4 == "1");
					if ($need_interpreter && !$p_online && !$d_online && !$i_online)  {
						if ($my_type == UTYPE_PATIENT ||
							$my_type == UTYPE_DOCTOR || 
							$my_type == UTYPE_INTERPRETER)
							$note = _l("您没在规定时间内进入会诊室");
						else // UTYPE_SUPER || UTYPE_ADMIN
							$note = _l("患者、专家和翻译没在规定时间内进入会诊室");
					}
					else if ($need_interpreter && !$p_online && !$i_online) {
						if ($my_type == UTYPE_PATIENT ||
							$my_type == UTYPE_INTERPRETER)
							$note = _l("您没在规定时间内进入会诊室");
						else // UTYPE_SUPER || UTYPE_ADMIN || UTYPE_DOCTOR
							$note = _l("患者和翻译没在规定时间内进入会诊室");
					}
					else if (!$p_online && !$d_online) {
						if ($my_type == UTYPE_PATIENT ||
							$my_type == UTYPE_DOCTOR)
							$note = _l("您没在规定时间内进入会诊室");
						else // UTYPE_SUPER || UTYPE_ADMIN || UTYPE_INTERPRETER
							$note = _l("患者和专家没在规定时间内进入会诊室");
					}
					else {
						if ($my_type == UTYPE_PATIENT)
							$note = _l("您没在规定时间内进入会诊室");
						else // UTYPE_SUPER || UTYPE_ADMIN
							$note = _l("患者没在规定时间内进入会诊室");
					}

					$t = _l("会诊已失效\n理由：%s", $note); break;
				case IHTYPE_UNFINISHED_INTERVIEW:
					$my_type = _my_type();
					$need_interpreter = ($this->data1 == "1");
					$p_online = ($this->data2 == "1");
					$d_online = ($this->data3 == "1");
					$i_online = ($this->data4 == "1");
					if ($need_interpreter && !$d_online && !$i_online) {
						// 专家和翻译没在规定时间内进入会诊室
						if ($my_type == UTYPE_DOCTOR ||
							$my_type == UTYPE_INTERPRETER)
							$note = _l("您没在规定时间内进入会诊室");
						else
							$note = _l("专家和翻译没在规定时间内进入会诊室");
					}
					else if (!$d_online) {
						// 专家没在规定时间内进入会诊室
						if ($my_type == UTYPE_DOCTOR)
							$note = _l("您没在规定时间内进入会诊室");
						else
							$note = _l("专家没在规定时间内进入会诊室");
					}
					else if ($need_interpreter && !$i_online) {
						// 翻译没在规定时间内进入会诊室
						if ($my_type == UTYPE_INTERPRETER)
							$note = _l("您没在规定时间内进入会诊室");
						else
							$note = _l("翻译没在规定时间内进入会诊室");
					}
					$t = _l("会诊未完成\n理由：%s", $note); break;
				case IHTYPE_FINISHED_INTERP:
					$t = _l("翻译%s完成会诊翻译", $this->data1); break;
				case IHTYPE_ACCEPTED_PATIENT:
					$t = _l("专家%s已接受患者", $this->data1); break;
				case IHTYPE_REJECTED_PATIENT:
					$t = _l("专家%s已拒绝患者\n理由：%s", $this->data1, $this->data2); break;
				case IHTYPE_RERESERVED:
					$t = _l("重新预约会诊"); break;
				default:
					$t = "";
			}

			return $t;
		}

		public static function get_logs($interview_id)
		{
			$logs = array();
			$log = new static;

			$err = $log->select('interview_id=' . _sql($interview_id), array("order" => "ihistory_id ASC"));
			while ($err == ERR_OK) {
				$t = $log->message();
				$log->time = _date_time($log->create_time, "Y-m-d H:i");
				$log->message =  $t;

				$logs[] = clone $log;
				$err = $log->fetch();
			}

			return $logs;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/