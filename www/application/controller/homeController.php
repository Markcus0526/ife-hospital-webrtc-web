<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class homeController extends controller {
		public function __construct(){
			parent::__construct();	

			$this->_navi_menu = "home";
		}

		public function check_priv($action, $utype)
		{
			switch($action) {
				default:
					parent::check_priv($action, UTYPE_LOGINUSER);
					break;
			}
		}

		public function index() {
			if (_my_type() == UTYPE_PATIENT)
				$this->forward(_url("interview/doctors"));
			else if (_my_type() == UTYPE_DOCTOR)
				$this->forward(_url("interview"));
			else if (_my_type() == UTYPE_INTERPRETER)
				$this->forward(_url("interview"));
			else if (_my_type() == UTYPE_ADMIN) {
				/*
				$reguser = new reguserModel;
				$err = $reguser->select("status=" . RSTATUS_NONE, array("order" => "create_time DESC"));
				if ($err == ERR_OK) {
					if ($reguser->user_type == UTYPE_INTERPRETER)
						$this->forward(_url("reguser/interpreters"));
				}
				*/
				if (_has_priv(CODE_PRIV_REG_USER, PRIV_REG_USER_D))
					$this->forward(_url("reguser/doctors"));
				else if (_has_priv(CODE_PRIV_REG_USER, PRIV_REG_USER_I))
					$this->forward(_url("reguser/interpreters"));
				else if (_has_priv(CODE_PRIV_INTERVIEWS))
					$this->forward(_url("interview"));
				else if (_has_priv(CODE_PRIV_CHISTORY))
					$this->forward(_url("chistory"));
				else if (_has_priv(CODE_PRIV_PATIENTS))
					$this->forward(_url("user/patients"));
				else if (_has_priv(CODE_PRIV_DOCTORS))
					$this->forward(_url("user/doctors"));
				else if (_has_priv(CODE_PRIV_INTERPRETERS))
					$this->forward(_url("user/interpreters"));
				else if (_has_priv(CODE_PRIV_HOSPITALS))
					$this->forward(_url("hospital"));
				else if (_has_priv(CODE_PRIV_STATS))
					$this->forward(_url("stats"));
				else if (_has_priv(CODE_PRIV_FEEDBACK))
					$this->forward(_url("feedback"));
				else
					$this->forward(_url("profile"));
			}
			else {
				$this->forward(_url("interview"));
			}
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/