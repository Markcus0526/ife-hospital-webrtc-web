<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class module {
		public $_props;

		public function __construct(){
		}

		public function __get($prop) {
			if ($prop == "props") {
				return $this->_props;
			}
			else {
				return $this->_props[$prop];
			}
		}

		public function __set($prop, $val) {
			$this->_props[$prop] = $val;
		}

		public function __call($method, $params) {
			global $cur_controller;

			if (method_exists($cur_controller, $method)) {
				call_user_func_array(array($cur_controller, $method), $params);
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

		static public function show()
		{
			$count = func_num_args();
			$params = array();

			if ($count == 0) {
				$action = "action";
			}
			else {
				for ($i = 0; $i < $count; $i ++) {
					if ($i == 0)
						$action = func_get_arg($i);
					else
						$params[] = func_get_arg($i);
				}
			}

			$module = new static;
			$module_name = str_replace("Module", "", get_class($module));
			$module->_view = call_user_func_array(array($module, $action), $params);
			if ($module->_view == null) {
				if ($action == "action")
					$module->_view = _template("module/" . $module_name . ".php");
				else 
					$module->_view = _template("module/" . $module_name . "_" . $action . ".php");
			}

			$module->include_view();
		}

		public function include_view()
		{
			if (file_exists($this->_view)) {
				// export models to view
				foreach ($this->_props as $var_name => $var_value) {
					if (preg_match('/^m[A-Z]/', $var_name)) {
						$GLOBALS[$var_name] = $var_value;
						global $$var_name;
					}
				}

				include($this->_view);

				$this->addmodview($this->_view);
			}
		}

		static public function shortcode($path)
		{
			$params = preg_split("/\//", $path);

			if (count($params) >= 2) {
				try {
					$module_name = $params[0] . "Module";
					$params = array_slice($params, 1);

					$module = new $module_name;
					call_user_func_array(array($module, "show"), $params);
				} catch (Exception $e) {
					// ignore module
					print "[mod]" . $path . "[/mod]";
				}
			}
			else {
				print "[mod]" . $path . "[/mod]";
			}
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/