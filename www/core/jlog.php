<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class jlog
	{
		private $_props;
		private $search;
		private $fp; // log file pointer
		private $offset;
		private $offset_start;
		private $offset_end;

		private $_viewHelper;

		public function __construct()
		{
			$this->init_props(array(
				'date',
				'time',
				'user_id',
				'email',
				'prison_id',
				'user_type',
				'log_type', 
				'ip', 
				'detail'));

			$this->_viewHelper = new viewHelper($this);
		}

		function __clone() {
			$this->_viewHelper = new viewHelper($this);
		}

		public function init_props($arr)
		{
			foreach($arr as $item) {
				$this->$item = null;
			}
		}

		public function __get($prop) {
			if ($prop == "props")
				return $this->_props;
			else
			{
				return $this->_props[$prop];
			}
		}

		public function __set($prop, $val) {
			if ($prop == "props") {
				if (is_array($val))
					$this->_props = $val;
			}
			else {
				$this->_props[$prop] = $val;
			}
		}

		public function __call($method, $params) {
			if (method_exists($this->_viewHelper, $method)) {
				call_user_func_array(array($this->_viewHelper, $method), $params);
			}
		}

		public function load($load_object)
		{
			foreach ($this->_props as $field_name => $val)
			{
				if ($load_object->exist_prop($field_name)) {
					if (is_array($load_object->$field_name)) {
						$this->$field_name = 0;
						foreach($load_object->$field_name as $v)
							$this->$field_name |= $v;
					}
					else {
						$this->$field_name = $load_object->$field_name;
					}
				}
			}
		}

		private function open_log()
		{
			if ($this->fp)
				@fclose($this->fp);

			if ($this->search != null && $this->search->search_startdate != null) {
				$file = LOG_PATH . _date($this->ssearch->search_startdate, "Ym") . ".log";
			}
			else {
				$file = LOG_PATH . _date(null, "Ym") . ".log";
			}

			$this->fp = @fopen($file, "r+");

			return $this->fp != null;
		}

		public function counts($search)
		{
			$err = $this->select($search);

			$c = 0;
			while ($err == ERR_OK)
			{
				$c ++;
				$err = $this->fetch();
			}

			return $c;
		}

		public function select($search, $options=null)
		{
			$this->offset_start = null;
			$this->offset_end = null;

			if ($options != null) {
				if ($options["offset"] !== null)
					$this->offset_start = $options["offset"];
				if ($options["limit"] !== null)
					$this->offset_end = $this->offset_start + $options["limit"] - 1;
			}

			$this->offset = -1;
			$this->search = $search;
			if ($this->open_log()) {
				return $this->fetch();
			}
			else {
				return ERR_NODATA;
			}
		}

		public function fetch()
		{
			if ($this->fp && !@feof($this->fp)) {
				$row = fgets($this->fp, 1024);
				$fields = preg_split("/\t/", $row, 3);
				if ($fields && count($fields) >= 2) {
					$p = preg_split("/ /", $fields[0]);
					$this->date = $p[0];
					$this->time = $p[1];
					$this->user_id = $p[2];
					$this->prison_id = $p[3]; 
					$this->user_type = $p[4]; 
					$this->log_type = $p[5]; 
					$this->ip = $p[6];

					$this->email = $p[7];

					$this->detail = $fields[1];

					if ($this->search->search_prison_id != null && $this->search->search_prison_id != $this->prison_id ||
						$this->search->search_user_type != null && $this->search->search_user_type != $this->user_type ||
						$this->search->search_email != null && $this->search->search_email != $this->email ||
						$this->search->search_startdate != null && !($this->search->search_startdate <= $this->date) ||
						$this->search->search_enddate != null && !($this->search->search_enddate >= $this->date) ||
						$this->search->search_starttime != null && !($this->search->search_starttime . ":00" <= $this->time) ||
						$this->search->search_endtime != null && !($this->search->search_endtime . ":00" >= $this->time) ||
						$this->search->search_string != null && !strstr($this->detail, $this->search->search_string))
						return $this->fetch();

					$this->offset = $this->offset + 1;
					if ($this->offset_start != null && $this->offset < $this->offset_start ||
						$this->offset_end != null && $this->offset > $this->offset_end)
						return $this->fetch();
					
					return ERR_OK;
				}
				else {
					return $this->fetch();
				}
			}
			else {
				return ERR_NODATA;
			}
		}

		public static function write($log_type, $msg, $convert_break=true) 
		{
			if (LOG_MODE) {
				if ($convert_break)
					$msg = str_replace("\n", '\\n', $msg);
				
				$log = sprintf("%s %s %d %d %d %s %s %s\t%s", 
					_date(null), _time(null, "H:i:s"), 
					_my_id(), _my_type(), $log_type, _ip(),
					_code_label(CODE_UTYPE, _my_type()), _code_label(CODE_LOGTYPE, $log_type), 
					$msg);

				$file = LOG_PATH . _date(null, "Ym") . ".log";
				$fp = @fopen($file, "a+");
				if($fp != null) {
					@fputs($fp, $log . "\n");
					@fclose($fp);
				}
			}
		}
	};