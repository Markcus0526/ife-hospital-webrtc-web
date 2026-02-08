<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	//-------------------------
	// 1. database management
	//-------------------------

	define('DEBUG_SQL',		false);

	// db object
	$g_db = null;

	class db {
		var $conn;
		var $trans;
		var $sql_result;

		// for debug
		var $query_count;
		var $err_count;

		public function __construct(){
			$this->conn = null;
			$this->trans = false;
			$this->query_count = 0;
			$this->query_time = 0;
			$this->err_count = 0;
		}

		static function get_db() {
			global $g_db;
			if ($g_db == null)
			{
				$g_db = new db;
			}
			
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$g_db->connect();
			}

			return $g_db;
		}

		// connect to database
		function connect()
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				if ($this->conn == null) {
					$this->conn = @mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

					if (mysqli_connect_errno())
					{
						_debug_log("Failed to connect to MySQL: " . mysqli_connect_error());
						die("Database Connection Failed");
					}
					
					$sql = "SET NAMES utf8";
					@mysqli_query($this->conn, $sql);

					//$sql = "SET GLOBAL time_zone = '" . TIME_ZONE . "'";
					//@mysqli_query($sql, $this->conn);

					$sql = "SET time_zone = '" . _time_zone(null, true) . "'";
					@mysqli_query($this->conn, $sql);

					$sql = "SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED ;";
					@mysqli_query($this->conn, $sql);

					//  iso-8601　'2013-08-25T17:00:00+00:00'の日付形式を入力する場合v5.6で错误となる現像を回避
					$sql = "SET @@sql_mode='no_engine_substitution';";
					@mysqli_query($this->conn, $sql);
				}
			}

			return TRUE;
		}

		function set_time_zone($time_zone)
		{
			$sql = "SET time_zone = '" . $time_zone . "'";
			@mysqli_query($this->conn, $sql);
		}

		// close the connection of database
		function close()
		{
		    mysqli_close($this->conn);
		    $this->conn = null;
		}	
		function reconnect()
		{
			$this->close();
			$this->connect();
		}	
		
		// execute SQL command (SELECT)
		public function query($sql)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				global $g_cache_key;

				if ($g_cache_key) {
					$this->sql_result = cacheHelper::get_cache($sql);
					if ($this->sql_result->cached)
						return ERR_OK;
				}

				if (DEBUG_MODE) {
					$start_time = $this->now();
				}

				$this->sql_result = mysqli_query ($this->conn, $sql);

				if (DEBUG_MODE) {
					$query_time = $this->now() - $start_time;
					$this->query_time += $query_time;
					if (DEBUG_SQL)
						_debug_log($query_time . " : " . $sql);
					$this->query_count++;
					if (!$this->sql_result)
						$this->err_count++;
				}

				if ($g_cache_key)
					cacheHelper::create_cache($sql, $this->sql_result);
				else if (IS_CREATEMOCKUP)
					mockupHelper::create_mockup($sql, $this->sql_result);

				return  $this->sql_result ? ERR_OK : ERR_SQL;
			}
			else { // IS_MOCKUP
				$this->sql_result = mockupHelper::get_mockup($sql);
				return ERR_OK;
			}
		}

		// execute SQL command (SELECT COUNT, MAX)
		public function scalar($sql)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				global $g_cache_key;

				if ($g_cache_key) {
					$this->sql_result = cacheHelper::get_cache($sql);
					if ($this->sql_result && $this->sql_result->cached) {
						return $this->sql_result->get(0, 0);
					}
				}

				if (DEBUG_MODE) {
					$start_time = $this->now();
				}

				$this->sql_result = mysqli_query ($this->conn, $sql);

				if (DEBUG_MODE) {
					$query_time = $this->now() - $start_time;
					$this->query_time += $query_time;
					if (DEBUG_SQL)
						_debug_log($query_time . " : " . $sql);

					$this->query_count++;
					if (!$this->sql_result)
						$this->err_count++;
				}
				
				if ($g_cache_key)
					cacheHelper::create_cache($sql, $this->sql_result);
				else if (IS_CREATEMOCKUP) 
					mockupHelper::create_mockup($sql, $this->sql_result);

				if (!$this->sql_result)
				{
					return null;
				}

				if (mysqli_num_rows($this->sql_result) != 1)
					return null;

				$result = $this->fetch_array($this->sql_result);
				if ($result)
					return $result[0];
				else
					return null;
			}
			else { // IS_MOCKUP
				$this->sql_result = mockupHelper::get_mockup($sql);
				return $this->sql_result->get(0, 0);
			}
		}

		// execute SQL command (INSERT, UPDATE, DELETE)
		public function execute($sql)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				if (DEBUG_MODE) {
					$start_time = $this->now();
				}

				$this->sql_result = mysqli_query($this->conn, $sql);

				if (DEBUG_MODE) {
					$query_time = $this->now() - $start_time;
					$this->query_time += $query_time;
					if (DEBUG_SQL)
						_debug_log($query_time . " : " . $sql);

					$this->query_count++;
					if (!$this->sql_result)
						$this->err_count++;
				}

				return $this->sql_result ? ERR_OK : ERR_SQL;
			}
			else { // IS_MOCKUP
				return ERR_OK;
			}
		}

		public function execute_batch($sql)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$sqls = preg_split('/;/', $sql);

				foreach($sqls as $sql) {
					if ($sql != "") {
						$this->sql_result = mysqli_query($this->conn, $sql);
						if (DEBUG_MODE) {
							$this->query_count++;
							if (!$this->sql_result)
								$this->err_count++;
						}
					}
				}

				return ERR_OK;
			}
			else { // IS_MOCKUP
				return ERR_OK;
			}
		}

		public function last_id() 
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				return mysqli_insert_id($this->conn);
			}
			else { // IS_MOCKUP
				return 1;
			}
		}

		function affected_rows()
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				return mysqli_affected_rows($this->conn);
			}
			else { // IS_MOCKUP
				return 1;
			}
		}

		function fetch_array($result)
		{
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				if (isset($result->cached))
					return $result->fetch_array();
				else 
					return mysqli_fetch_array($result);
			}
			else { // IS_MOCKUP
				return $result->fetch_array();
			}
		}

		// begin transaction
		function begin()
		{
			$err = $this->execute("begin");

			$this->trans = true;
		}

		// commit transaction
		function commit()
		{
			if ($this->trans) {
				$err = $this->execute("commit");

				$this->trans = false;
			}
		}

		// rollback transaction
		function rollback()
		{
			$err = $this->execute("rollback");

			$this->trans = false;
		}

		function is_exist_table($tablename) {
			if (IS_NOMOCKUP || IS_CREATEMOCKUP) {
				$sql = "SHOW TABLES WHERE tables_in_" . DB_NAME . "=" . _sql($tablename);
				$tbl = $this->scalar($sql);
				
				return ($tbl != "");
			}
		}

		function free_result($result) {
			if (!isset($result->cached))
				mysqli_free_result($result);
		}

		function next_result() {
			if (!isset($result->cached))
				return mysqli_next_result($this->conn);
		}

		function escape($txt) {
			return mysqli_escape_string($this->conn, $txt);
		}

		function now()
		{
			return intval(floor(microtime(true) * 1000));
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/