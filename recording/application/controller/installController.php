<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class installController extends controller {
		public function __construct(){
			parent::__construct();	
		}

		public function check_priv($action, $utype)
		{
			parent::check_priv($action, UTYPE_NONE);
		}

		public function index() {
			if (!defined('DB_HOSTNAME')) {
				_session();

				$sysconfig = new sysconfig;

				$sysconfig->step = 0;

				$sysconfig->check_envir();

				$this->mConfig = $sysconfig;

				return "none/install_index";
			}
			else 
				return "none/install_removeconfig";
		}

		public function testdb_ajax() {
			if (!defined('DB_HOSTNAME')) {
				$sysconfig = new sysconfig;

				$sysconfig->load($this);

				$this->response($this->json(array("err_code" => $sysconfig->connect())));
			}
			else 
				$this->response($this->json(array("err_code" => ERR_ALREADYINSTALLED)));
		}

		public function testldap_ajax() {
			if (!defined('DB_HOSTNAME')) {
				$sysconfig = new sysconfig;

				$sysconfig->load($this);

				$this->response($this->json(array("err_code" => $sysconfig->connect_ldap())));
			}
			else 
				$this->response($this->json(array("err_code" => ERR_ALREADYINSTALLED)));
		}

		public function start_ajax() {
			global $_SERVER;
			if (!defined('DB_HOSTNAME')) {
				$sysconfig = new sysconfig;

				$sysconfig->load($this);
				
				$err = $sysconfig->connect($this->step == 0);
				if ($err != ERR_OK)
					$this->response($this->json(array("err_code" => $err)));

				switch($this->step) {
					case 0:
						$err = ERR_OK;
						break;
					case 1:
						$err = ERR_OK;
						break;
					case 2:
						$err = ERR_OK;
						break;
					case 3:
						//_install_batch();
						foreach (patchModel::$patches as $version => $p) {
						}
						$sysconfig->version = $version;
						$err = $sysconfig->save();
						break;
				}

				$this->response($this->json(array("err_code" => $err)));
			}
			else 
				$this->response($this->json(array("err_code" => ERR_ALREADYINSTALLED)));
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/