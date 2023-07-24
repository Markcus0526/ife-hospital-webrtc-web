<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	final class modelConfig {
		protected static $instance;

		public $_table_name;
		public $_pkeys;
		public $_fields;

		// options
		public $_auto_inc;
		public $_rand_hash;
		public $_rand_id;
		public $_dist_inc;
		public $_operate_time; // default true
		public $_operator_info;
		public $_del_flag; // default true

		function __construct($tname = null, $pkeys = null, $fields = null, $options=null) {
			$this->_table_name = $tname;
			$this->_pkeys = is_array($pkeys) ? $pkeys : array($pkeys);
			$this->_fields = is_array($fields) ? $fields : array($fields);

			if (is_array($options)) {
				// auto increment
				$this->_auto_inc = (isset($options["auto_inc"]) && $options["auto_inc"] == true) ? true : false;
				// random hash
				$this->_rand_hash = (isset($options["rand_hash"]) && $options["rand_hash"] == true) ? true : false;
				// random id 
				$this->_rand_id = (isset($options["rand_id"]) && $options["rand_id"] == true) ? true : false;
				// distribute increment(ex: odd and even)
				$this->_dist_inc = (isset($options["dist_inc"]) && $options["dist_inc"] == true) ? true : false;
				// operation time(create_time, update_time)
				$this->_operate_time = (isset($options["operate_time"]) && $options["operate_time"] == false) ? false : true;
				if ($this->_operate_time) {
					$this->_fields = array_merge($this->_fields, array("create_time", "update_time"));
				}
				// operator information(create_user_id, update_user_id)
				$this->_operator_info = (isset($options["operator_info"]) && $options["operator_info"] == true) ? true : false;
				if ($this->_operator_info) {
					$this->_fields = array_merge($this->_fields, array("create_user_id", "update_user_id"));
				}
				// use del_flag
				$this->_del_flag = (isset($options["del_flag"]) && $options["del_flag"] == false) ? false : true;
				if ($this->_del_flag) {
					$this->_fields = array_merge($this->_fields, array("del_flag"));
				}
			}
		}

		public static function init($model_name, 
			$tname = null, $pkeys = null, $fields = null, $options=null) {
			$c = new modelConfig($tname, $pkeys, $fields, $options);

			self::$instance[$model_name] = $c;
		}

		public static function config($model_name) 
	    {
	    	if (!isset(self::$instance)) {
	    		self::$instance = array();
	    	}

	    	if ($model_name == "model") 
	    	{
	    		self::$instance[$model_name] = new modelConfig;
	    	}

	        if (!isset(self::$instance[$model_name])) {
	        	die("not found config of " . $model_name);
	        }

	        return self::$instance[$model_name];
	    }
	}

	class model {
		private $_db;
		private $sql_result;

		private $_props;

		private $_config;

		private $_viewHelper;

		public $name_prefix;

		function __construct() {
			$this->_config = modelConfig::config(get_called_class());

			$this->_db = db::get_db();
			$this->_viewHelper = new viewHelper($this);

			$this->init_props();
		}

		function __clone() {
			$this->_db = db::get_db();
			$this->_viewHelper = new viewHelper($this);
		}

		private function init_props()
		{
			$this->_props = array();

			if ($this->_config->_table_name != null) {
				// single table mode
				foreach($this->_config->_pkeys as $f)
				{
					$this->_props[$f] = null;
				}

				foreach($this->_config->_fields as $f)
				{
					$this->_props[$f] = null;
				}

				if ($this->_config->_operate_time) {
					$this->_props["create_time"] = null;
					$this->_props["update_time"] = null;
				}

				if ($this->_config->_operator_info) {
					$this->_props["create_user_id"] = null;
					$this->_props["update_user_id"] = null;
				}

				if ($this->_config->_del_flag) {
					$this->_props["del_flag"] = null;
				}
			}
			else {
				// table join mode
			}
		}

		public function __get($prop) {
			if ($prop == "table")
				return $this->_config->_table_name;
			else if ($prop == "props")
				return $this->_props;
			else if ($prop == "db")
				return $this->_db;
			else
			{
				return isset($this->_props[$prop]) ? $this->_props[$prop] : null;
			}
		}

		public function props($prop_names) {
			$props = array();

			foreach ($prop_names as $prop_name) {
				if (isset($this->_props[$prop_name]))
					$props[$prop_name] = $this->_props[$prop_name];
			}

			return $props;

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

		public function validate_pkey()
		{
			foreach ($this->_config->_pkeys as $field_name)
			{
				if (_is_empty($this->_props[$field_name]))
						return false;
			}
			return true;
		}

		public function new_id($field_name) 
		{
			if ($this->_config->_rand_hash)
				return _rand_hash();
			else if ($this->_config->_rand_id)
				return _rand_id();
			else if ($this->_config->_dist_inc)
			{
				$max_id = $this->_db->scalar("SELECT MAX(" . $field_name . ") FROM " . $this->table);

				$mod = $max_id % DIST_INC_COUNT;
				$diff = DIST_INC_NO - $mod;
				if ($diff > 0)
					$diff = $diff - DIST_INC_COUNT;
				$cur_max_id = $max_id + $diff;

				$new_id = $cur_max_id + DIST_INC_COUNT;

				return $new_id;
			}
		}

		public function insert()
		{
			// single table mode
			if (!$this->_config->_auto_inc) {
				if ($this->_config->_rand_hash || $this->_config->_rand_id || $this->_config->_dist_inc) {
					foreach ($this->_config->_pkeys as $field_name)
					{
						if (_is_empty($this->_props[$field_name])) {
							$this->_props[$field_name] = $this->new_id($field_name);
						}
					}
				}
				else {
					if (!$this->validate_pkey())
						return ERR_INVALID_PKEY;
				}
			}
			
			if ($this->_config->_operate_time) {
				if ($this->_props["create_time"] == null) {
					$this->_props["create_time"] = "##NOW()";	
				}	
			}

			if ($this->_config->_operator_info) {
				if ($this->_props["create_user_id"] == null) {
					$this->_props["create_user_id"] = _my_id();	
				}	
			}
			
			if ($this->_config->_del_flag) {
				$this->_props["del_flag"] = 0;
			}

			$sql = "INSERT INTO " . $this->table . "(";

			if ($this->_config->_auto_inc) {
				$auto_inc = true;
				foreach ($this->_config->_pkeys as $field_name)
				{
					if (!_is_empty($this->_props[$field_name]))
						$auto_inc = false;
				}
			}
			else 
				$auto_inc = false;

			$fields = "";
			if (!$auto_inc) {
				foreach ($this->_config->_pkeys as $field_name)
				{
					if ($fields != "") $fields .= ",";
					$fields .= $field_name;
				}
			}
			foreach ($this->_config->_fields as $field_name)
			{
				if ($fields != "") $fields .= ",";
				$fields .= $field_name;
			}

			$sql .= $fields . ") VALUES(";

			$vals = "";
			if (!$auto_inc) {
				foreach ($this->_config->_pkeys as $field_name)
				{
					if ($vals != "") $vals .= ",";
					$vals .= _sql($this->_props[$field_name]);
				}
			}
			foreach ($this->_config->_fields as $field_name)
			{
				if ($vals != "") $vals .= ",";
				$vals .= _sql($this->_props[$field_name]);
			}

			$sql .= $vals . ");";

			$err = $this->_db->execute($sql);

			if ($err != ERR_OK) {
				if (mysqli_errno($this->_db->conn) == 1062)
				{
					//duplicate keys.
					if (!$this->_config->_auto_inc && $this->_config->_rand_hash) {
						// regenerate random hash
						if ($this->rand_hash_retry < 5) {
							$this->rand_hash_retry ++;
							return $this->insert();
						}
					}
				}

				if (LOG_MODE) {
					model::print_sql_error($sql);	
				}
			}

			if ($this->_config->_auto_inc) {
				$pkey = $this->_config->_pkeys[0];
				$this->$pkey = $this->last_id();
			}
			else if ($this->_config->_rand_hash) {
				$this->rand_hash_retry = 0;
			}

			return $err;
		}

		public function update($fields=null)
		{
			// single table mode
			if (!$this->validate_pkey())
				return ERR_INVALID_PKEY;

			if ($this->_config->_operate_time) {
				$this->_props["update_time"] = "##NOW()";
			}

			if ($this->_config->_operator_info) {
				$this->_props["update_user_id"] = _my_id();	
			}

			$sql = "UPDATE " . $this->table . " SET ";

			$sub = "";
			if ($fields == null)
				$fields = $this->_config->_fields;
			else if (is_string($fields))
				$fields = array($fields);
			foreach ($fields as $field_name)
			{
				if ($sub != "")
					$sub .= ",";
				$sub .= $field_name . "=" . _sql($this->_props[$field_name]) . " ";
			}

			$sql .=  $sub . " WHERE ";

			$where = "";
			foreach ($this->_config->_pkeys as $field_name)
			{
				if ($where != "")
					$where .= " AND ";
				$where .= $field_name . "=" . _sql($this->_props[$field_name]) . " ";
			}

			$sql .= $where;

			$err = $this->_db->execute($sql);

			if (LOG_MODE && $err != ERR_OK)
				model::print_sql_error($sql);

			return $err;
		}

		public function save() 
		{
			if (!$this->validate_pkey()) 
				return $this->insert();
			else
				return $this->update();
		}

		public function save_field($field_name)
		{
			if ($this->validate_pkey()) {
				if (in_array($field_name, $this->_config->_fields)) {
					$sql = "UPDATE " . $this->table . " 
						SET " . $field . "=" . _sql($this->_props[$field_name]);	

					$sql .=  " WHERE ";
					$where = "";
					foreach ($this->_config->_pkeys as $field_name)
					{
						if ($where != "")
							$where .= " AND ";
						$where .= $field_name . "=" . _sql($this->_props[$field_name]) . " ";
					}

					$sql .= $where;

					$err = $this->_db->execute($sql);

					if (LOG_MODE && $err != ERR_OK)
						model::print_sql_error($sql);

					return $err;					
				}	
			}
			else
				return ERR_INVALID_PKEY;
		}

		public static function remove_model($pkvals, $permanent=false)
		{
			$model = static::get_model($pkvals);
			if ($model != null) {
				return $model->remove($permanent);
			}

			return ERR_OK;
		}

		public function remove($permanent=false)
		{
			// single table mode
			if (!$this->validate_pkey())
				return ERR_INVALID_PKEY;

			if (!$this->_config->_del_flag) 
				$permanent = true;

			if (!$permanent) {
				$sql = "UPDATE " . $this->table . " SET ";
				$sql .= "del_flag = 1, ";
				$sql .= "update_time=now() WHERE ";
			}
			else {
				$sql = "DELETE FROM " . $this->table . " WHERE ";
			}

			$where = "";
			foreach ($this->_config->_pkeys as $field_name)
			{
				if ($where != "")
					$where .= " AND ";
				$where .= $field_name . "=" . _sql($this->_props[$field_name]) . " ";
			}

			$sql .= $where;

			$err = $this->_db->execute($sql);

			if (LOG_MODE && $err != ERR_OK)
				model::print_sql_error($sql);

			return $err;
		}

		public function remove_where($where, $permanent=false)
		{
			if (!$this->_config->_del_flag) 
				$permanent = true;

			// single table mode
			if (!$permanent) {
				$sql = "UPDATE " . $this->table . " SET del_flag = 1, update_time=now() WHERE " . $where;
			}
			else {
				$sql = "DELETE FROM " . $this->table . " WHERE " . $where;
			}

			$err = $this->_db->execute($sql);

			if (LOG_MODE && $err != ERR_OK)
				model::print_sql_error($sql);

			return $err;
		}

		public static function get_model($pkvals, $ignore_del_flag=false)
		{
			$model = new static;
			$err = $model->get($pkvals, $ignore_del_flag);
			if ($err == ERR_OK)
				return $model;

			return null;
		}

		public function get($pkvals, $ignore_del_flag=false)
		{
			if (!is_array($pkvals))
				$pkvals = array($pkvals);

			if (count($pkvals) != count($this->_config->_pkeys))
				return ERR_INVALID_PKEY;

			foreach($pkvals as $pkval)
			{
				if ($pkval === null)
					return ERR_INVALID_PKEY;
			}

			if (!$this->_config->_del_flag) 
				$ignore_del_flag = true;

			$where = "";
			if (!$ignore_del_flag)
				$where = "del_flag=0";

			$cnt = count($pkvals);
			for ($i = 0; $i < $cnt; $i ++)
			{
				if ($where != "")
					$where .= " AND ";
				$where .= $this->_config->_pkeys[$i] . "=" . _sql($pkvals[$i]) . " ";
			}

			$sql = "SELECT * FROM " . $this->table . " WHERE " . $where;

			$err = $this->_db->query($sql);

			if (LOG_MODE && $err != ERR_OK)
				model::print_sql_error($sql);

			if ($err != ERR_OK)
				return $err;

			$this->sql_result = $this->_db->sql_result;
			$row = $this->_db->fetch_array($this->sql_result);

			if (!$row)
				return ERR_NODATA;

			foreach ($this->_props as $field_name => $val)
			{
				if (is_string($field_name)) {
					$this->_props[$field_name] = $row[$field_name];
				}
			}

			return $err;
		}

		public static function counts_model($where="", $options=null, $ignore_del_flag=false)
		{
			$model = new static;
			return $model->counts($where, $options, $ignore_del_flag);
		}

		public function counts($where="", $options=null, $ignore_del_flag=false)
		{
			if (!$this->_config->_del_flag) 
				$ignore_del_flag = true;

			// single table mode
			if (!$ignore_del_flag) {
				if ($where != "")
					$where .= " AND ";
				$where .= "del_flag=0";
			}

			$sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE " . $where;

			return $this->_db->scalar($sql);
		}

		public function select($where="", $options=null, $ignore_del_flag=false)
		{
			if (!$this->_config->_del_flag) 
				$ignore_del_flag = true;
			
			// single table mode
			if (!$ignore_del_flag) {
				if ($where != "")
					$where = "(" . $where . ") AND ";
				$where .= "del_flag=0";
			}

			$sql = "SELECT * FROM " . $this->table;
			if ($where != "")
				$sql .= " WHERE " . $where;
			if ($options != null) {
				if (isset($options["group"]) && !_is_empty($options["group"]))
					$sql .= " GROUP BY " . $options["group"];
				if (isset($options["having"]) && !_is_empty($options["having"]))
					$sql .= " HAVING " . $options["having"];
				if (isset($options["order"]) && !_is_empty($options["order"]))
					$sql .= " ORDER BY " . $options["order"];
				if (isset($options["limit"]) && $options["limit"] > 0) {
					$sql .= " LIMIT " . _sql_number($options["limit"]);

					if (isset($options["offset"]) && $options["offset"] > 0)
						$sql .= " OFFSET " . _sql_number($options["offset"]);
				}
			}

			$err = $this->_db->query($sql);
			if (LOG_MODE && $err != ERR_OK)
				model::print_sql_error($sql);

			if ($err != ERR_OK)
				return $err;

			$this->sql_result = $this->_db->sql_result;
			$row = $this->_db->fetch_array($this->sql_result);

			if (!$row)
				return ERR_NODATA;

			foreach ($this->_props as $field_name => $val)
			{
				if (is_string($field_name)) {
					$this->_props[$field_name] = $row[$field_name];
				}
			}

			return $err;
		}

		public function fetch()
		{
			// single table mode
			$err = ERR_OK;

			$row = $this->_db->fetch_array($this->sql_result);

			if (!$row) {
				$this->_db->free_result($this->sql_result);
				return ERR_NODATA;
			}

			foreach ($this->_props as $field_name => $val)
			{
				if (is_string($field_name) && array_key_exists($field_name, $row)) {
					$this->_props[$field_name] = $row[$field_name];
				}
			}

			return $err;
		}

		public function query($sql, $options=null)
		{
			// table join mode
			if ($options != null) {
				if (isset($options["where"]) && !_is_empty($options["where"]))
					$sql .= " WHERE " . $options["where"];
				if (isset($options["group"]) && !_is_empty($options["group"]))
					$sql .= " GROUP BY " . $options["group"];
				if (isset($options["having"]) && !_is_empty($options["having"]))
					$sql .= " HAVING " . $options["having"];
				if (isset($options["order"]) && !_is_empty($options["order"]))
					$sql .= " ORDER BY " . $options["order"];
				if (isset($options["limit"]) && $options["limit"] > 0) {
					$sql .= " LIMIT " . _sql_number($options["limit"]);

					if (isset($options["offset"]) && $options["offset"] > 0)
						$sql .= " OFFSET " . _sql_number($options["offset"]);
				}
			}
			
			$err = $this->_db->query($sql);

			if (LOG_MODE && $err != ERR_OK)
				model::print_sql_error($sql);

			if ($err != ERR_OK)
				return $err;

			$this->sql_result = $this->_db->sql_result;
			$row = $this->_db->fetch_array($this->sql_result);

			if (!$row)
				return ERR_NODATA;

			foreach ($row as $field_name => $val)
			{
				if (is_string($field_name)) {
					$this->_props[$field_name] = $row[$field_name];
				}
			}

			return $err;
		}

		public function scalar($sql, $options=null)
		{
			// table join mode
			if ($options != null) {
				if (isset($options["where"]) && !_is_empty($options["where"]))
					$sql .= " WHERE " . $options["where"];
				if (isset($options["group"]) && !_is_empty($options["group"]))
					$sql .= " GROUP BY " . $options["group"];
				if (isset($options["having"]) && !_is_empty($options["having"]))
					$sql .= " HAVING " . $options["having"];
				if (isset($options["order"]) && !_is_empty($options["order"]))
					$sql .= " ORDER BY " . $options["order"];
				if (isset($options["limit"]) && $options["limit"] > 0) {
					$sql .= " LIMIT " . _sql_number($options["limit"]);

					if (isset($options["offset"]) && $options["offset"] > 0)
						$sql .= " OFFSET " . _sql_number($options["offset"]);
				}
			}
			return $this->_db->scalar($sql);
		}

		public function save_session($session_name)
		{
			_session($session_name, $this->_props);
		}

		public function load_session($session_name)
		{
			$this->_props = _session($session_name);
		}

		public function load($load_object, $ignores = array())
		{
			if (is_array($load_object))
				$load = (object) $load_object;
			else
				$load = $load_object;

			$_exist_prop = method_exists($load, "exist_prop");
			foreach ($this->_props as $field_name => $val)
			{
				if ($this->name_prefix)
					$l_field_name = $this->name_prefix . $field_name;
				else
					$l_field_name = $field_name;
				
				if (!in_array($field_name, $ignores)) {
					$exists = property_exists($load, $l_field_name) || 
						$_exist_prop && $load->exist_prop($l_field_name);
					if ($exists) {
						$this->$field_name = $load->$l_field_name;
						if (is_array($this->$field_name)) // checkbox
							$this->$field_name = _arr2bits($this->$field_name);
					}
					else {
						// for single checkbox
						$l_field_name .= "_@@@";
						$exists = property_exists($load, $l_field_name) || 
							$_exist_prop && $load->exist_prop($l_field_name);
						if ($exists) {
							$this->$field_name = _arr2bits($load->$l_field_name);
						}
					}
				}
			}
		}

		public function exist_prop($prop)
		{
			$keys = array_keys($this->_props);
			foreach($keys as $key)
			{
				if ($key == $prop)
					return true;
			}
			return false;
		}

		public function encode_prop($prop)
		{
			if ($this->exist_prop($prop)) {
				$this->$prop = _encode($this->$prop);
			}
		}

		public function decode_prop($prop)
		{
			if ($this->exist_prop($prop)) {
				$this->$prop = _decode($this->$prop);
			}
		}

		public function is_exist_table()
		{
			return $this->_db->is_exist_table($this->_config->_table_name);
		}

		public function last_id() 
		{
			return $this->_db->last_id();
		}

		static function print_sql_error($sql)
		{
			global $g_err_msg;
			$err_seq = date('YmdHis', time()) . sprintf("%04d", rand() * 10000.1);
			$g_err_msg = "DB错误が発生しました。错误コード：" . $err_seq;
			$db = db::get_db();
			$log = $g_err_msg . " SQL:$sql Error Detail:"  . mysqli_error($db->conn);

			if (DEBUG_MODE) {
				$g_err_msg = $log;	
			}
			_err_log($log);
		}
	};

	// initialize model
	function _model($model_name, 
			$tname = null, $pkeys = null, $fields = null, $options=null)
	{
		modelConfig::init($model_name, $tname, $pkeys, $fields, $options);
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/