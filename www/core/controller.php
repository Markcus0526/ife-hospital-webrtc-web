<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class controller {
		public $_layout, $_default_layout;
		public $_view, $_modview;
		public $_js, $_css;
		private $_request;
		public $_navi_menu;
		public $_subnavi_menu;
		public $_controller_id;
		public $_action_type;
		public $_params;
		public $_download_mode;

		public function __construct(){
			$this->inputs();
			$this->_js = array();
			$this->_css = array();
			$this->_modview = array();
			$this->_download_mode = false;
			$this->_default_layout = "main";
			$this->_controller_id = str_replace("Controller", "", get_class($this));

			if ($this->_controller_id != "install" && !defined('DB_HOSTNAME')) {
				$this->forward("install");
			}

			// auto_login
			if (_my_id() == null && defined('DB_HOSTNAME')) {
				$user = new userModel;
				$user->login(true);
			}
		}
		
		public function get_referer(){
			return $_SERVER['HTTP_REFERER'];
		}
		
		public function response($data,$status = 200){
			$this->_code = ($status)?$status:200;
			$this->set_headers();
			echo $data;
			exit;
		}
		
		private function get_status_message(){
			$status = array(
						100 => 'Continue',  
						101 => 'Switching Protocols',  
						200 => 'OK',
						201 => 'Created',  
						202 => 'Accepted',  
						203 => 'Non-Authoritative Information',  
						204 => 'No Content',  
						205 => 'Reset Content',  
						206 => 'Partial Content',  
						300 => 'Multiple Choices',  
						301 => 'Moved Permanently',  
						302 => 'Found',  
						303 => 'See Other',  
						304 => 'Not Modified',  
						305 => 'Use Proxy',  
						306 => '(Unused)',  
						307 => 'Temporary Redirect',  
						400 => 'Bad Request',  
						401 => 'Unauthorized',  
						402 => 'Payment Required',  
						403 => 'Forbidden',  
						404 => 'Not Found',  
						405 => 'Method Not Allowed',  
						406 => 'Not Acceptable',  
						407 => 'Proxy Authentication Required',  
						408 => 'Request Timeout',  
						409 => 'Conflict',  
						410 => 'Gone',  
						411 => 'Length Required',  
						412 => 'Precondition Failed',  
						413 => 'Request Entity Too Large',  
						414 => 'Request-URI Too Long',  
						415 => 'Unsupported Media Type',  
						416 => 'Requested Range Not Satisfiable',  
						417 => 'Expectation Failed',  
						500 => 'Internal Server Error',  
						501 => 'Not Implemented',  
						502 => 'Bad Gateway',  
						503 => 'Service Unavailable',  
						504 => 'Gateway Timeout',  
						505 => 'HTTP Version Not Supported');
			return ($status[$this->_code])?$status[$this->_code]:$status[500];
		}
		
		public function get_request_method(){
			return $_SERVER['REQUEST_METHOD'];
		}
		
		private function inputs(){
			switch($this->get_request_method()){
				case "POST":	
					$this->_request = array_merge($this->clean_inputs($_GET), $this->clean_inputs($_POST));
					break;
				case "GET":
				case "DELETE":
					$this->_request = $this->clean_inputs($_GET);
					break;
				case "PUT":
					parse_str(file_get_contents("php://input"),$this->_request);
					$this->_request = $this->clean_inputs($this->_request);
					break;
				default:
					$this->response('',406);
					break;
			}
		}		
		
		private function clean_inputs($data){
			$clean_input = array();
			if(is_array($data)){
				foreach($data as $k => $v){
					$clean_input[$k] = $this->clean_inputs($v);
				}
			}else{
				/*
				if(get_magic_quotes_gpc()){
					$data = trim(stripslashes($data));
				}
				$data = strip_tags($data);
				$clean_input = trim($data);
				*/
				$clean_input = $data;
			}
			return $clean_input;
		}		
		
		private function set_headers(){
			if ($this->_code != 200) {
				header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
			}
			header("Content-Type:".$this->_content_type);
		}

		public function json($data){
			if(!is_array($data))
				$data = array($data);
			return _json_encode($data);
		}

		public function start()
		{
			$db = db::get_db();
			$db->begin();
		}

		public function commit()
		{
			$db = db::get_db();
			$db->commit();
		}

		public function rollback()
		{
			$db = db::get_db();
			$db->rollback();
		}

		public function finish($data, $err, $status=200)
		{
			global $g_err_msg;

			$db = db::get_db();
			if ($err == ERR_OK)
				$db->commit();
			else{
				ob_clean();
				header('Content-Disposition: inline');
				$db->rollback();
			}

			if ($this->_download_mode) {
				if ($err != ERR_OK)
				{
					if ($g_err_msg == null)
						$g_err_msg = _err_msg($err);
?>
<!doctype html>
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
    </head>
    <body>
    	<?php print $g_err_msg; ?>
    </body>
</html>   
<?php
				}
				exit;
			}
			else {
				if ($err == ERR_OK)
					$ret = array("err_code" => $err, "err_msg" => "");
				else {
					if ($g_err_msg == null)
						$g_err_msg = _err_msg($err);
					$ret = array("err_code" => $err, "err_msg" => $g_err_msg);
				}
				if ($data != null) {
					$data = is_array($data) ? $data : array($data);
					$ret = array_merge($ret , $data);
				}
				$this->response($this->json($ret), $status);
			}
		}

		public function check_error($err, $detail=null)
		{
			if ($err != ERR_OK) {
				if ($this->_action_type == ACTIONTYPE_AJAXJSON) {
					$this->finish($detail, $err);	
				}
				else {
					$this->show_error($err);
				}
			}
		}

		public function check_required($params)
		{
			global $g_err_msg;

			$err = ERR_OK;
			$params = is_array($params) ? $params : array($params);
			foreach($params as $param)
			{
				if ($this->$param === null || $this->$param === '') {
					$g_err_msg = _l("参数不正确");
					$err = ERR_INVALID_REQUIRED;
				}
			}

			$this->check_error($err);
		}

		public function __get($prop) {
			if ($prop == "request") {
				return $this->_request;
			}
			else {
				return isset($this->_request[$prop]) ? $this->_request[$prop] : null;
			}
		}

		public function __set($prop, $val) {
			$this->_request[$prop] = $val;
		}

		public function __call($method, $params) {

		}

		public function exist_prop($prop)
		{
			$keys = array_keys($this->_request);
			foreach($keys as $key)
			{
				if ($key == $prop)
					return true;
			}
			return false;
		}

		public function process($_controller, $_action, $_params){
			if (defined("API_MODE")) {
				$this->process_api($_controller, $_action, $_params);
			}
			else {
				$this->_params = $_params;

				$this->_action_type = ACTIONTYPE_HTML;
				if (strstr($_action, "_ajax"))
					$this->_action_type = ACTIONTYPE_AJAXJSON;
				if (strstr($_action, "_refresh")) {
					$this->_action_type = ACTIONTYPE_REFRESH;
					$_action = strstr($_action, "_refresh", true);
				}

				if((int)method_exists($this,$_action) > 0) 
				{
					$this->check_priv($_action, UTYPE_NONE);

					$ret = @call_user_func_array(array($this, $_action), $_params);

					$ret = preg_split("/\//", $ret, 2);

					$vw = "";
					if (count($ret) <= 1) {
						$this->_layout = _template("layout/" . $this->_default_layout . ".php");
						if (count($ret) == 1) 
							$vw = $ret[0];
					}
					else {
						$this->_layout = _template("layout/" . $ret[0] . ".php");
						if (count($ret) == 2) 
							$vw = $ret[1];
						else {
							$vw = $ret[1] . "/" . $ret[2];
						}
					}

					if ($this->is_refresh())
						$this->_layout = _template("layout/none.php");

					if ($vw == "")  {
						$prefix = "view/" . $_controller . "/" . $_controller . "_" . $_action;
						$this->_view = _template($prefix . ".php");
						$this->_viewjs = _template($prefix . ".js");
					}
					else {
						$this->_view = _template("view/" . $vw . ".php");
						$this->_viewjs = _template("view/" . $vw . ".js");
					}

					if (file_exists($this->_layout)) {
						require_once($this->_layout);
					}
					else
						$this->show_error(ERR_NOTFOUND_PAGE);
				}
				else
					$this->show_error(ERR_NOTFOUND_PAGE);
			}
		}

		private function show_error($err_code, $title = "错误発生")
		{
			if ($err_code == ERR_OK)
				return;
			
			$this->err_title = $title;
			$this->err_msg = _err_msg($err_code);
			switch ($err_code) {
				case ERR_NODATA:
				case ERR_NOTFOUND_PAGE:
					$this->_code = 404;
					break;
				case ERR_NOPRIV:
					$this->_code = 403;
					break;
				case ERR_ALREADYLOGIN:
				case ERR_FAILLOGIN:
				case ERR_NOT_LOGINED:
					$this->_code = 401;
					break;
				default:
					$this->_code = 400;
					break;
			}
			$this->set_headers();
			require_once(_template("layout/error.php"));
			exit;
		}

		public function forward($url) {
			_goto($url);
		}

		public function set_active($menu) {
			print $menu == $this->_navi_menu ? "active" : "";
		}

		public function set_arrow_open($menu) {
			print $menu == $this->_navi_menu ? "open" : "";
		}

		public function set_sub_active($submenu) {
			print $submenu == $this->_subnavi_menu ? "active" : "";
		}

		public function out_json($result, $err) {
			$ret = array("err_code" => $err, "err_msg" => $g_err_msg);
			if ($err === ERR_OK) {
				if ($result != null) {
					$result = is_array($result) ? $result : array($result);
					$ret = array_merge($ret , $result);
				}
			}

			print $this->json($ret);
			exit;
		}

		public function check_priv($_action, $utype)
		{
			if(_in_blacklist()) {
				$this->check_error(ERR_BLACKIP);
			}

			if ($utype == UTYPE_NONE)
				return;

			$cur_utype = _my_type();
			if ($cur_utype == null)
			{ 
				if ($this->_action_type == ACTIONTYPE_AJAXJSON) {
					$this->check_error(ERR_NOT_LOGINED);
				}
				else if ($this->_action_type == ACTIONTYPE_HTML) {
					global $_SERVER;
					_session("request_uri", $_SERVER["REQUEST_URI"]);
					$this->forward("login");
				}
				else { // ACTIONTYPE_REFRESH
					print "";
					exit;
				}

			}
			if ($cur_utype & UTYPE_SUPER ||
				$utype & UTYPE_SUPER && $cur_utype & UTYPE_SUPER ||
				$utype & UTYPE_ADMIN && $cur_utype & UTYPE_ADMIN ||
				$utype & UTYPE_PATIENT && $cur_utype & UTYPE_PATIENT ||
				$utype & UTYPE_DOCTOR && $cur_utype & UTYPE_DOCTOR ||
				$utype & UTYPE_INTERPRETER && $cur_utype & UTYPE_INTERPRETER)
			{
				return;
			}
			
			if ($this->_action_type == ACTIONTYPE_AJAXJSON) {
				$this->check_error(ERR_NOPRIV);
			}
			else if ($this->_action_type == ACTIONTYPE_HTML) {
				$this->forward("login");
			}
			else { // ACTIONTYPE_REFRESH, ACTIONTYPE_HTML
				$this->show_error(ERR_NOPRIV);
			}
		}

		public function addjs($jsfile) {
			$this->_js[] = $jsfile;
		}

		public function addcss($cssfile) {
			$this->_css[] = $cssfile;
		}

		public function addmodview($modview) {
			$this->_modview[] = $modview;
		}

		function include_view()
		{
			// export models to view
			foreach ($this->_request as $var_name => $var_value) {
				if (preg_match('/^m[A-Z]/', $var_name)) {
					$GLOBALS[$var_name] = $var_value;
					global $$var_name;
				}
			}

			include($this->_view);
		}

		function include_viewjs()
		{
			if ($this->_viewjs != null) {

				if (file_exists($this->_viewjs)) {
					$fp = fopen(SITE_ROOT . "/" . $this->_viewjs, "r");
					fpassthru($fp);
				}
				else if (file_exists($this->_viewjs . ".php"))
				{
					// export models to view
					foreach ($this->_request as $var_name => $var_value) {
						if (preg_match('/^m[A-Z]/', $var_name)) {
							global $$var_name;
						}
					}
					include_once($this->_viewjs . ".php");
				}
			}
		}

		function include_js()
		{
			foreach( $this->_js as $js) {
			?>
				<script src="<?php p($js); ?>"></script>
			<?php
			}
		}

		function include_css()
		{ 
			foreach( $this->_css as $css) {
			?>
				<link rel="stylesheet" type="text/css" href="<?php p($css); ?>"/>
			<?php
			}
		}

		function download_mode($mode = null)
		{
			if ($mode == null)
				return $this->_download_mode;
			else 
				$this->_download_mode = $mode;
		}

		// API related
		protected $api_name = "";
		protected $api_url = "", $apitest_url = "";
		protected $apis = null;
		protected $api_methods = null;
		protected $api_params = null, $api_param_names = null;

		public function process_api($_controller, $_action, $_params){
			$api_suffix = "_ajax";

			if ($_action == "index")
			{
				if (defined("API_TEST")) {
					$this->api_name = $_controller;
					$this->apitest_url = _abs_url("apitest/" . $_controller . "/");
					$this->api_url = _abs_url("api/" . $_controller . "/");
					$methods = get_this_class_methods(get_class($this));

					$this->api_methods = array();
					$exp = '/' . $api_suffix . '$/';
					foreach($methods as $method)
					{
						if (preg_match($exp, $method)) {
							$this->api_methods[] = preg_replace($exp, '', $method);
						}
					}

					$this->show_test_view("api_methods");
				}
			}
			else {
				$this->apitest_url = _abs_url("apitest/" . $_controller . "/" . $_action);
				$this->api_url = _abs_url("api/" . $_controller . "/" . $_action);
				$this->_params = $_params;
				if ((int)method_exists($this, $_action . $api_suffix) > 0) 
				{
					$this->api_params = new model;
					$request_body = file_get_contents('php://input');
					$data = json_decode($request_body);
					if ($data != null) {
						foreach($data as $key => $value)
						{
							$this->$key = $value;
						}
					}

					if (defined("API_TEST"))
						$this->_action_type = ACTIONTYPE_HTML;
					else {
						$this->_action_type = ACTIONTYPE_AJAXJSON;
						if ($this->token != null) {
							_load_session_from_token($this->token);

							$db = db::get_db();
							$db->set_time_zone(_time_zone());
						}
						$this->check_priv($_action . $api_suffix, UTYPE_NONE);
					}

					@call_user_func_array(array($this, $_action . $api_suffix), $_params);
					// exit response
				}
			}

			$this->show_error(ERR_NOTFOUND_PAGE);
		}

		public function set_api_params($params)
		{
			$this->api_param_names = $params;
			foreach($params as $param)
			{
				if ($this->exist_prop($param))
					$this->api_params->$param = $this->$param;
			}
			
			if (defined("API_TEST"))
				$this->show_test_view("api_test");
		}

		private function show_test_view($view) {
			$this->_layout = _template("layout/api.php");
			$this->_view = _template("view/" . $view . ".php");
			$this->_viewjs = _template("view/" . $view . ".js");

			if (file_exists($this->_layout)) {
				require_once($this->_layout);
				exit;
			}
			else
				$this->show_error(ERR_NOTFOUND_PAGE);
		}

		public function from_url($from_id)
		{
			if ($from_id != "") {
				$from = substr($from_id, 0, 1);
				$id = substr($from_id, 1);
				switch ($from) {
					case "i":
						return "interview/";
						break;
				}
			}
			return null;
		}

		public function is_refresh()
		{
			return $this->_action_type == ACTIONTYPE_REFRESH;
		}
	}

	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/