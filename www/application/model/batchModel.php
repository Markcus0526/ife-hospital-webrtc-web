<?php
	/************************* Copyright Info ***************************
	*   Project Name:       MARKCUS World Tele Clinic System                *
	*   Framework:          MAL MVC Web Framewrok v1.0                  *
	*   Author:             Markcus                                        *
	*   Date:               2017/10/02                                  *
	*                                                                   *
	*   2017 ©      ALL Rights Reserved.                                *
	************************** Copyright Info **************************/

	class batchModel extends model 
	{
		public $silent_mode;

		public $daily_clear_old_session;
		public $daily_clear_old_dtimes;
		public $daily_clear_old_sms;

		public $last_date;

		public function __construct($silent_mode=true){
			$this->silent_mode = $silent_mode;
			$this->daily_clear_old_session = false;
			$this->daily_clear_old_dtimes = false;
			$this->daily_clear_old_sms = false;
			$this->last_date = null;
		}

		public function run($run_type=null) {
			$db = db::get_db();

			$this->refresh_status_of_interviews();

			$this->save_chatfile_and_clear();

			$this->clear_old_session();

			$this->clear_old_dtimes();

			$this->clear_old_sms();

			$this->send_sms_and_email();

			$db->close();

			$this->last_date = _date();
		}

		public function refresh_status_of_interviews()
		{
			$db = db::get_db();
			$db->begin();

			$first = true;

			$interview = new interviewModel;

			$statuses = array(ISTATUS_NONE, ISTATUS_PAYED, ISTATUS_OPENED, ISTATUS_PROGRESSING);

			$err = $interview->select("status IN (" . implode(",", $statuses) . ")");
			while($err == ERR_OK)
			{
				$refreshed = $interview->refresh_status();
				if ($first && $refreshed) {
					$this->print_log("refreshing status of interviews.");
					$first = false;
				}

				$err = $interview->fetch();
			}

			$db->commit();
		}

		public function clear_old_session() 
		{
			if ($this->last_date != _date() || !$this->daily_clear_old_session)
			{
				$db = db::get_db();
				$db->begin();
				
				$this->print_log("clear old session.");

				// remove sessions before 3 days
				$sql = "DELETE FROM l_session WHERE DATE_SUB(NOW(),INTERVAL 3 day ) > create_time";
				$db->execute($sql);

				// remove access log before 30 days
				$sql = "DELETE FROM l_access WHERE DATE_SUB(NOW(),INTERVAL 30 day ) > create_time";
				$db->execute($sql);

				$this->daily_clear_old_session = true;

				$db->commit();
			}
		}

		public function clear_old_dtimes()
		{
			if ($this->last_date != _date() || !$this->daily_clear_old_dtimes)
			{
				$db = db::get_db();
				$db->begin();
				
				$this->print_log("clear old dtimes.");

				// remove dtimes before 7 days
				$sql = "DELETE FROM t_dtime WHERE DATE_SUB(NOW(),INTERVAL 7 day ) > start_time";
				$db->execute($sql);

				$this->daily_clear_old_dtimes = true;

				$db->commit();
			}
		}

		public function save_chatfile_and_clear()
		{
			$db = db::get_db();
			$db->begin();

			$first = true;
			$interview = new interviewModel;
			$err = $interview->select("chat_file IS NULL AND status IN (" . ISTATUS_FINISHED . "," . ISTATUS_CANCELED . ")");
			while ($err == ERR_OK)
			{
				if ($first) {
					$this->print_log("save chat file and clear.");
					$first = false;
				}
				$interview->save_chatfile_and_clear();

				$err = $interview->fetch();
			}

			$db->commit();
		}

		public function clear_old_sms()
		{
			if ($this->last_date != _date() || !$this->daily_clear_old_sms)
			{
				$db = db::get_db();
				$db->begin();
				
				$this->print_log("clear old sms.");

				// remove sms before 2 day
				$sql = "DELETE FROM t_message WHERE DATE_SUB(NOW(),INTERVAL 1 day ) > create_time";
				$db->execute($sql);

				$this->daily_clear_old_sms = true;

				$db->commit();
			}
		}

		public function send_sms_and_email()
		{
			$db = db::get_db();
			$db->begin();

			$first = true;
			$message = new messageModel;
			$err = $message->select("send_time IS NULL", array("limit" => 10));

			while ($err == ERR_OK)
			{
				if ($first) {
					$this->print_log("send sms & email.");
					$first = false;
				}

				$sms = $message->content;
				//if ($message->sms_header)
				//$sms = $message->sms_header . $sms;

				_send_sms($message->mobile, $sms);

				if ($message->email)
				{
					$body = $message->content;
					if ($message->email_header)
						$body = $message->email_header . $body;
					_send_mail($message->email_from_name, $message->email, $message->email_to_name,
						$message->email_title, $body);
				}

				$message->send_time = _date_time();

				$err = $message->update("send_time");

				$err = $message->fetch();
			}	

			$db->commit();
		}

		private function print_log($msg)
		{
			if (!$this->silent_mode)
				print sprintf("%s %s %s\n", 
					_date(), _time(null, "H:i:s"), $msg);
		}

	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.                     *
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/