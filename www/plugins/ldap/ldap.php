<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	class ldap {
		private $ds;
		private $ldap_root;

		public function __construct($server = LDAP_SERVER, $port = LDAP_PORT, $userid = LDAP_USERID, $password = LDAP_PASSWORD, $root = LDAP_ROOT){
			$this->ldap_root = $root;

			// connect
			$this->ds = ldap_connect($server, $port);
			if (!$this->ds)
				return;

			ldap_set_option($this->ds, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($this->ds, LDAP_OPT_REFERRALS,0);

			// bind
			if (!@ldap_bind($this->ds, $userid, $password)) {
				$this->close();
			}
		}

		function is_connected()
		{
			return $this->ds != null;
		}

		// close
		function close()
		{
			if ($this->ds)
				ldap_close($this->ds);
			$this->ds = null;
		}

		// find user (return array("name", "duty", "mail", "login=0/1"))
		function check_user($userid, $pwd)
		{
			if ($this->ds == null)
				return null;

			// search user id
			$sr=ldap_search($this->ds, $this->ldap_root, "samaccountname=" . $userid);
			if (!$sr)
				return null;

			$info = ldap_first_entry($this->ds, $sr);
			if (!$info)
				return null;
			
			$attrs = ldap_get_attributes($this->ds, $info);
			if (!$attrs)
				return null;

			$ret = array();
			$ret["name"] = $attrs["cn"][0];
			$ret["duty"] = $attrs["description"][0];
			$ret["mail"] = $attrs["userPrincipalName"][0];
			$ret["dn"] = $attrs["distinguishedName"][0];
			$ret["login"] = 0;

			$user_dn = $attrs["distinguishedName"][0];

			// check password
			if (@ldap_bind($this->ds, $user_dn, $pwd))
				$ret["login"] = 1;
			
			return $ret;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/