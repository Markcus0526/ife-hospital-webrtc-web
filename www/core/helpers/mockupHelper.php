<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	define("MOCKUP_PATH",	SITE_ROOT . "mockup/");
	class mockupResult {
		private $current;
		private $count;
		private $datas;
		private $sql;
		private $select_id;
		private $var_name;
		private $must_write;

		function __construct($sql, $sql_result = null) {
			$this->sql = $sql;
			$this->current = 0;
			$this->count = 0;
			$this->datas = array();
			$this->select_id = mockupHelper::selectId($sql);
			$this->var_name = mockupHelper::varName($sql);
		
			$this->read();
			if ($sql_result != null) {
				@mysqli_data_seek($sql_result, 0);

				do {
					$arr = mysqli_fetch_array($sql_result);
					if ($arr != null)
						$this->datas[] = $arr;

				} while($arr);

				@mysqli_data_seek($sql_result, 0);
			}

			if ($this->must_write)
				$this->write();
		}

		public function read()
		{
			$var_name = $this->var_name;
			$path = MOCKUP_PATH . $this->select_id . ".php";
			$fp = @fopen($path, "r");
			$datas = @fread($fp, filesize($path));
			@fclose($fp);
			if ($datas != null) {
				eval($datas);
			}

			$this->datas = ($$var_name == null ? array() : unserialize(base64_decode($$var_name)));
			$this->must_write = $$var_name == null;

			$this->count = count($this->datas);
		}

		public function write()
		{
			if (IS_CREATEMOCKUP) {
				$var_name = $this->var_name;
				$path = MOCKUP_PATH . $this->select_id . ".php";
				$fp = fopen($path, "a+");
				@fputs($fp, '$' . $var_name . " = '");
				@fputs($fp, base64_encode(serialize($this->datas)));
				@fputs($fp, "';\n");
				@fclose($fp);
			}
		}

		public function get($row, $col) 
		{
			if ($row >= $this->count)
				return null;
			$d = $this->datas[$row];

			return $d[$col];
		}

		public function fetch_array()
		{
			if ($this->current < $this->count) {
				return $this->datas[$this->current ++];
			}
			else {
				return null;
			}
		}

	}

	class mockupHelper {
		function __construct() {
		}

		public static function create_mockup($sql, $result) {
			$mockup_result = new mockupResult($sql, $result);
			return $mockup_result;
		}

		public static function get_mockup($sql) {
			$mockup_result = new mockupResult($sql);
			return $mockup_result;
		}

		public static function cols($sql) {
			preg_match('/^select(.+)from(.+)/i', $sql, $parts);
			if (count($parts) == 3) {
				$cols = $parts[1];
			}

			return $cols;
		}

		public static function varName($sql) {
			return "v" . md5($sql);
		}

		public static function selectId($sql) {
			preg_match('/^(.+)where(.+)/i', $sql, $parts);
			if (count($parts) == 3) {
				$select = $parts[1];
			}
			else {
				$select = $sql;
			}

			return md5($select);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/