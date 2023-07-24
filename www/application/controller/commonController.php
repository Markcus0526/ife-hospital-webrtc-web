<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class commonController extends controller {
		public function __construct(){
			parent::__construct();	
		}

		public function check_priv($action, $utype)
		{
			parent::check_priv($action, UTYPE_NONE);
		}

		public function booth($mode = "avartar")
		{
			$this->mode = $mode;

			return "popup/common_booth";
		}

		public function booth_ajax()
		{
			$param_names = array("png_data", "mode");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;

			$data = base64_decode($params->png_data);

			$tmppath = _tmp_path("png");
			$tmpfile = basename($tmppath);
			
			file_put_contents($tmppath, $data);

			_resize_userphoto($tmppath, "png", AVARTAR_SIZE, AVARTAR_SIZE);

			_erase_old(TMP_PATH);

			$this->finish(array("tmp_path" => "tmp/" . $tmpfile), ERR_OK);
		}

		public function upload($upload_id = null, $upload_type = null)
		{
			$this->upload_id = $upload_id;
			$this->upload_type = $upload_type;
			$this->addcss("js/dropzone/dropzone.css");
			return "popup/common_upload";
		}

		public function upload_ajax() {
			$this->start();

			$tmppath = _tmp_path();
			$tmpfile = basename($tmppath);

			if (($filename = _upload("file", $tmppath)) == null) {
				$this->finish(null, ERR_FAIL_UPLOAD, 400);
			} 

			_erase_old(TMP_PATH);
			$filesize = _mem_size(filesize($tmppath));
			$ext = _ext($filename);

			if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp" || $ext == "gif" || $ext == "tiff") {
				// create thumbnail
				$thumb_path = $tmppath . "_thumb.jpg";
				copy($tmppath, $thumb_path);
				_resize_image($thumb_path, $ext, "jpg", THUMB_SIZE, THUMB_SIZE, RESIZE_CONTAIN);

			}

			$this->finish(array("path" => "tmp/" . $tmpfile, "filename" => $filename, "filesize" => $filesize), ERR_OK);
		}

		public function view($param1, $param2 = null, $param3 = null, $param4 = null, $param5 = null)
		{
			if ($param1 != null && $param2 != null && $param3 != null) {
				$param1 .= "/";
				if ($param1 == 'tmp/') {
					$path = $param1 . $param2;
					_goto($path);
					exit;
				}
				if ($param1 == ATTACH_URL) {
					$path = $param1 . $param2 . "/" . $param3 . "/" . $param4 . "/" . $param5;
					_goto($path);
					exit;
				}
			}
			down($param1, $param2, $param3, $param4, $param5);
		}

		public function zoomview($param1, $param2 = null, $param3 = null, $param4 = null, $param5 = null)
		{
			$img = "";
			if ($param1 != null && $param2 != null && $param3 != null) {
				$param1 .= "/";
				if ($param1 == 'tmp/') {
					$img = $param1 . $param2;
				}
				else if ($param1 == ATTACH_URL) {
					$img = $param1 . $param2 . "/" . $param3 . "/" . $param4 . "/" . $param5;
				}
			}

			$this->mImage = $img;
			return "popup/common_zoomview";
		}

		public function down($param1, $param2 = null, $param3 = null, $param4 = null, $param5 = null)
		{
			$mode = "attachment";
			if ($param1 != null && $param2 != null && $param3 != null) {
				$param1 .= "/";
				if ($param1 == 'tmp/') {
					$path = TMP_PATH . $param2;
					$filename = $param3;
				}
				else if ($param1 == ATTACH_URL) {
					$ext = _ext($param5);
					
					if ($ext != "jpg" && $ext != "png" && $ext != "pdf") {
						$path = $param1 . $param2 . "/" . $param3 . "/" . $param4 . "/" . $param5;
						_goto($path);
						exit;
					}

					// fore download
					$path = DATA_PATH . $param1 . $param2 . "/" . $param3 . "/" . $param4;
				}
				else 
					exit;
				$sz = filesize($path);
			}
			else {
				exit;
			}
			$fp = fopen($path, "rb");
			$mime = _mime_type($filename);

			if (ISIE7 || ISIE8)
	            $filename = 'filename=' . urlencode(_basename($filename));
	        if (ISSafari)
	            $filename = 'filename="' . _basename($filename) . '"';
	        else 
	            $filename = 'filename="' . _basename($filename) . '"; filename*=UTF-8\'\'' . urlencode(_basename($filename));

			if ($fp) {
				ob_end_clean();
				header('Content-Disposition: ' . $mode  . '; '. $filename);
				header('Content-Type: ' . $mime);
				header("Content-Length: " . $sz);
				fpassthru($fp);

				fclose($fp);
				if ($del)
					@unlink($path);
			}
		
			exit;
		}

		public function now_ajax()
		{
			$this->finish(array("now" => _date_time()), ERR_OK);
		}

		public function is_exist_ajax()
		{
			$param_names = array("mobile");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			
			$exist = userModel::is_exist_by_mobile($params->mobile);

			$this->finish(array("is_exist" => $exist), ERR_OK);
		}

		public function send_passkey_ajax()
		{
			$param_names = array("phone_num");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;

			$err = logPasskeyModel::send_passkey($params->phone_num);		

			$this->finish(null,$err);
		}

		public function check_passkey_ajax()
		{
			$param_names = array("phone_num", "passkey", "login");
			$this->set_api_params($param_names);
			$this->check_required(array("phone_num", "passkey"));
			$params = $this->api_params;

			$passkey = logPasskeyModel::get_passkey($params->phone_num);		

			$err = ERR_OK;
			if ($params->login == 1) {
				$checked = false;
				$user = userModel::get_model($params->phone_num);
				if ($user == null)
					$err = ERR_NOTFOUND_USER;
				else if ($user->lock_flag == LOCK_DISABLE)
				{
					$err = ERR_DISABLED_LOGIN;
				}
				else if ($user->lock_flag == LOCK_ON &&
					$user->is_lock_time_before_hours()) {
					$err = ERR_LOCKED;
				}
				else if ($passkey != $params->passkey) {
					$fails = logFailModel::inc_fail(FAIL_PASSKEY, $user->user_id);
					if ($fails >= LOGIN_FAIL_LOCK) {
						$err = $user->update_lock_flag(FAIL_PASSKEY);
					}
				}
				else {
					$checked = true;
				}

				if ($err == ERR_DISABLED_LOGIN)
				{
					global $g_err_msg;
					$g_err_msg = _err_msg(ERR_DISABLED_LOGIN);
					$admin_mobiles = implode(",", userModel::get_nation_admin_mobiles($user->country_id));

					if ($admin_mobiles)
						$g_err_msg .= _l("（电话:%s）", $admin_mobiles);
				}
			}
			else
				$checked = $passkey == $params->passkey;

			$this->finish(array("valid" => $checked), $err);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/