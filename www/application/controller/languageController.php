<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class languageController extends controller {
		public function __construct() {
			parent::__construct();

			$this->_navi_menu = "master";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_SUPER);
					break;
			}
		}

		public function index($page = 0, $size = 50) {
			$this->_subnavi_menu = "master_language";
			$languages = array();
			$language = new languageModel;
			
			$this->search = new reqsession("master_language");

			if ($this->search->sort_field != null)
				$this->order = _sql_field($this->search->sort_field) . " " . _sql_order($this->search->sort_order);
			else 
				$this->order = "sort ASC";

			$this->counts = $language->counts("");

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$where = "";

			$err = $language->select($where,
				array("order" => $this->order,
					"limit" => $size,
					"offset" => $this->pagebar->page * $size));

			while ($err == ERR_OK)
			{
				$languages[] = clone $language;

				$err = $language->fetch();
			}

			$this->mLanguages = $languages;
			$this->mLanguage = new languageModel;
			$this->mPageLanguages = languageModel::get_all_code();
		}

		public function get_ajax()
		{
			$param_names = array("language_id");
			$this->check_required($param_names);
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$language_id = $params->language_id;
			$language = languageModel::get_model($language_id);

			if ($language == null)
				$this->check_error(ERR_NODATA);

			$this->finish(array("language" => $language->props), ERR_OK);
		}

		public function save_ajax()
		{
			$param_names = array("language_id", 
				"language_name",
				"language_code");
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$language_id = $params->language_id;
			if ($language_id == null) {
				$language = new languageModel;
				$language->sort = languageModel::get_last_sort_no() + 1;
			}
			else {
				$language = languageModel::get_model($language_id);

				if ($language == null)
					$this->check_error(ERR_NODATA);
			}

			if ($params->language_code != "")
			{
				$other = languageModel::get_model($params->language_code);
				if ($other && $other->language_id != $language_id)
				{
					$this->check_error(ERR_ALREADY_USING_LCODE);
				}
			}

			$language->load($params);

			$err = $language->save();

			if ($err == ERR_OK)
			{
				languageModel::upgrade_resource($language->language_code);
			}

			$this->finish(null, $err);
		}

		public function save_name_ajax()
		{
			$param_names = array("id", 
				"name_field",				// 字段名称
				"name_l");					// 多语言
			$this->check_required(array("id", "name_field"));
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$id = $params->id;
			$name_l = _json_encode($params->name_l);

			if ($params->name_field == "language_name") {
				$language = languageModel::get_model($id);

				if ($language == null)
					$this->check_error(ERR_NODATA);

				$language->language_name_l = $name_l;

				$err = $language->save();
			}

			$this->finish(null, $err);
		}

		public function move_to_ajax() {
			$param_names = array("language_id", 
				"direct");					// 移动方向
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$language_id = $params->language_id;
			$language = languageModel::get_model($language_id);

			if ($language == null)
				$this->check_error(ERR_NODATA);

			$err = $language->move_to($params->direct);

			$this->finish(null, $err);
		}

		public function delete_ajax() {
			$param_names = array("language_id");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$language_id = $params->language_id;

			$language = languageModel::get_model($language_id);

			if ($language == null)
				$this->check_error(ERR_NODATA);

			if ($language->language_code == DEFAULT_LANGUAGE)
				$this->check_error(ERR_NOPRIV);

			$err = $language->remove();

			$this->finish(null, $err);
		}

		public function resource($language_code, $page = 0, $size = 50) {
			$this->_subnavi_menu = "master_language";
			$language = languageModel::get_model($language_code);

			if ($language == null)
				$this->check_error(ERR_NODATA);

			if ($this->upgrade == "1") {
				$language->upgrade_resource($language_code);
				_goto("language/resource/" . $language_code);
			}

			_load_lang($language_code);

			global $g_string;
			$all_strings = $g_string[$language_code];

			$org_strings = array_keys($all_strings);

			$this->counts = count($org_strings);

			$this->pagebar = new pageHelper($this->counts, $page, $size);

			$strings = array();
			$start = $this->pagebar->start_no() - 1;
			$end = min($this->counts, $start + $size);
			for ($i = $start; $i < $end; $i ++)
			{
				$strings[$org_strings[$i]] = $all_strings[$org_strings[$i]];
			}

			$this->language_code = $language_code;
			$this->mStrings = $strings;
		}

		public function save_resource_ajax()
		{
			$param_names = array("language_code",
				"org_string",
				"interp_string");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$lang = $params->language_code;

			$language = languageModel::get_model($lang);

			if ($language == null)
				$this->check_error(ERR_NODATA);

			if ($lang == DEFAULT_LANGUAGE)
				$this->check_error(ERR_NOPRIV);

			_load_lang($lang);

			global $g_string;

			foreach ($params->org_string as $key => $org_string) {
				$interp_string = $params->interp_string[$key];

				$g_string[$lang][$org_string] = $interp_string;
			}

			$string_path = SITE_ROOT . "resource/lang/$lang.php";

			$fp = fopen($string_path, "w+");
			if ($fp)
			{
				$string = "<?php\n";
				$string .= '$g_string["' . $lang .'"]=array(' . "\n";
				foreach ($g_string[$lang] as $key => $value) {
					$key = preg_replace('/\n/', "\\n", $key);
					$key = preg_replace('/\"/', "\\\"", $key);
					$value = preg_replace('/\n/', "\\n", $value);
					$value = preg_replace('/\"/', "\\\"", $value);
					$string .= "	\"" . $key . "\" => \"" . $value . "\"," . "\n";
				}

				$string .= ");\n";
				
				fwrite($fp, $string);

				fclose($fp);
			}

			$this->finish(null, ERR_OK);

		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/