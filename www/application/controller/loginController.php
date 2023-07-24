<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class loginController extends controller {
		public $err_login;

		public function __construct(){
			parent::__construct();	
		}

		public function check_priv($action, $utype)
		{			
			parent::check_priv($action, UTYPE_NONE);
		}

		public function index() {
			patchModel::check_patch();

			if (AUTO_LANGUAGE_FROM_IP) {
				$ip = _ip();
				$country_id = _country_id_from_ip($ip);

				$lang = "zh-CN";
				if ($country_id != "CN") {
					$lang = "en-US";
				}
				_lang($lang);
				_load_lang($lang);
			}

			$this->err_login = ERR_OK;

			if ($this->login_mode == null)
				$this->login_mode = 1; // 1: password, 2: passkey

			$this->mUser = new userModel;
			if ($this->mobile != "") {
				$this->mUser->load($this);
				if ($this->passkey != "") {
					$this->mUser->password = "";
					$this->mUser->passkey = $this->passkey;
				}

				$this->err_login = $this->mUser->login($this->auto_login);
				if ($this->err_login == ERR_OK) {
					if (!($this->mUser->user_type & UTYPE_LOGINUSER))
					{
						_session();
						$this->mUser->mobile = "";
						$this->mUser->password = "";
						$this->mUser->passkey = "";
						return "login/";
					}

					$uri = _session("request_uri");
					_session("request_uri", "");
					if ($uri == "") {
						$this->forward(_url("home"));
					}
					else {
						_abs_goto($uri);
					}
				}
				else {
					if ($this->err_login == ERR_UNREGISTERED_MOBILE) {
						$this->mUser->mobile = "";
						$this->mUser->password = "";
						$this->mUser->passkey = "";
					}
					if ($this->err_login == ERR_LOCKED ||
						$this->err_login == ERR_DISABLED_LOGIN) {
						$this->mUser->password = "";
						$this->mUser->passkey = "";
					}
					if ($this->err_login == ERR_INVALID_PASSWORD ||
						$this->err_login == ERR_LOCK_PASSWORD)
						$this->mUser->password = "";
					if ($this->err_login == ERR_INVALID_PASSKEY ||
						$this->err_login == ERR_LOCK_PASSKEY)
						$this->mUser->passkey = "";
				}
			}

			return "login/";
		}

		public function logout() {
			$me = _user();
			if ($me != null)
				$me->logout();
			else
				_session();

			$this->forward("home");
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/