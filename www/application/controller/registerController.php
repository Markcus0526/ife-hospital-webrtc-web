<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class registerController extends controller {
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
			
			$this->err_login = ERR_OK;

			$this->mReguser = new reguserModel;

			$this->mReguser->contract = _fread(SITE_ROOT.'/resource/txt/contract_register.'._lang().'.txt');

			return "register/";
		}

		// insert new user
		public function save_ajax() {
			$param_names = array("user_name", "user_type", "password", 
				"sex", "mobile", "email", "passkey",
				"home_address", "introduction", "languages", "diseases",
				"d_title", "d_depart", "hospitals", "d_fee",
				"i_age", "diplomas", "passports");
			$this->set_api_params($param_names);
			$params = $this->api_params;

			$this->start();

			$reguser = new reguserModel;
			
			if (reguserModel::is_exist_by_mobile($params->mobile))
				$this->check_error(ERR_ALREADY_USING_MOBILE);

			/*$passkey = logPasskeyModel::get_passkey($params->mobile);
			if ($params->passkey != $passkey)
				$this->check_error(ERR_INVALID_PASSKEY);*/

			$reguser->load($params);

			if (_is_empty($reguser->user_type)) 
				$this->check_error(ERR_NOPRIV);

			$reguser->user_type = $reguser->user_type & (UTYPE_PATIENT | UTYPE_DOCTOR | UTYPE_INTERPRETER);

			if ($reguser->user_type == UTYPE_DOCTOR || $reguser->user_type == UTYPE_INTERPRETER) {
				if ($params->exist_prop("diplomas")) {
					$reguser->save_attaches("diplomas");
				}
			}
			if ($params->exist_prop("passports")) {
				$reguser->save_attaches("passports");
			}

			$reguser->country_id = countryModel::tel_num_to_country_id($reguser->mobile);
			if ($reguser->country_id == null)
				$reguser->country_id = 1; // 中国
			
			if (!_is_empty($params->password)) {
				$reguser->password = _password($params->password);
			}

			// update languages
			if (is_array($params->languages)) {
				$reguser->languages = implode(",", $params->languages);
			}
			// update diseases
			if ($reguser->user_type == UTYPE_DOCTOR) {
				if (is_array($params->diseases)) {
					$reguser->diseases = implode(",", $params->diseases);
				}
				if (is_array($params->hospitals)) {
					$reguser->hospitals = implode(",", $params->hospitals);
				}
			}
			if ($reguser->user_type == UTYPE_PATIENT) {
				$user = $reguser->activate();
				if ($user == null) {
					$this->check_error(ERR_SQL);
				}
			}

			$reguser->status = RSTATUS_NONE;

			$this->check_error($err = $reguser->save());
				
			$this->finish(array("reguser" => $reguser->reguser_id), $err);

		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/