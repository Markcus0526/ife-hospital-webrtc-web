<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class forgetController extends controller {
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

			$this->reseted_password = -1;

			$ip = _ip();
			$country_id = _country_id_from_ip($ip);
			if ($country_id == "US")
				$this->contact_phone = _l("如无法找回，请致电") . "+1-949-888-6605";
			else
				$this->contact_phone = _l("如无法找回，请致电") . "+86-400-656-4366";

			$this->mUser = new userModel;

			if ($this->user_name != "" ||
				$this->mobile != "")
			{
				$this->mUser->load($this);
				
				$this->reseted_password = $this->mUser->send_reset_password($this->mobile, $this->user_name);
				$this->admin_mobiles = implode(",", userModel::get_nation_admin_mobiles_for_forget($this->mobile, $this->user_name));
			}

			return "forget/";
		}
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/