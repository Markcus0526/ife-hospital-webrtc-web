<?php
	/************************* Copyright Info ***************************
	*	Project Name:		MARKCUS World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Markcus										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	_model(
		"patchModel",			// model name
		"t_patch_record",
		"patch_id",
		array("version", "description"),
		array("auto_inc" => true));

	class patchModel extends model 
	{
		var $db_version;
		static public $patches = array(
			"1.0" => array("func" => "patch1_0", "description" => "最初版本"),
			"1.1" => array("func" => "patch1_1", "description" => "分析Record记录")
		);

		private $sysconfig;

		public function __construct()
		{
			parent::__construct();

			$this->sysconfig = new sysconfig;
		}

		static public function check_patch() {
			$patch = new patchModel;
			$patch->check_self();
			if ($patch->last_version() != $patch->version)
				_goto("patch");
		}

		public function patch_info() {
			$this->check_self();
			$patched = true;
			$must_patches = array();
			foreach (patchModel::$patches as $version => $p) {
				if (!$patched)
				{
					$p["version"] = $version;
					$must_patches[$version] = $p;
				}
				if ($version === $this->version)
					$patched = false;
			}

			return $must_patches;
		}

		public function patch() {
			$this->check_self();
			$patched = true;
			$err = ERR_OK;
			foreach (patchModel::$patches as $version => $p) {
				if (!$patched)
				{
					$func = $p["func"];
					$err = $this->$func();
					$this->did_patch($version);
					if ($err != ERR_OK)
						return $err;
				}
				if ($version === $this->version)
					$patched = false;
			}

			return $err;
		}

		public function last_version() {
			foreach (patchModel::$patches as $version => $p) {
			}
			return $version;
		}

		public function check_self() {
			if (!$this->is_exist_table()) {
				// create table;
				$sql = "CREATE TABLE t_patch_record (
					`patch_id`  int NOT NULL AUTO_INCREMENT ,
					`version`  varchar(10) NOT NULL ,
					`description`  varchar(255) NOT NULL ,
					`create_time`  datetime NOT NULL ,
					`update_time`  datetime NULL ,
					`del_flag`  numeric(1,0) NOT NULL ,
					PRIMARY KEY (`patch_id`)
					) CHARSET=utf8;";

				$this->db->execute($sql);

				$this->did_patch("1.0");

				$this->version = "1.0";
			}
			else {
				$err = $this->select("", array("order" => "patch_id DESC", "limit" => 1));
				if ($err != ERR_OK) {
					$this->version = "1.0";
				}
			}
		}

		public function did_patch($version)
		{
			$p = patchModel::$patches[$version];

			$this->sysconfig->version = $version;
			$this->sysconfig->save();

			$this->patch_id = null;
			$this->version = $version;
			$this->description = $p["description"];
			$this->create_time = null;
			$err = $this->insert();

			return $err;
		}

		/// patch functions 
		public function patch1_0() {
			return ERR_OK;
		}

		public function patch1_1() {
			return ERR_OK;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/