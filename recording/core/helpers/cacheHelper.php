<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	define("CACHE_PATH",	SITE_ROOT . "cache/");
	class cacheResult {
		private $current;
		private $count;
		private $datas;
		private $sql;
		private $select_id;
		private $var_name;
		private $must_write;
		public $cached;

		function __construct($sql, $sql_result = null) {
			$this->sql = $sql;
			$this->current = 0;
			$this->count = 0;
			$this->datas = array();
			$this->select_id = cacheHelper::select_id($sql);
			$this->var_name = cacheHelper::var_name($sql);

			if ($this->read()) {
				$this->cached = !$this->must_write;
			}
			else {
				$this->cached = false;
			}

			if ($sql_result != null) {
				@mysqli_data_seek($sql_result, 0);

				do {
					$arr = mysqli_fetch_array($sql_result);
					if ($arr != null)
						$this->datas[] = $arr;

				} while($arr);

				@mysqli_data_seek($sql_result, 0);

				if ($this->must_write)
					$this->write();
			}
		}

		public function read()
		{
			$var_name = $this->var_name;
			$path = CACHE_PATH . $this->select_id . ".php";
			$fp = @fopen($path, "r");
			if ($fp) {
				$datas = @fread($fp, filesize($path));
				@fclose($fp);
				if ($datas != null) {
					eval($datas);
				}

				$this->datas = ((!isset($$var_name) || $$var_name == null) ? array() : unserialize(base64_decode($$var_name)));
				$this->must_write = (!isset($$var_name) || $$var_name == null);

				$this->count = count($this->datas);
				return true;
			}
			else {
				$this->datas = array();
				$this->must_write = true;
				$this->count= 0;
				return false;
			}
		}

		public function write()
		{
			$var_name = $this->var_name;
			$path = CACHE_PATH . $this->select_id . ".php";
			$fp = fopen($path, "a+");
			@fputs($fp, '$' . $var_name . " = '");
			@fputs($fp, base64_encode(serialize($this->datas)));
			@fputs($fp, "';\n");
			@fclose($fp);
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

	class cacheHelper {
		function __construct() {
		}

		public static function start_cache($cache_key) {
			global $g_cache_key;
			$g_cache_key = $cache_key;
		}

		public static function end_cache() {
			global $g_cache_key;
			$g_cache_key = null;
		}

		public static function clear_cache($cache_key) {
			$files = scandir(CACHE_PATH);
			if (count($files) == 0)
				return;

			$now = time();
			foreach ($files as $file)
			{
				if (strpos($file, $cache_key . "_") === 0) {
					@unlink(CACHE_PATH . $file);
				}
			}
		}

		public static function create_cache($sql, $result) {
			$cache_result = new cacheResult($sql, $result);
			return $cache_result;
		}

		public static function get_cache($sql) {
			$cache_result = new cacheResult($sql);
			return $cache_result;
		}

		public static function cols($sql) {
			preg_match('/^select(.+)from(.+)/i', $sql, $parts);
			if (count($parts) == 3) {
				$cols = $parts[1];
			}

			return $cols;
		}

		public static function var_name($sql) {
			return "v" . md5($sql);
		}

		public static function select_id($sql) {
			global $g_cache_key;
			preg_match('/^(.+)where(.+)/i', $sql, $parts);
			if (count($parts) == 3) {
				$select = $parts[1];
			}
			else {
				$select = $sql;
			}

			return $g_cache_key . "_" . md5($select);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/