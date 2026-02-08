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
		"dtemplateModel",			// model name
		"m_dtemplate",
		"dtemplate_id",
		array(
			"disease_id",			// 病种ID
			"dtemplate_name",		// 模板名称
			"dtemplate_name_l",		// 模板名称(多语言)
			"sort",					// 
			"template_file"),		// 模板文件(多语言)
		array("auto_inc" => true, 
			"operator_info" => true));

	class dtemplateModel extends model // 病种模板信息模型
	{
		public function get_sort_no()
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_dtemplate
				WHERE del_flag = 0 AND 
					disease_id = " . _sql($this->disease_id) . " AND
					sort <= " . _sql($this->sort);

			$no = $db->scalar($sql) - 1;
			if ($no < 0)
				$no = 0;
			return $no;
		}

		public static function get_last_sort_no($disease_id)
		{
			$db = db::get_db();

			$sql = "SELECT COUNT(*) FROM m_dtemplate
				WHERE del_flag = 0 AND 
					disease_id = " . _sql($disease_id);
			
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
				$last_sort_no = static::get_last_sort_no($this->disease_id);
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
			$dtemplate = new static;
			$where = " dtemplate_id!=" . _sql($this->dtemplate_id) . "
				AND disease_id=" . _sql($this->disease_id);

			$err = $dtemplate->select($where, array("order" => "sort ASC"));	

			$sort_no = 0;

			while($err == ERR_OK)
			{
				if ($sort_no == $new_sort_no) // target
					$sort_no++; // next

				$dtemplate->sort = $sort_no;
				$err = $dtemplate->update();
				if ($err != ERR_OK)
					return $err;

				$err = $dtemplate->fetch();

				$sort_no ++;
			}

			return ERR_OK;
		}

		public function save_attaches($lang) 
		{
			$template_file_l = $this->template_file_l;
			$attaches = $template_file_l[$lang];
			$attaches = preg_split("/;/", $attaches);
			$new_attaches = array();

			foreach($attaches as $attach)
			{
				$pf = preg_split("/:/", $attach);
				$path = $pf[0]; $file_name = _safe_filename($pf[1]); $ext = _ext($file_name);
				if (substr($path, 0, 3) == "tmp") {
					$dir = ATTACH_URL . date('Y/m') . "/";

					$path = substr($path, 4);
					
					$attach_url = $dir . "dt_" . $this->dtemplate_id . "_" . $path;
					$real_path = DATA_PATH . $attach_url;
					@unlink($real_path);
					@_mkdir(DATA_PATH . $dir);
					@rename(TMP_PATH . $path, $real_path);

					if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp" || $ext == "gif" || $ext == "tiff") {
						// create thumbnail
						$thumb_path = $real_path . "_thumb.jpg";
						copy($real_path, $thumb_path);
						_resize_image($thumb_path, $ext, "jpg", THUMB_SIZE, THUMB_SIZE, RESIZE_CONTAIN);

					}

					array_push($new_attaches, $attach_url . ":" . $file_name . ":" . $pf[2]);
				}
				else if ($path != "") {
					array_push($new_attaches, $attach);
				}
			}

			$template_file_l[$lang] = implode(";", $new_attaches);

			$this->template_file_l = $template_file_l;

			return ERR_OK;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/