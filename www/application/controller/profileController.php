<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class profileController extends controller {
		public function __construct(){
			parent::__construct();	

			$this->_navi_menu = "profile";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				case "edit":
					parent::check_priv($action, UTYPE_PATIENT);
					break;
				default:
					parent::check_priv($action, UTYPE_LOGINUSER);
					break;
			}
		}

		public function index() {
			$this->_subnavi_menu = "profile_main";
			$me = _user();
			if ($me == null)
				$this->check_error(ERR_NODATA);

			$my_type = _my_type();
			$me->country_name = countryModel::get_country_name($me->country_id);
			if ($my_type == UTYPE_DOCTOR) {
				$me->d_title_l = stringModel::get_string_l($me->d_title);
				$me->d_depart_l = stringModel::get_string_l($me->d_depart);
				$me->hospitals = uhospitalModel::get_hospitals($me->user_id);
				$me->diseases = udiseaseModel::get_diseases($me->user_id, false);
				$me->introduction_l = stringModel::get_string_l($me->introduction);
			}
			else if ($my_type == UTYPE_INTERPRETER) {
				$me->introduction_l = stringModel::get_string_l($me->introduction);
			}
			$me->languages = ulangModel::get_languages($me->user_id, false);

			$this->mUser = $me;
		}

		public function edit() {
			$this->_subnavi_menu = "profile_main";
			$me = _user();
			if ($me == null)
				$this->check_error(ERR_NODATA);

			$my_type = _my_type();
			if ($my_type == UTYPE_DOCTOR) {
				$me->hospitals = uhospitalModel::get_hospitals($me->user_id, true);
				$me->diseases = udiseaseModel::get_diseases($me->user_id, true);
			}
			$me->languages = ulangModel::get_languages($me->user_id, true);

			$this->mUser = $me;
		}

		public function save_ajax() {
			$param_names = array("user_name", 
				"sex", "other_tel", "avartar", "email", "home_address",
				"introduction", "languages", "diseases",
				"d_title", "d_depart", "hospitals",
				"i_age", "diplomas", "passports");
			$this->set_api_params($param_names);
			$params = $this->api_params;
			$this->start();

			$me = _user();
			$my_type = _my_type();
			if ($me == null)
				$this->check_error(ERR_NODATA);

			$me->load($params);

			if ($my_type == UTYPE_DOCTOR || $my_type == UTYPE_INTERPRETER) {
				if ($params->exist_prop("diplomas")) {
					$me->save_attaches("diplomas");
				}
			}

			$this->check_error($err = $me->save());

			// update hospitals
			if (!_is_empty($params->hospitals)) {
				uhospitalModel::save_hospitals($me->user_id, $params->hospitals);
			}
			// update languages
			if (is_array($params->languages)) {
				ulangModel::save_languages($me->user_id, $params->languages);
			}
			// update diseases
			if ($my_type == UTYPE_DOCTOR) {
				if (is_array($params->diseases)) {
					udiseaseModel::save_diseases($me->user_id, $params->diseases);
				}
			}

			// change avartar
			if (substr($params->avartar, 0, 4) == 'tmp/') {
				$tmp_path = SITE_ROOT . $params->avartar;
				if (_resize_image($tmp_path, '', 'jpg', AVARTAR_SIZE, AVARTAR_SIZE, RESIZE_CROP)) {
					$avartar_path = AVARTAR_PATH . _my_id() . ".png";
					if (rename($tmp_path, $avartar_path)) {
						_renew_avartar_cache_id();
					}	
				}
			}

			// update my_name
			_my_name($me->user_name);

			_opr_log("用户更改基本信息(" . join(',', $me->dirties) . ")成功");
		
			$this->finish(null, $err);
		}

		public function password() {
			$this->_subnavi_menu = "profile_password";
			$me = _user();
			if ($me == null)
				$this->check_error(ERR_NODATA);

			$this->mUser = $me;
		}

		public function password_ajax() {
			$param_names = array("old_password", "new_password");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$me = _user();
			if ($me == null)
				$this->check_error(ERR_NODATA);

			if (_password($params->old_password) == $me->password) {
				$me->password = _password($params->new_password);

				$this->check_error($err = $me->save());
			}
			else {
				$this->check_error(ERR_INVALID_OLDPWD);
			}
			_opr_log("用户更改密码成功");
										
			$this->finish(null, $err);
		}

		public function mobile() {
			$this->_subnavi_menu = "profile_mobile";
			$me = _user();
			if ($me == null)
				$this->check_error(ERR_NODATA);

			$this->mUser = $me;
		}

		public function mobile_ajax() {
			$param_names = array("mobile_passkey", "new_mobile", "new_mobile_passkey");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$me = _user();
			if ($me == null)
				$this->check_error(ERR_NODATA);

			if (userModel::is_exist_by_mobile($params->new_mobile, $me->user_id)) {
				$this->check_error(ERR_ALREADY_USING_MOBILE);
			}

			$old_mobile = $me->mobile;
			$me->mobile = $params->new_mobile;
			
			$me->country_id = countryModel::tel_num_to_country_id($me->mobile);
			if ($me->country_id == null)
				$me->country_id = 1; // 中国

			$err = $me->save();

			_opr_log("用户更改手机成功。旧手机: ".$old_mobile." 新手机:" . $me->mobile);
										
			$this->finish(null, $err);
		}

		public function feedback() {
			$this->_subnavi_menu = "profile_feedback";
			$this->mFeedback = new feedbackModel;
		}

		public function feedback_ajax() {
			$param_names = array("comment");
			$this->set_api_params($param_names);
			$this->check_required($param_names);
			$params = $this->api_params;
			$this->start();

			$mine = _user();

			$feedback = new feedbackModel;
			$feedback->user_id = _my_id();
			$feedback->user_type = _my_type();
			$feedback->mobile = $mine->mobile;
			$feedback->email = $mine->email;
			$feedback->comment = $params->comment;
			$feedback->status = FSTATUS_UNREAD;
			$err = $feedback->save();
										
			$this->finish(null, $err);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/