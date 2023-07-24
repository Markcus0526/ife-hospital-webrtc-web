<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class sysmanController extends controller {
		public function __construct(){
			parent::__construct();	

			$this->_navi_menu = "sysman";
		}

		public function check_priv($action, $utype)
		{
			parent::check_priv($action, UTYPE_SUPER);
		}

		public function setting() {
			$this->_subnavi_menu = "sysman_setting";
			$sysconfig = new sysconfig;
			$this->mConfig = $sysconfig;

			$settings = settingsModel::get_config();
			$this->mSettings = $settings;

			include_once 'plugins/payment/chinapay/sdk/SDKConfig.php';
			$this->mChinapayConfig = com\unionpay\acp\sdk\SDKConfig::getSDKConfig();
			$this->mPaypal = new paypal;
		}

		public function testdb_ajax() {
			$sysconfig = new sysconfig;

			$sysconfig->load($this);

			$this->response($this->json(array("err_code" => $sysconfig->connect())));
		}

		public function version_history() {
			$this->_subnavi_menu = "sysman_version_history";
			$patch = new patchModel;

			$patched = array();
			$err = $patch->select('', array('order' => 'patch_id DESC'));
			while($err == ERR_OK)
			{
				$new = clone $patch;
				$patched[] = $new;

				$err = $patch->fetch();
			}

			$this->mPatched = $patched;
		}

		public function save_setting_ajax() {
			$param_names = array(
				/* db related */
				"db_hostname",
				"db_user",
				"db_password",
				"db_name",
				"db_port",

				/* e-mail related */
				"mail_enable",
				"mail_from",
				"mail_fromname",
				"mail_smtp_auth",
				"mail_smtp_use_ssl",
				"mail_smtp_server",
				"mail_smtp_user",
				"mail_smtp_password",
				"mail_smtp_port",

				/* main */
				"recording_api",
				"chinapay_merchantid",
				"chinapay_signcert_pwd",
				"paypal_email_address",
				"paypal_identity_token",
				"paypal_cert_id",
				"paypal_ewp_private_key_pwd",
				"paypal_sandbox_client_id",
				"paypal_sandbox_client_secret",
				"paypal_client_id",
				"paypal_client_secret",
				"exrate_api_key",
				
				"interview_pay_limit",
				"interview_alarm_start",
				"interview_offline_limit",
				"interview_reoffline_limit",
				"interview_alarm_offline_limit",
				"interview_no_interp_alarm_limit",
				"interview_patient_reservable_time_after_now",
				"interview_doctor_changable_time_after_now",
				"interview_doctor_settable_time_after_now",
				"save_record_limit",
			);
			$this->set_api_params($param_names);
			$params = $this->api_params;

			$me = _user();

			$sysconfig = new sysconfig;

			$sysconfig->load($params);

			$err = $sysconfig->save();

			if ($err == ERR_OK) {
				$settings = settingsModel::get_config();
				$settings->load($params);

				$err = $settings->save();
			}

			if ($err == ERR_OK) {
				_opr_log("系统设置更改成功");
			}

			$this->finish(null, $err);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/