<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
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
			$this->_navi_menu = "sysman";
			$this->_subnavi_menu = "sysman_setting";
			$sysconfig = new sysconfig;

			$this->mConfig = $sysconfig;
		}

		public function testdb_ajax() {
			$sysconfig = new sysconfig;

			$sysconfig->load($this);

			$this->response($this->json(array("err_code" => $sysconfig->connect())));
		}

		public function version_history() {
			$this->_navi_menu = "sysman_version_history";
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
				"mail_smtp_port"
			);
			$this->set_api_params($param_names);
			$params = $this->api_params;

			$sysconfig = new sysconfig;

			$sysconfig->load($this);

			$err = $sysconfig->save();

			$this->finish(null, $err);
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/