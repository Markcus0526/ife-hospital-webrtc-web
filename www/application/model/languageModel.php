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
		"languageModel",			// model name
		"m_language",
		"language_id",
		array(
			"language_name",		// 语言名称
			"language_name_l",		// 语言名称（跨国）
			"language_code",		// 语言码
			"sort"),
		array("auto_inc" => true, 
			"operator_info" => true));

	class languageModel extends model // 语言信息模型
	{
		public static function get_model($pkvals, $ignore_del_flag=false)
		{
			$model = new static;
			$err = $model->get($pkvals, $ignore_del_flag);
			if ($err == ERR_OK) 
				return $model;

			if (is_string($pkvals)) {
				$err = $model->select("language_code = " . _sql($pkvals));
				if ($err == ERR_OK)
					return $model;
			}

			return null;
		}

		public static function get_all()
		{
			$languages = array();
			$language = new static;

			$err = $language->select("", array("order" => "sort ASC"));
            while ($err == ERR_OK)
            {
            	$languages[] = $language->props;

            	$err = $language->fetch();
            }

            return $languages;
		}

		public static function get_all_code()
		{
			$languages = array();
			$language = new static;

			$err = $language->select("language_code IS NOT NULL", array("order" => "sort ASC"));
            while ($err == ERR_OK)
            {
            	$languages[] = $language->props(array(
            		"language_code",
            		"language_name",
            		"language_id"
            	));

            	$err = $language->fetch();
            }
            
            return $languages;
		}

		public static function get_language_name($language_id)
		{
			if ($language_id == null)
				return "";
			
			global $g_languages;
			if ($g_languages == null)
				$g_languages = array();

			if (isset($g_languages[$language_id]))
				return  _l_model($g_languages[$language_id], "language_name");

			$language = static::get_model($language_id);
			if ($language == null)
				return "";
			else {
				$g_languages[$language_id] = $language->props;
				return _l_model($language, "language_name");
			}
		}

		public static function get_language_names($language_ids)
		{
			$names = array();
			foreach ($language_ids as $language_id) {
				$names[] = static::get_language_name($language_id);
			}

			return implode("/", $names);
		}

		public function get_sort_no()
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_language
				WHERE del_flag = 0 AND 
					sort <= " . _sql($this->sort);

			$no = $db->scalar($sql) - 1;
			if ($no < 0)
				$no = 0;
			return $no;
		}

		public static function get_last_sort_no()
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_language
				WHERE del_flag = 0";
			
			$no = $db->scalar($sql) - 1;
			if ($no < 0)
				$no = 0;
			return $no;
		}

		public function move_to($direct)
		{
			$org_sort = $this->sort;
			$sort_no = $this->get_sort_no();

			if ($direct == 1 || $direct == 2) {
				$last_sort_no = static::get_last_sort_no();
			}

			switch($direct) {
				case -2: // top
					$new_sort_no = 0;
					break;
				case -1: // up
					$new_sort_no = $sort_no - 1;
					if ($new_sort_no < 0)
						$new_sort_no = 0;
					break;
				case 1: // down
					$new_sort_no = $sort_no + 1;
					if ($new_sort_no > $last_sort_no)
						$new_sort_no = $last_sort_no;
					break;
				case 2: // bottom
					$new_sort_no = $last_sort_no;
					break;
			}

			if ($new_sort_no == $org_sort)
				return ERR_OK;

			$this->sort = $new_sort_no;

			$err = $this->update();
			if ($err != ERR_OK)
				return $err;

			// refresh sort
			$language = new static;
			$where = " language_id!=" . _sql($this->language_id);

			$err = $language->select($where, array("order" => "sort ASC"));	

			$sort_no = 0;

			while($err == ERR_OK)
			{
				if ($sort_no == $new_sort_no) // target
					$sort_no++; // next

				$language->sort = $sort_no;
				$err = $language->update();
				if ($err != ERR_OK)
					return $err;

				$err = $language->fetch();

				$sort_no ++;
			}

			return ERR_OK;
		}

		public static function upgrade_resource($lang)
		{
			if ($lang != DEFAULT_LANGUAGE) {
				$cmd = "php lang.php " . $lang;
				chdir(SITE_ROOT);
				$log = @exec($cmd);
			}
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/