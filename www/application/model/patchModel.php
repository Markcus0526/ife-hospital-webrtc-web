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
		"t_patch",
		"patch_id",
		array("version", "description"),
		array("auto_inc" => true));

	class patchModel extends model 
	{
		var $db_version;
		static public $patches = array(
			"1.0" => array("func" => "patch1_0", "description" => "最初版本"),
			"1.1" => array("func" => "patch1_1", "description" => "-Ubuntu对应\n-测试错误对应"),
			"1.2" => array("func" => "patch1_2", "description" => "-测试错误对应"),
			"1.3" => array("func" => "patch1_3", "description" => "-银联在线支付对应"),
			"1.4" => array("func" => "patch1_4", "description" => "-PayPal在线支付对应"),
			"1.5" => array("func" => "patch1_5", "description" => "-测试错误对应(2017/12/11)"),
			"1.6" => array("func" => "patch1_6", "description" => "-测试错误对应(2017/12/16)"),
			"1.7" => array("func" => "patch1_7", "description" => "-测试错误对应(2017/12/21)"),
			"1.8" => array("func" => "patch1_8", "description" => "-测试错误对应(2018/01/17)"),
			"1.9" => array("func" => "patch1_9", "description" => "-测试错误对应(2018/01/20)"),
			"1.10" => array("func" => "patch1_10", "description" => "-自动语言切换(2018/01/28)"),
			"1.11" => array("func" => "patch1_11", "description" => "-修改系统设置\n-汇率查询"),
			"1.12" => array("func" => "patch1_12", "description" => "-美元对应(2018/2/7)"),
			"1.13" => array("func" => "patch1_13", "description" => "-用户新增字段(2018/2/12)"),
			"1.14" => array("func" => "patch1_14", "description" => "-多语言对应(2018/2/14)"),
			"1.15" => array("func" => "patch1_15", "description" => "-病历修改(2018/2/20)"),
			"1.16" => array("func" => "patch1_16", "description" => "-整个会诊流程变动(2018/2/22)"),
			"1.17" => array("func" => "patch1_17", "description" => "-短信和邮件通知(2018/2/24)"),
			"1.18" => array("func" => "patch1_18", "description" => "-多语言对应(2018/2/28)"),
			"1.19" => array("func" => "patch1_19", "description" => "-新增专家是否接受患者功能(2018/3/4)"),
			"1.20" => array("func" => "patch1_20", "description" => "-专家信息的更改汇总(2018/3/6)"),
			"1.21" => array("func" => "patch1_21", "description" => "-专家医院多选(2018/3/7)"),
			"1.22" => array("func" => "patch1_22", "description" => "-英文系统中网页标签页名称和英文邮件中公司名称的中文显示(2018/3/15)"),
			"1.23" => array("func" => "patch1_23", "description" => "-数据库更新(2018/3/21)"),
			"1.24" => array("func" => "patch1_24", "description" => "-更新订单跟踪(2018/3/22)"),
			"1.25" => array("func" => "patch1_25", "description" => "-更新订单跟踪(2018/4/11)"),
			"1.26" => array("func" => "patch1_26", "description" => "-错误补丁(2018/4/14)"),
			"1.27" => array("func" => "patch1_27", "description" => "-用户名（专家、翻译、患者名）多语言对应(2018/4/29)"),
			"1.28" => array("func" => "patch1_28", "description" => "-安全性强化(2018/6/21)"),
			"1.29" => array("func" => "patch1_29", "description" => "-安全性强化错误补丁(2018/7/25)")
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
				$sql = "CREATE TABLE t_patch (
					`patch_id`  int NOT NULL AUTO_INCREMENT ,
					`version`  varchar(10) NOT NULL ,
					`description`  varchar(255) NOT NULL ,
					`create_time`  datetime NOT NULL ,
					`update_time`  datetime NULL ,
					`del_flag`  numeric(1,0) NOT NULL ,
					PRIMARY KEY (`patch_id`)
					);";

				$this->db->execute($sql);

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
			$sql = "ALTER TABLE `t_chistory`
MODIFY COLUMN `medical_reports`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `current_symptom`,
MODIFY COLUMN `symptom_records`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `medical_reports`,
MODIFY COLUMN `operation_reports`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `symptom_records`,
MODIFY COLUMN `roentgen_reports`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `operation_reports`,
MODIFY COLUMN `examination_reports`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `roentgen_reports`,
MODIFY COLUMN `roentgen_interviews`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `examination_reports`,
MODIFY COLUMN `pathology_interviews`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `roentgen_interviews`,
MODIFY COLUMN `prescription`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `pathology_interviews`;";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_2() {
			$sql = "ALTER TABLE `t_reguser`
MODIFY COLUMN `diplomas`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `i_age`,
MODIFY COLUMN `languages`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `diplomas`,
MODIFY COLUMN `diseases`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `languages`;";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_3() {
			$sql = "ALTER TABLE `t_reguser`
MODIFY COLUMN `diplomas`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `i_age`,
MODIFY COLUMN `languages`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `diplomas`,
MODIFY COLUMN `diseases`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `languages`;
ALTER TABLE `t_interview`
ADD COLUMN `pay_id`  varchar(10) NULL AFTER `change_time_at`,
ADD COLUMN `pay_time`  timestamp NULL DEFAULT NULL AFTER `pay_id`,
ADD COLUMN `refund_amount`  decimal(10,2) NULL AFTER `pay_time`,
ADD COLUMN `refund_time`  timestamp NULL DEFAULT NULL AFTER `refund_amount`;
DROP TABLE l_bhistory;";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_4() {
			return ERR_OK;
		}

		public function patch1_5() {
			$sql = "ALTER TABLE `t_interview`
ADD COLUMN `org_reserved_starttime`  timestamp NULL DEFAULT NULL AFTER `change_time_at`;";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_6() {
			return ERR_OK;
		}

		public function patch1_7() {
			return ERR_OK;
		}

		public function patch1_8() {
			$sql = "ALTER TABLE `t_interview`
ADD COLUMN `notify_interp_time`  timestamp NULL DEFAULT NULL AFTER `refund_time`;";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_9() {
			$sql = "ALTER TABLE `m_user`
MODIFY COLUMN `d_fee`  decimal(10,2) NULL DEFAULT NULL AFTER `d_hospital_id`;
ALTER TABLE `t_interview`
MODIFY COLUMN `d_cost`  decimal(10,2) NULL DEFAULT NULL AFTER `cost`,
MODIFY COLUMN `i_cost`  decimal(10,2) NULL DEFAULT NULL AFTER `d_cost`;
";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_10() {
			return ERR_OK;
		}

		public function patch1_11() {
			$sql = "CREATE TABLE `m_settings` (
  `settings_id` int(10) unsigned NOT NULL,
  `save_record_limit` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO m_settings(settings_id, save_record_limit, create_time, update_time)
VALUES(1, 30, NOW(), NULL);

CREATE TABLE `m_exrate` (
`exrate_id`  int NOT NULL AUTO_INCREMENT ,
`exrate_time`  timestamp NOT NULL ,
`rmb_to_usd`  decimal(10,4) NOT NULL ,
`usd_to_rmb`  decimal(10,4) NOT NULL ,
`create_time`  timestamp NOT NULL ,
`update_time`  timestamp NULL ,
PRIMARY KEY (`exrate_id`),
INDEX `exrate_time` (`exrate_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
;

INSERT INTO m_exrate(exrate_time, rmb_to_usd, usd_to_rmb, create_time, update_time)
VALUES('2016-07-22 10:32:31', 0.1502, 6.6563, NOW(), NULL);

";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_12() {
			$sql = "ALTER TABLE `m_user`
ADD COLUMN `d_cunit`  varchar(3) NULL DEFAULT NULL AFTER `d_fee`,
ADD INDEX `d_fee` (`d_fee`) ,
ADD INDEX `user_type` (`user_type`) ;

UPDATE m_user SET d_cunit='rmb' WHERE user_type=32;

ALTER TABLE `t_interview`
ADD COLUMN `cunit`  varchar(3) NULL AFTER `i_cost`;

UPDATE t_interview SET cunit='rmb';

DROP TABLE m_exrate;
CREATE TABLE `m_exrate` (
`exrate_id`  int NOT NULL AUTO_INCREMENT ,
`exrate_time`  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
`rmb_to_usd`  decimal(10,4) NOT NULL ,
`usd_to_rmb`  decimal(10,4) NOT NULL ,
`create_time`  timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
`update_time`  timestamp NULL ,
PRIMARY KEY (`exrate_id`),
INDEX `exrate_time` (`exrate_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO m_exrate(exrate_time, rmb_to_usd, usd_to_rmb, create_time, update_time)
VALUES('2016-07-22 10:32:31', 0.1502, 6.6563, NOW(), NULL);
";

			$this->db->execute_batch($sql);

			return ERR_OK;

		}

		public function patch1_13() {
			$sql = "ALTER TABLE `m_user`
ADD COLUMN `home_address`  varchar(255) NULL AFTER `other_tel`,
ADD COLUMN `passports`  varchar(255) NULL AFTER `diplomas`;
	ALTER TABLE `t_reguser`
ADD COLUMN `home_address`  varchar(255) NULL AFTER `other_tel`,
ADD COLUMN `passports`  varchar(255) NULL AFTER `diplomas`;
ALTER TABLE `t_feedback`
ADD COLUMN `email`  varchar(80) NULL AFTER `mobile`;

";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_14() {
			$sql = "ALTER TABLE `m_disease`
ADD COLUMN `disease_name_l`  text NULL AFTER `disease_name`;
ALTER TABLE `m_hospital`
ADD COLUMN `hospital_name_l`  text NULL AFTER `hospital_name`;
ALTER TABLE `m_depart`
ADD COLUMN `depart_name_l`  text NULL AFTER `depart_name`;
ALTER TABLE `m_hcountry`
ADD COLUMN `country_name_l`  text NULL AFTER `country_name`;
ALTER TABLE `m_language`
ADD COLUMN `language_name_l`  text NULL AFTER `language_name`;
ALTER VIEW `v_udisease` AS 
SELECT u.user_id, d.disease_id, d.disease_name, d.disease_name_l
FROM m_udisease u
INNER JOIN m_disease d ON u.disease_id=d.disease_id ;

ALTER TABLE `m_disease`
ADD COLUMN `sort`  int NULL AFTER `description`;

CREATE TABLE `m_dtemplate` (
`dtemplate_id`  int UNSIGNED NOT NULL AUTO_INCREMENT ,
`disease_id`  int NOT NULL ,
`dtemplate_name`  varchar(100) NOT NULL ,
`dtemplate_name_l`  text DEFAULT NULL, 
`sort` int(11) DEFAULT NULL,
`template_file` text DEFAULT NULL,
`create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
`create_user_id` bigint(20) DEFAULT NULL,
`update_time` timestamp NULL DEFAULT NULL,
`update_user_id` bigint(20) DEFAULT NULL,
`del_flag` tinyint(1) NOT NULL,
PRIMARY KEY (`dtemplate_id`),
KEY `disease_id` (`disease_id`),
KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

";
			$this->db->execute_batch($sql);

			$sort = 0;
			$disease = new diseaseModel;
			$err = $disease->select("", array("order" => "disease_id DESC"));

			while ($err == ERR_OK) {
				$disease->sort = $sort;
				$disease->save();
				$sort ++;

				$err = $disease->fetch();
			}

			return ERR_OK;
		}

		public function patch1_15() {
			$sql = "ALTER TABLE `t_chistory`
DROP COLUMN examination_result,
DROP COLUMN current_symptom,
DROP COLUMN prescription,
DROP COLUMN past_diseases,
DROP COLUMN prescription_details;

ALTER TABLE `t_chistory`
DROP COLUMN medical_reports,
DROP COLUMN symptom_records,
DROP COLUMN operation_reports,
DROP COLUMN roentgen_reports,
DROP COLUMN examination_reports,
DROP COLUMN roentgen_interviews,
DROP COLUMN pathology_interviews;

ALTER TABLE `t_chistory`
ADD COLUMN `trans_flag`  tinyint(1) NOT NULL DEFAULT 0 AFTER `user_id`,
ADD COLUMN `trans_lang`  varchar(7) NULL AFTER `trans_flag`,
ADD COLUMN `org_id`  bigint NULL AFTER `trans_lang`;

ALTER TABLE `t_chistory`
ADD COLUMN birthday date DEFAULT NULL AFTER patient_sex,
DROP COLUMN patient_age,
ADD COLUMN home_address varchar(255) NULL AFTER  birthday,
ADD COLUMN passports varchar(255) NULL AFTER  home_address;

CREATE TABLE `t_cattach` (
`cattach_id`  bigint UNSIGNED NOT NULL AUTO_INCREMENT ,
`chistory_id`  bigint NOT NULL ,
`dtemplate_id`  int NOT NULL ,
`files` text NOT NULL ,
`create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
`create_user_id` bigint(20) DEFAULT NULL,
`update_time` timestamp NULL DEFAULT NULL,
`update_user_id` bigint(20) DEFAULT NULL,
`del_flag` tinyint(1) NOT NULL,
PRIMARY KEY (`cattach_id`),
KEY `dtemplate_id` (`dtemplate_id`),
KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_16() {
			$sql = "ALTER TABLE `t_interview`
ADD COLUMN `trans_chistory_id`  bigint(20) DEFAULT NULL AFTER `chistory_id`;
ALTER TABLE `t_interview`
ADD COLUMN `trans_prescription`  varchar(255) DEFAULT NULL AFTER `prescription`;
ALTER TABLE `t_chistory`
DROP COLUMN trans_lang,
ADD COLUMN `interview_id`  bigint(20) DEFAULT NULL AFTER `trans_flag`;
ALTER TABLE `t_chistory`
ADD COLUMN `post_flag`  tinyint(1) DEFAULT NULL AFTER `org_id`;
			";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_17() {
			$sql = "DROP TABLE IF EXISTS `t_sms`;
			CREATE TABLE `t_message` (
  `message_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(25) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `email_to_name` varchar(50) DEFAULT NULL,
  `content` text NOT NULL,
  `email_title` varchar(100) DEFAULT NULL,
  `email_header` varchar(100) DEFAULT NULL,
  `sms_header` varchar(50) DEFAULT NULL,
  `send_time` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `send_time` (`send_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `t_interview`
ADD COLUMN `i_finshed_interview_id`  bigint(20) DEFAULT NULL AFTER `interview_id`;
ALTER TABLE `t_interview`
ADD INDEX `status` (`status`) ;
";
			$this->db->execute_batch($sql);

			return ERR_OK;

		}

		public function patch1_18() {
			$sql = "ALTER VIEW `v_ulang` AS 
SELECT u.user_id, l.language_id, l.language_name, l.language_name_l, l.sort
FROM m_ulang u
INNER JOIN m_language l ON u.language_id=l.language_id ;
";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_19() {
			$sql = "ALTER TABLE `t_interview`
ADD COLUMN `reject_cause_id`  tinyint(4) DEFAULT NULL AFTER `cancel_cause_note`;
ALTER TABLE `t_interview`
ADD COLUMN `reject_cause_note`  text DEFAULT NULL AFTER `reject_cause_id`;
";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_20() {
			$sql = "ALTER TABLE `m_user`
ADD COLUMN `d_depart`  varchar(255) DEFAULT NULL AFTER `d_depart_id`;
ALTER TABLE `m_user` DROP COLUMN `d_depart_id`;
ALTER TABLE `m_user` DROP COLUMN `d_post`;
ALTER TABLE `m_user` MODIFY COLUMN `d_title` varchar(255) DEFAULT NULL;
ALTER TABLE `t_reguser`
ADD COLUMN `d_depart`  varchar(255) DEFAULT NULL AFTER `d_depart_id`;
ALTER TABLE `t_reguser` DROP COLUMN `d_depart_id`;
ALTER TABLE `t_reguser` DROP COLUMN `d_post`;
ALTER TABLE `t_reguser` MODIFY COLUMN `d_title` varchar(255) DEFAULT NULL;
CREATE TABLE `m_string` (
  `string_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `string` varchar(520) NOT NULL,
  `string_l` text DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`string_id`),
  UNIQUE INDEX `string` (`string`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `m_notice` (
  `notice_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL,
  `content_l` text DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`notice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `m_notice` VALUES ('1', '请您使用Chrome浏览器，视频会诊前请保证网络畅通并进行系统检测。', null, '2018-03-06 17:23:44', null);
INSERT INTO `m_notice` VALUES ('2', '若视频会诊过程中更换摄像头、耳麦等设备，需要重启浏览器，重新登录系统。', null, '2018-03-06 17:23:57', null);
";
			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_21() {
			$sql = "CREATE TABLE `m_uhospital` (
  `uhospital_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `hospital_id` int(10) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`uhospital_id`),
  UNIQUE INDEX `uhospital` (`user_id`, `hospital_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE VIEW `v_uhospital` AS 
SELECT u.user_id, h.hospital_id, h.hospital_name, h.hospital_name_l, h.sort, h.hcountry_id
FROM m_uhospital u
INNER JOIN m_hospital h ON u.hospital_id=h.hospital_id;

INSERT INTO m_uhospital
(user_id, hospital_id)
SELECT user_id, d_hospital_id
FROM m_user
WHERE d_hospital_id IS NOT NULL;
ALTER TABLE m_user
DROP COLUMN d_hospital_id;

ALTER TABLE `t_reguser`
ADD COLUMN `hospitals`  varchar(255) DEFAULT NULL AFTER `d_hospital_id`;
UPDATE t_reguser SET hospitals=d_hospital_id;
ALTER TABLE t_reguser
DROP COLUMN d_hospital_id;
";
			$this->db->execute_batch($sql);

			return ERR_OK;

		}

		public function patch1_22() {
			$sql = "ALTER TABLE `t_message`
ADD COLUMN `email_from_name`  varchar(100) DEFAULT NULL AFTER `email_to_name`;";

			$this->db->execute_batch($sql);

			return ERR_OK;

		}

		public function patch1_23() {
			$sql = "ALTER TABLE `m_string`
MODIFY COLUMN `string`  varchar(511) NOT NULL;
ALTER TABLE `m_user`
MODIFY COLUMN `introduction`  varchar(511) DEFAULT NULL;
ALTER TABLE `t_reguser`
MODIFY COLUMN `introduction`  varchar(511) DEFAULT NULL;
DROP TABLE m_depart;";

			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_24() {
			$sql = "ALTER TABLE `l_ihistory`
ADD COLUMN `data4`  text DEFAULT NULL AFTER `data3`;";

			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_25() {
			return ERR_OK;
		}

		public function patch1_26() {
			return ERR_OK;
		}

		public function patch1_27() {
			$sql = "ALTER TABLE `m_user`
ADD COLUMN `user_name_l`  text DEFAULT NULL AFTER `user_name`;";

			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_28() {
			$sql = "ALTER TABLE `m_user` DROP COLUMN `login_fails`;
ALTER TABLE `m_user` DROP COLUMN `lock_flg`;
ALTER TABLE `m_user` ADD COLUMN `lock_flag` tinyint(1) DEFAULT 0 AFTER admin_priv;
ALTER TABLE `m_user` ADD COLUMN `lock_date` date DEFAULT NULL AFTER lock_flag;
CREATE TABLE `l_logfail` (
  `logfail_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `fail_type` tinyint(1) NOT NULL,
  `login_fails` int(10) NOT NULL,
  `login_date` date NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`logfail_id`),
  INDEX `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `m_priv`
ADD COLUMN `priv_syscheck`  int DEFAULT NULL AFTER `priv_feedback`;
			";

			$this->db->execute_batch($sql);

			return ERR_OK;
		}

		public function patch1_29() {
			$sql = "ALTER TABLE `m_user` ADD COLUMN `lock_time` timestamp NULL DEFAULT NULL AFTER `lock_date`;
UPDATE m_user SET lock_time = lock_date WHERE lock_date IS NOT NULL;
ALTER TABLE `m_user` DROP COLUMN `lock_date`;
			";

			$this->db->execute_batch($sql);

			return ERR_OK;
		}
	};
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/