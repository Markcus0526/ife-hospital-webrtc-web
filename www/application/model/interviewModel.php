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
		"interviewModel",			// model name
		"t_interview",
		"interview_id",
		array(
			"i_finshed_interview_id",// 翻译的已完成会诊ID
			"patient_id",			// 患者ID
			"doctor_id",			// 专家ID
			"interpreter_id",		// 翻译ID
			"reserved_starttime",	// 会诊开始预定时刻
			"reserved_endtime",		// 会诊完毕预定时刻
			"chistory_id",			// 原版病历ID
			"trans_chistory_id",	// 翻译版病历ID
			"planguage_id",			// 患者精通语言
			"dlanguage_id",			// 专家精通语言
			"need_interpreter",		// 是否需要翻译
			"status",				// 状态
			"sub_status",			// 部分状态
			"cancel_cause_id",		// 取消原因
			"cancel_cause_note",	// 取消原因详细
			"reject_cause_id",		// 拒绝理由
			"reject_cause_note",	// 拒绝理由详细
			"interview_starttime",	// 会诊开始时刻
			"interview_endtime",	// 会诊完毕时刻
			"interview_seconds",	// 会诊时长
			"record_video",			// 
			"patient_status",		// 患者上线状态
			"patient_starttime",	// 患者开始时刻
			"patient_leavetime",	// 患者掉线时刻
			"patient_ip",			// 患者IP
			"doctor_status",		// 专家上线状态
			"doctor_starttime",		// 专家开始时刻
			"doctor_leavetime",		// 专家掉线时刻
			"doctor_ip",			// 专家IP
			"interpreter_status",	// 翻译上线状态
			"interpreter_starttime",// 翻译开始时刻
			"interpreter_leavetime",// 翻译掉线时刻
			"interpreter_ip",		// 翻译IP
			"cost",					// 会诊费
			"d_cost",				// 专家费
			"i_cost",				// 翻译费
			"cunit",				// 货币单位
			"filesize",
			"doctor_sign",			// 专家签字
			"prescription",			// 原版第二诊疗意见
			"trans_prescription",	// 翻译版第二诊疗意见
			"chat_file",			// 讨论内容
			"change_time_at",		// 更改时间时刻
			"org_reserved_starttime",// 原会诊开始预定时刻
			"pay_id",				// 支付方式
			"pay_time",				// 支付时刻
			"payment_id",			// Paypal退款ID
			"refund_amount",		// 退款金额
			"refund_time",			// 退款时刻
			"notify_interp_time"	// 向符合类型翻译通知时刻
		),
		array("rand_id" => true, 
			"operator_info" => true));

	class interviewModel extends model // 会诊信息模型
	{
		public function new_id($field_name)
		{
			$country_id = _country_id();
			if ($country_id == null)
				$country_id = 1;
			$country_id = str_pad($country_id, 2, "0", STR_PAD_LEFT);
			// 2017(年)09(月)01(国家:中国01)01(类型:会诊04)0001(时间顺序)
			$prefix = date('Ym') . $country_id . "04";

			$min = $prefix * 10000;
			$max = $prefix * 10000 + 10000;

			$db = db::get_db();
			$no = $db->scalar("SELECT MAX(".$field_name.") % 10000 + 1 FROM " . $this->table . " WHERE ".$field_name.">" . _sql($min) . " AND ".$field_name."<" . _sql($max));

			if ($no == null)
				$no = 1;

			return $prefix * 10000 + $no;
		}

		public function refresh_status()
		{
			$refreshed = false;
			switch ($this->status) {
				case ISTATUS_NONE: // 待付款
					$now = _DateTime(); 
					$pay_limit_time = _DateTime($this->create_time)->modify(INTERVIEW_PAY_LIMIT . " minutes");
					$this->pay_limit_tm = $pay_limit_time->getTimestamp();
					$this->now_tm = $now->getTimestamp();
					$this->pay_limit = $this->pay_limit_tm - $this->now_tm;

					if ($this->pay_limit <= 0) {
						// 已失效
						$this->status = ISTATUS_CANCELED;
						$this->sub_status = SISTATUS_CANCEL_BEFORE_PAY;
						$this->pay_limit = 0;

						if ($this->update(array('status', 'sub_status')) == ERR_OK) {
							logIhistoryModel::log($this, IHTYPE_EXPIRED_PAY, INTERVIEW_PAY_LIMIT);
						}
						$refreshed = true;	
					}
					break;
				case ISTATUS_PAYED: // 已付款
					if ($this->need_interpreter && $this->interpreter_id == null)
					{
						$now = _date_time();
						$reserved_starttime = _date_time($this->reserved_starttime);
						if ($now > $reserved_starttime)
						{
							// 到了会诊开始时间，仍没有翻译接单
							$this->status = ISTATUS_CANCELED;
							$this->sub_status = 0;

							if ($this->update('status', 'sub_status') == ERR_OK) {
								logIhistoryModel::log($this, IHTYPE_NO_INTERPRETER);
								$this->refund_penalty(0, _l("无翻译接单"));
							}
							$refreshed = true;	
						}
					}

					break;
				case ISTATUS_OPENED: // 已生效
					$now = _date_time();
					$this->before_starttime = ($now < $this->reserved_starttime);

					if ($this->reserved_starttime < $now)
					{
						$this->status = ISTATUS_PROGRESSING;
						$this->interview_starttime = _date_time();
						$this->patient_status = OFFLINE;
						$this->doctor_status = OFFLINE;
						$this->interpreter_status = OFFLINE;
						$err = $this->update();
						if ($err == ERR_OK)
						{
							logIhistoryModel::log($this, IHTYPE_START_INTERVIEW);
						}
						$refreshed = true;	
					}
					break;
				
				case ISTATUS_PROGRESSING: // 进行中
					$now = _date_time();
					$this->before_starttime = false;
					
					$limit_time = _date_time(_DateTime($this->reserved_starttime)->modify( INTERVIEW_OFFLINE_LIMIT . " minutes"));
					// 对话者没在规定时间内上线
					if ($limit_time < $now && $this->doctor_starttime == null)
					{
						// 自动结束
						$this->finish(null);
						$refreshed = true;	
					}
					else {
						if ($this->doctor_starttime && 
							$this->doctor_starttime < $this->doctor_leavetime) 
						{
							// 专家离线后没在规定时间内上线
							$limit_time = _date_time(_DateTime($this->doctor_leavetime)->modify( INTERVIEW_REOFFLINE_LIMIT . " minutes"));
							if ($limit_time < $now)
							{
								// 自动结束
								$this->finish(null);
								$refreshed = true;			
							}
						}
					}
					break;

				default:
					break;
			}

			if ($this->status == ISTATUS_PAYED || $this->status == ISTATUS_OPENED)
			{
				if (!($this->sub_status & SISTATUS_ALERTED_START_ALARM)) {
					$now = _DateTime(); 
					$alarm_time = _DateTime($this->reserved_starttime)->modify("-" . INTERVIEW_ALARM_START . " minutes");
					$diff = $alarm_time->getTimestamp() - $now->getTimestamp();
					$diff = floor($diff / 60);

					if ($diff <= 0) {
						_push_message($this->patient_id, _ll("会诊前提醒"), _ll("距离会诊开始还有%s分钟，请您做好会诊准备。", INTERVIEW_ALARM_START));
						_push_message($this->doctor_id, _ll("会诊前提醒"), _ll("距离会诊开始还有%s分钟，请您做好会诊准备。", INTERVIEW_ALARM_START));
						_push_message($this->interpreter_id, _ll("会诊前提醒"), _ll("距离会诊开始还有%s分钟，请您做好会诊准备。", INTERVIEW_ALARM_START));

						$this->sub_status |= SISTATUS_ALERTED_START_ALARM;
						$this->update('sub_status');
						$refreshed = true;
					}	
				}

				if (!($this->sub_status & SISTATUS_ALERT_NO_INTERP)) {
					if ($this->need_interpreter && $this->interpreter_id == null && $this->notify_interp_time != null)
					{
						$now = _DateTime(); 
						$bid_limit_time = _DateTime($this->reserved_starttime)->modify("-" . INTERVIEW_NO_INTERP_ALARM_LIMIT . " hours");
						$bid_limit_tm = $bid_limit_time->getTimestamp();
						$now_tm = $now->getTimestamp();
						$bid_limit = $bid_limit_tm - $now_tm;
						if ($bid_limit <= 0) {
							userModel::message_to_nation_admin($this->patient_id, _ll("规定时间内翻译未接单提醒"), _ll("会诊编号为%s的预约申请无翻译接单，请指派翻译。", $this->interview_id, INTERVIEW_NO_INTERP_ALARM_LIMIT));

							$this->sub_status |= SISTATUS_ALERT_NO_INTERP;
							$this->update('sub_status');
							$refreshed = true;
						}
					}
				}
			}

			return $refreshed;
		}

		public static function browse_chistory($interview_id)
		{
			$my_id = _my_id();

			$interview = static::get_model($interview_id);

			if ($interview == null)
				return ERR_NODATA;

			if ($my_id != $interview->doctor_id)
				return ERR_NOPRIV;

			$log = new logIhistoryModel;
			$err = $log->select("interview_id=" . _sql($interview_id) . 
				" AND ihistory_type=" . IHTYPE_BROWSED_IHISTORY);
			if ($err == ERR_NODATA)
				logIhistoryModel::log($interview, IHTYPE_BROWSED_IHISTORY);

			return ERR_OK;
		}

		public function save_interp($planguage_id, $dlanguage_ids)
		{
			$db = db::get_db();

			$err = $db->execute("DELETE FROM t_interp WHERE interview_id=" . _sql($this->interview_id));
			if ($err != ERR_OK)
				return $err;

			foreach ($dlanguage_ids as $dlanguage_id) {
				if ($planguage_id != $dlanguage_id) {
					$interp = new interpModel;
					$interp->interview_id = $this->interview_id;
					$interp->planguage_id = $planguage_id;
					$interp->dlanguage_id = $dlanguage_id;

					$err = $interp->save();
					if ($err != ERR_OK)
						return $err;	
				}
			}

			return ERR_OK;
		}

		public function save_attaches($upload_id) 
		{
			$attaches = $this->$upload_id;
			$attaches = preg_split("/;/", $attaches);
			$new_attaches = array();

			foreach($attaches as $attach)
			{
				$pf = preg_split("/:/", $attach);
				$path = $pf[0]; $file_name = _safe_filename($pf[1]); $ext = _ext($file_name);
				if (substr($path, 0, 3) == "tmp") {
					$dir = ATTACH_URL . date('Y/m') . "/";

					$path = substr($path, 4);
					
					$attach_url = $dir . "i" . $this->interview_id . "_" . $path;
					$real_path = DATA_PATH . $attach_url;
					@unlink($real_path);
					@_mkdir(DATA_PATH . $dir);
					@rename(TMP_PATH . $path, $real_path);

					if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp" || $ext == "gif" || $ext == "tiff") {
						// create thumbnail
						$thumb_path = $real_path . "_thumb.jpg";
						copy($real_path, $thumb_path);
						_resize_image($thumb_path, $ext, "jpg", THUMB_SIZE, THUMB_SIZE, RESIZE_CONTAIN);

					}

					array_push($new_attaches, $attach_url . ":" . $file_name . ":" . $pf[2]);
				}
				else if ($path != "") {
					array_push($new_attaches, $attach);
				}
			}

			$this->$upload_id = implode(";", $new_attaches);

			return ERR_OK;
		}

		public function save_chatfile_and_clear()
		{
			$dir = CHAT_URL . date('Y/m') . "/";
			
			$url = $dir . $this->interview_id . ".txt";
			$real_path = DATA_PATH . $url;
			@unlink($real_path);
			@_mkdir(DATA_PATH . $dir);

			$messages = imessageModel::all($this->interview_id, null);

			if (count($messages)) {
				$content = "";
				foreach ($messages as $msg) {
					$content .= $msg["user_name"] . ":" . $msg["content"] . "\n";
				}

				_fwrite($real_path, $content);

				imessageModel::clear($this->interview_id);

				$this->chat_file = $url;
			}
			else
				$this->chat_file = " ";

			return $this->update("chat_file");
		}

		public function remove_attaches($upload_id=null)
		{
			if ($upload_id == null) {
				$this->remove_attaches("prescription");
			}
			else {
				$attaches = $this->$upload_id;
				$attaches = preg_split("/;/", $attaches);

				foreach($attaches as $attach)
				{
					$pf = preg_split("/:/", $attach);
					$path = $pf[0];
					if ($path != "") {
						$real_path = DATA_PATH . $path;
						@unlink($real_path);
					}
				}
			}
		}

		public static function totals($search)
		{
			$my_id = _my_id();
			$my_type = _my_type();
			$where = "i.del_flag=0 AND i.status=" . ISTATUS_FINISHED;
			if ($my_type == UTYPE_PATIENT)
				$where .= " AND i.patient_id=" . _sql($my_id);
			else if ($my_type == UTYPE_DOCTOR)
				$where .= " AND i.doctor_id=" . _sql($my_id);
			else if ($my_type == UTYPE_INTERPRETER)
				$where .= " AND i.interpreter_id=" . _sql($my_id);

			if ($search->query != "") {
				$like = _sql("%" . $search->query . "%");
				$where .= " AND (i.interview_id LIKE " . $like  . " OR p.user_name LIKE " . $like  . " OR d.user_name LIKE " . $like . " OR n.user_name LIKE " . $like . ")";
			}

			if ($search->from_date) {
				$where .= " AND i.reserved_starttime>=" . _sql($search->from_date);
			}
			if ($search->to_date) {
				$where .= " AND i.reserved_starttime<=" . _sql($search->to_date . " 23:59:59");
			}

			$interview = new model;
			$err = $interview->query("SELECT COUNT(i.interview_id) counts, SUM(i.interview_seconds) seconds 
				FROM t_interview i 
				LEFT JOIN m_user p ON i.patient_id=p.user_id
				LEFT JOIN m_user d ON i.doctor_id=d.user_id
				LEFT JOIN m_user n ON i.interpreter_id=n.user_id 
				WHERE " . $where);

			if ($interview->seconds == null)
				$interview->seconds = 0;
			return $interview;
		}

		public function check_already()
		{
			$o_interview = new static;
			if (_my_type() == UTYPE_PATIENT) {
				$where = "(doctor_id=" . _sql($this->doctor_id) . " AND patient_id=" . _sql($this->patient_id)  . ") " .
					" AND status IN (" . ISTATUS_NONE . "," . ISTATUS_PAYED . "," . ISTATUS_OPENED . "," . ISTATUS_PROGRESSING . ")";
				if ($this->interview_id != "")
					$where .= " AND interview_id!=" . _sql($this->interview_id);
				$err = $o_interview->select($where);
				if ($err == ERR_OK) {
					// 当前已有预约，不能重复预约。
					return ERR_YOU_DUP_INTERVIEW;
				}
			}
			$where = "reserved_starttime=" . _sql($this->reserved_starttime) . 
				" AND (doctor_id=" . _sql($this->doctor_id) . " OR patient_id=" . _sql($this->patient_id)  . ") " .
				" AND status IN (" . ISTATUS_NONE . "," . ISTATUS_PAYED . "," . ISTATUS_OPENED . "," . ISTATUS_PROGRESSING . ")";
			if ($this->interview_id != "")
				$where .= " AND interview_id!=" . _sql($this->interview_id);
			$err = $o_interview->select($where);
			if ($err == ERR_OK) {
				if (_my_type() == UTYPE_PATIENT) {
					if ($o_interview->patient_id == $this->patient_id)
					// 当前已有预约，不能重复预约。
						return ERR_YOU_ONLY_ONE_DOCTOR;
					else
					// 该会诊时间已被预约，请您重新预约。
						return ERR_OTHER_DUP_INTERVIEW;	
				}
				else { // doctor
					return ERR_PATIENT_DUP_INTERVIEW;
				}
			}

			return ERR_OK;
		}

		public function check_dtime()
		{
			$dtime = new dtimeModel;
			$err = $dtime->select("doctor_id=" . _sql($this->doctor_id) . " AND start_time=" . _sql($this->reserved_starttime));

			if ($err != ERR_OK || $dtime->state != TSTATE_ENABLE)
				return ERR_INVALID_DTIME;

			return ERR_OK;
		}

		public function start($user_id, $utype)
		{
			$err = ERR_OK;

			if ($utype == UTYPE_ADMIN || $utype == UTYPE_SUPER)
			{
				if ($this->status != ISTATUS_PROGRESSING)
					return ERR_INTERVIEW_NOT_PROGRESS;
			}
			else {
				if ($this->patient_id == $user_id) {
					$this->patient_status = ONLINE;
					$this->patient_starttime = _date_time();
				}
				else if ($this->doctor_id == $user_id) {
					$this->doctor_status = ONLINE;
					$this->doctor_starttime = _date_time();
				}
				else if ($this->interpreter_id == $user_id) {
					$this->interpreter_status = ONLINE;
					$this->interpreter_starttime = _date_time();
				}
				else {
					return ERR_NOPRIV;
				}

				if ($this->interview_starttime == null) {
					$this->interview_starttime = _date_time();
				}

				if ($this->status != ISTATUS_PROGRESSING)
				{
					$this->status = ISTATUS_PROGRESSING;
					logIhistoryModel::log($this, IHTYPE_START_INTERVIEW);
				}
				
				$err = $this->update();				
			}

			// 专家一直没上线
			if ($this->doctor_starttime == null)
			{
				$now = _DateTime(); 
				$offline_limit_time = _DateTime($this->reserved_starttime)->modify(INTERVIEW_ALARM_OFFLINE_LIMIT . " minutes");
				$this->doctor_offline_yet = $offline_limit_time->getTimestamp() - $now->getTimestamp();
				if ($this->doctor_offline_yet < 0)
					$this->doctor_offline_yet = 0.1;
			}
			else
				$this->doctor_offline_yet = false;

			return $err;
		}

		public function leave($user_id)
		{
			$err = ERR_OK;

			if ($this->patient_id == $user_id) {
				if ($this->status == ISTATUS_PROGRESSING)
					$this->patient_status = OFFLINE;
					$this->patient_leavetime = _date_time();
			}
			else if ($this->doctor_id == $user_id) {
				if ($this->status == ISTATUS_PROGRESSING)
					$this->doctor_status = OFFLINE;
					$this->doctor_leavetime = _date_time();
			}
			else if ($this->interpreter_id == $user_id) {
				if ($this->status == ISTATUS_PROGRESSING)
					$this->interpreter_status = OFFLINE;
					$this->interpreter_leavetime = _date_time();
			}
			else {
				return ERR_NOPRIV;
			}

			$err = $this->update();

			return $err;
		}

		public function finish($user_id, $statuses=null)
		{
			$err = ERR_OK;

			if ($this->status == ISTATUS_FINISHED ||
				$this->status == ISTATUS_CANCELED)
				return $err;

			if ($this->patient_id == $user_id) {
				// 自动结束
			}
			else if ($this->doctor_id == $user_id) {
			}
			else if ($this->interpreter_id == $user_id) {
				// 自动结束
			}
			else if ($user_id == null) {
				// 自动结束
			}
			else {
				return ERR_NOPRIV;
			}

			if ($this->interview_starttime == null)
				$this->interview_starttime = $this->reserved_starttime;
			$this->interview_endtime = _date_time();
			$this->interview_seconds = _DateTime($this->interview_endtime)->getTimestamp() - _DateTime($this->interview_starttime)->getTimestamp();

			if ($this->patient_status == ONLINE)
				$this->patient_leavetime = $this->interview_endtime;
			if ($this->doctor_status == ONLINE)
				$this->doctor_leavetime = $this->interview_endtime;
			if ($this->interpreter_status == ONLINE)
				$this->interpreter_leavetime = $this->interview_endtime;

			if ($this->need_interpreter) {
				if ($this->doctor_starttime &&
					$this->patient_starttime && 
					$this->interpreter_starttime) 
					// 专家离开的时候，三方均上线
					$this->status = ISTATUS_FINISHED;
				else if ($this->patient_starttime == null) 
					// 只要患者一直未上线，会诊状态就变为已失效
					$this->status = ISTATUS_CANCELED;
				else
					// 患者上线，专家/翻译/专家和翻译一直没上线，会诊状态变为未完成。
					$this->status = ISTATUS_UNFINISHED;
			}
			else {
				if ($this->doctor_starttime &&
					$this->patient_starttime) 
					// 专家离开的时候，两方均上线
					$this->status = ISTATUS_FINISHED;
				else if ($this->patient_starttime == null) 
					// 只要患者一直未上线，会诊状态就变为已失效
					$this->status = ISTATUS_CANCELED;
				else 
					// 患者上线，专家一直没上线，会诊状态变为未完成。
					$this->status = ISTATUS_UNFINISHED;
			}
			$this->sub_status = SISTATUS_NONE;

			if ($this->status == ISTATUS_CANCELED || 
				$this->status == ISTATUS_UNFINISHED)
			{
				// 已失效 | 未完成
				$this->interview_seconds = 0;
			}

			$err = $this->update();

			if ($err == ERR_OK)
			{
				if ($this->status == ISTATUS_CANCELED) {
					// 患者没在规定时间内进入会诊室
					logIhistoryModel::log($this, IHTYPE_CANCELED_INTERVIEW,
						($this->need_interpreter ? 1 : 0), 
						($this->patient_starttime != null ? 1 : 0), 
						($this->doctor_starttime != null ? 1 : 0),
						($this->interpreter_starttime != null ? 1 : 0));
				}
				else if ($this->status == ISTATUS_UNFINISHED) {
					logIhistoryModel::log($this, IHTYPE_UNFINISHED_INTERVIEW, 
						($this->need_interpreter ? 1 : 0), 
						($this->patient_starttime != null ? 1 : 0), 
						($this->doctor_starttime != null ? 1 : 0),
						($this->interpreter_starttime != null ? 1 : 0));
				}
				else {
					// ISTATUS_FINISHED
					logIhistoryModel::log($this, IHTYPE_FINISHED_INTERVIEW);
					if ($this->need_interpreter) {
						$interpreter = userModel::get_model($this->interpreter_id);
						if ($interpreter) {
							logIhistoryModel::log($this, IHTYPE_FINISHED_INTERP, $interpreter->user_name);	
						}
					}
				}
			}

			return $err;
		}

		public function pay($pay_id, $pay_time, $cost, $payment_id)
		{
			$payment = paybase::get_from_pay_id($pay_id);

			$payed = false;
			if ($payment != null) {
				$payed = $payment->respond($this->interview_id);
			}

			if ($payed) {
				if ($payed !== true) {
					$payment_id = $payed;
				}
				if ($this->status == ISTATUS_NONE)
				{
					if ($this->need_interpreter)
						$this->status = ISTATUS_PAYED;
					else {
						$this->status = ISTATUS_OPENED;
						$this->sub_status |= SISTATUS_D_MUST_ACCEPT_PATIENT;
					}
					$this->pay_id = $pay_id;
					$this->pay_time = _date_time($pay_time);
					$this->cost = base64_decode($cost);
					$this->payment_id = $payment_id;
					if ($pay_id == "chinapay")
						$this->cunit = "rmb";
					else if ($pay_id == "paypal")
						$this->cunit = "usd";

					$err = $this->update(array("status", "sub_status", "pay_id", "pay_time", "cost", "cunit", "payment_id"));

					if ($err == ERR_OK) {
						$pay_label = $payment->pay_name;
						logIhistoryModel::log($this, IHTYPE_PAYED, $pay_label);
					}
				}
			}

			return $err;
		}

		public function notify_to_all_interp()
		{
			// 所有符合该翻译类型的翻译
			interpModel::message_to_all_interpreter($this->interview_id, _ll("新翻译订单提醒"), _ll("您有新的翻译订单，时间：{0} {1}-{2}，请接单。", _ll_date($this->reserved_starttime), _ll_time($this->reserved_starttime), _ll_time($this->reserved_endtime)));

			$this->notify_interp_time = _date_time();
			$this->sub_status &= (~SISTATUS_ALERT_NO_INTERP);

			$err = $this->update(array("notify_interp_time", "sub_status"));

			return $err;
		}

		public function refund_penalty($penalty, $refund_note)
		{
			//$refund_amount = $this->cost - $penalty;
			$refund_amount = $penalty;
			return $this->refund($refund_amount, $refund_note);
		}

		public function refund($refund_amount, $refund_note)
		{
			// 退款金额必须需要小于支付金额
			$refunable_amount = $this->cost - $this->refund_amount;
			$refund_amount = min($refund_amount, $refunable_amount);
			
			if ($refund_amount > 0) {
				$payment = paybase::get_from_pay_id($this->pay_id);

				if ($payment) {
					// 读取从本来订单号流水号
					$org_pay_time = _date_time($this->pay_time, "YmdHis");
					$data = $payment->get_queryid($this->interview_id, $org_pay_time);
					if ($data["error_code"] == "0") {
						$query_id = $data["query_id"];

						$pay_time = date('YmdHis');
						$back_respond_url = _url("pay/respond/".$this->interview_id."/".$pay_time."/".$this->pay_id);
						$refunded = $payment->refund($this->interview_id, $query_id, $refund_amount, $back_respond_url, $this->payment_id);
						if ($refunded) {
							$this->refund_amount += $refund_amount;
							$this->refund_time = _date_time($pay_time);
							$err = $this->update(array("refund_amount", "refund_time"));

							if ($err === ERR_OK)
							{
								logIhistoryModel::log($this, IHTYPE_REFUNDED, _cunit($this->cunit) . $refund_amount, $refund_note);
							}
							return $err;
						}
						return ERR_REFUND_INVALID;
					} else {
						return ERR_REFUND_INVALID;
					}
				}
			}

			return ERR_OK;
		}
		public function is_payed() {
			return $this->status == ISTATUS_PAYED || $this->status == ISTATUS_OPENED; 
		}
		public function can_view_chistory()
		{
			$my_type = _my_type();

			if ($my_type == UTYPE_ADMIN) 
				return _has_priv(CODE_PRIV_INTERVIEWS, PRIV_CHISTORY);

			if ($my_type == UTYPE_DOCTOR)
				return !$this->need_interpreter || 
					$this->need_interpreter && $this->trans_chistory_id;

			return true;
		}

		public function can_view_trans_chistory()
		{
			return $this->trans_chistory_id != null;
		}

		public function can_insert_trans_chistory()
		{
			$my_type = _my_type();

			return $this->trans_chistory_id == null &&
				$this->need_interpreter &&
				$my_type == UTYPE_INTERPRETER &&
				($this->status == ISTATUS_OPENED ||
					$this->status == ISTATUS_PROGRESSING ||
					$this->status == ISTATUS_FINISHED);
		}

		public function can_view_prescription()
		{
			$my_type = _my_type();

			if ($my_type == UTYPE_PATIENT) {
				if ($this->need_interpreter)
					return !_is_empty($this->prescription) && 
						!_is_empty($this->trans_prescription);
				else
					return !_is_empty($this->prescription);
			}

			return ($my_type == UTYPE_ADMIN && _has_priv(CODE_PRIV_INTERVIEWS, PRIV_PRESC) || $my_type != UTYPE_ADMIN) && 
				!_is_empty($this->prescription);
		}

		public function can_upload_prescription()
		{
			$my_type = _my_type();

			return $my_type == UTYPE_DOCTOR &&
				(!$this->need_interpreter || 
					$this->need_interpreter && $this->trans_chistory_id);
		}

		public function can_upload_trans_prescription()
		{
			$my_type = _my_type();

			return $this->can_view_prescription() && 
				$my_type == UTYPE_INTERPRETER &&
				_is_empty($this->trans_prescription);
		}

		public function can_view_trans_prescription()
		{
			$my_type = _my_type();

			return ($my_type == UTYPE_ADMIN && _has_priv(CODE_PRIV_INTERVIEWS, PRIV_PRESC) || $my_type != UTYPE_ADMIN) && 
				!_is_empty($this->trans_prescription);
		}

		public function can_cancel_by_patient()
		{
			$my_type = _my_type();
			return $my_type == UTYPE_PATIENT;
		}

		public function is_disable_cancel_by_patient()
		{
			if ($this->status == ISTATUS_NONE)
				// 待付款
				return false;
			else if ($this->status == ISTATUS_PAYED)
				// 已付款
				return false;
			else if ($this->status == ISTATUS_OPENED)
			{
				// 已生效
				if ($this->sub_status & SISTATUS_D_MUST_ACCEPT_PATIENT)
					// 专家接受前
					return false;

				if ($this->need_interpreter && $this->trans_chistory_id == null)
					// 翻译接单后
					return false;
			}
			else if ($this->status == ISTATUS_WAITING)
				// 专家拒绝后
				return false;

			return true;
		}

		public function can_change_time()
		{
			$my_type = _my_type();

			return $my_type == UTYPE_DOCTOR && 
				($this->status == ISTATUS_PAYED ||
				$this->status == ISTATUS_OPENED ||
				$this->status == ISTATUS_UNFINISHED);
		}

		public function can_invite()
		{
			$my_type = _my_type();

			return $this->need_interpreter && 
				_has_priv(CODE_PRIV_INTERVIEWS, PRIV_INVITE) && 
				($this->status == ISTATUS_PAYED || 
					$this->status == ISTATUS_OPENED || 
					$this->status == ISTATUS_UNFINISHED);
		}

		public function is_must_record()
		{
			return $this->status == ISTATUS_FINISHED;
		}

		public function can_play_record()
		{
			return $this->is_must_record() &&
				_has_priv(CODE_PRIV_INTERVIEWS, PRIV_PLAY) && 
				RECORDING_API != '';
		}

		public function is_d_must_accept_patient()
		{
			$my_type = _my_type();

			return $this->status == ISTATUS_OPENED && 
				$my_type == UTYPE_DOCTOR && 
				($this->sub_status & SISTATUS_D_MUST_ACCEPT_PATIENT);
		}

		public function can_rereserve()
		{
			$my_type = _my_type();

			return ($this->status == ISTATUS_WAITING && 
				($my_type == UTYPE_ADMIN || $my_type == UTYPE_PATIENT));
		}

		public function can_enter_room()
		{
			$my_type = _my_type();
			return ($this->status == ISTATUS_OPENED || 
					$this->status == ISTATUS_PROGRESSING) &&
				_has_priv(CODE_PRIV_INTERVIEWS, PRIV_ENTER);
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/