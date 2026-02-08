/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : teleclinic

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-11-28 16:20:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for l_access
-- ----------------------------
DROP TABLE IF EXISTS `l_access`;
CREATE TABLE `l_access` (
  `access_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `access_time` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`access_id`),
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `create_time` (`create_time`)
) ENGINE=InnoDB AUTO_INCREMENT=2675 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for l_ihistory
-- ----------------------------
DROP TABLE IF EXISTS `l_ihistory`;
CREATE TABLE `l_ihistory` (
  `ihistory_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `interview_id` bigint(20) NOT NULL,
  `ihistory_type` int(11) NOT NULL,
  `data1` text,
  `data2` text,
  `data3` text,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`ihistory_id`),
  KEY `interview_id` (`interview_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2600 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for l_irating
-- ----------------------------
DROP TABLE IF EXISTS `l_irating`;
CREATE TABLE `l_irating` (
  `irating_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `interview_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `interpreter_id` bigint(20) NOT NULL,
  `rate` tinyint(1) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`irating_id`)
) ENGINE=InnoDB AUTO_INCREMENT=217 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for l_passkey
-- ----------------------------
DROP TABLE IF EXISTS `l_passkey`;
CREATE TABLE `l_passkey` (
  `passkey_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone_num` varchar(20) NOT NULL,
  `passkey_text` varchar(8) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`passkey_id`),
  UNIQUE KEY `phone_num` (`phone_num`)
) ENGINE=InnoDB AUTO_INCREMENT=346 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for l_session
-- ----------------------------
DROP TABLE IF EXISTS `l_session`;
CREATE TABLE `l_session` (
  `session_id` varchar(40) NOT NULL DEFAULT '',
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `mobile` varchar(20) DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT NULL,
  `access_time` timestamp NULL DEFAULT NULL,
  `ip` varchar(15) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`session_id`,`user_id`),
  KEY `access_time` (`access_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_country
-- ----------------------------
DROP TABLE IF EXISTS `m_country`;
CREATE TABLE `m_country` (
  `country_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_name` varchar(50) DEFAULT NULL,
  `country_code` varchar(4) DEFAULT NULL,
  `dial_code` varchar(5) DEFAULT NULL,
  `dial_code_order` int(11) DEFAULT NULL,
  `sub_dial_codes` text,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`country_id`),
  KEY `dial_code` (`dial_code`) USING BTREE,
  KEY `dial_code_order` (`dial_code`,`dial_code_order`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_depart
-- ----------------------------
DROP TABLE IF EXISTS `m_depart`;
CREATE TABLE `m_depart` (
  `depart_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `depart_name` varchar(100) NOT NULL,
  `depart_name_l` text,
  `depart_path` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT NULL,
  `sort` varchar(255) DEFAULT NULL,
  `expand_flag` tinyint(1) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`depart_id`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_disease
-- ----------------------------
DROP TABLE IF EXISTS `m_disease`;
CREATE TABLE `m_disease` (
  `disease_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `disease_name` varchar(100) NOT NULL,
  `disease_name_l` text,
  `description` text,
  `sort` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`disease_id`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=10039 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_dtemplate
-- ----------------------------
DROP TABLE IF EXISTS `m_dtemplate`;
CREATE TABLE `m_dtemplate` (
  `dtemplate_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `disease_id` int(11) NOT NULL,
  `dtemplate_name` varchar(100) NOT NULL,
  `dtemplate_name_l` text,
  `sort` int(11) DEFAULT NULL,
  `template_file` text,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`dtemplate_id`),
  KEY `disease_id` (`disease_id`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_exrate
-- ----------------------------
DROP TABLE IF EXISTS `m_exrate`;
CREATE TABLE `m_exrate` (
  `exrate_id` int(11) NOT NULL AUTO_INCREMENT,
  `exrate_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `rmb_to_usd` decimal(10,4) NOT NULL,
  `usd_to_rmb` decimal(10,4) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`exrate_id`),
  KEY `exrate_time` (`exrate_time`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_hcountry
-- ----------------------------
DROP TABLE IF EXISTS `m_hcountry`;
CREATE TABLE `m_hcountry` (
  `hcountry_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_name` varchar(50) DEFAULT NULL,
  `country_name_l` text,
  `sort` int(10) unsigned DEFAULT NULL,
  `h_expand_flag` tinyint(1) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`hcountry_id`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=221 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_hospital
-- ----------------------------
DROP TABLE IF EXISTS `m_hospital`;
CREATE TABLE `m_hospital` (
  `hospital_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hcountry_id` int(11) NOT NULL,
  `hospital_name` varchar(50) NOT NULL,
  `hospital_name_l` text,
  `address` varchar(255) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`hospital_id`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_language
-- ----------------------------
DROP TABLE IF EXISTS `m_language`;
CREATE TABLE `m_language` (
  `language_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_name` varchar(50) NOT NULL,
  `language_name_l` text,
  `language_code` varchar(10) DEFAULT NULL,
  `sort` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`language_id`),
  UNIQUE KEY `language_code` (`language_code`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_priv
-- ----------------------------
DROP TABLE IF EXISTS `m_priv`;
CREATE TABLE `m_priv` (
  `priv_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `priv_reg_user` int(11) DEFAULT NULL,
  `priv_patients` int(11) DEFAULT NULL,
  `priv_doctors` int(11) DEFAULT NULL,
  `priv_interpreters` int(11) DEFAULT NULL,
  `priv_hospitals` int(11) DEFAULT NULL,
  `priv_chistory` int(11) DEFAULT NULL,
  `priv_reserve` int(11) DEFAULT NULL,
  `priv_interviews` int(11) DEFAULT NULL,
  `priv_profile` int(11) DEFAULT NULL,
  `priv_stats` int(11) DEFAULT NULL,
  `priv_feedback` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`priv_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_settings
-- ----------------------------
DROP TABLE IF EXISTS `m_settings`;
CREATE TABLE `m_settings` (
  `settings_id` int(10) unsigned NOT NULL,
  `save_record_limit` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`settings_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_udisease
-- ----------------------------
DROP TABLE IF EXISTS `m_udisease`;
CREATE TABLE `m_udisease` (
  `udisease_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `disease_id` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) NOT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`udisease_id`),
  KEY `del_flag` (`del_flag`),
  KEY `language_id` (`disease_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_ulang
-- ----------------------------
DROP TABLE IF EXISTS `m_ulang`;
CREATE TABLE `m_ulang` (
  `ulang_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `language_id` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) NOT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ulang_id`),
  KEY `del_flag` (`del_flag`),
  KEY `language_id` (`language_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for m_user
-- ----------------------------
DROP TABLE IF EXISTS `m_user`;
CREATE TABLE `m_user` (
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_type` decimal(2,0) NOT NULL,
  `password` varchar(40) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `sex` decimal(1,0) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `mobile` varchar(25) DEFAULT NULL,
  `other_tel` varchar(25) DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `introduction` text,
  `d_title` varchar(50) DEFAULT NULL,
  `d_post` varchar(50) DEFAULT NULL,
  `d_depart_id` int(11) DEFAULT NULL,
  `d_hospital_id` int(11) DEFAULT NULL,
  `d_fee` decimal(10,2) DEFAULT NULL,
  `d_cunit` varchar(3) DEFAULT NULL,
  `i_age` int(11) DEFAULT NULL,
  `diplomas` varchar(255) DEFAULT NULL,
  `passports` varchar(255) DEFAULT NULL,
  `admin_priv` varchar(255) DEFAULT NULL,
  `login_fails` decimal(2,0) DEFAULT '0',
  `lock_flg` int(1) DEFAULT NULL,
  `access_time` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `del_flag` (`del_flag`),
  KEY `d_fee` (`d_fee`),
  KEY `user_type` (`user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_cattach
-- ----------------------------
DROP TABLE IF EXISTS `t_cattach`;
CREATE TABLE `t_cattach` (
  `cattach_id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `chistory_id` bigint(11) NOT NULL,
  `dtemplate_id` int(11) NOT NULL,
  `files` text NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`cattach_id`),
  KEY `dtemplate_id` (`dtemplate_id`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_chistory
-- ----------------------------
DROP TABLE IF EXISTS `t_chistory`;
CREATE TABLE `t_chistory` (
  `chistory_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `trans_flag` tinyint(1) NOT NULL DEFAULT '0',
  `interview_id` bigint(20) DEFAULT NULL,
  `org_id` bigint(20) DEFAULT NULL,
  `post_flag` tinyint(1) DEFAULT NULL,
  `chistory_name` varchar(100) DEFAULT NULL,
  `patient_name` varchar(50) DEFAULT NULL,
  `patient_sex` tinyint(1) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `passports` varchar(255) DEFAULT NULL,
  `disease_id` int(11) DEFAULT NULL,
  `want_resolve_problem` text,
  `sensitive_medicine` text,
  `smoking_drinking` text,
  `chronic_disease` text,
  `family_disease` text,
  `note` text,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`chistory_id`),
  KEY `del_flag` (`del_flag`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_dtime
-- ----------------------------
DROP TABLE IF EXISTS `t_dtime`;
CREATE TABLE `t_dtime` (
  `dtime_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` bigint(20) NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `state` tinyint(1) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`dtime_id`),
  UNIQUE KEY `doctor_time` (`doctor_id`,`start_time`),
  KEY `time` (`start_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2240 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_feedback
-- ----------------------------
DROP TABLE IF EXISTS `t_feedback`;
CREATE TABLE `t_feedback` (
  `feedback_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `user_type` int(4) DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` mediumtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`feedback_id`),
  KEY `del_flg` (`del_flag`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for t_imessage
-- ----------------------------
DROP TABLE IF EXISTS `t_imessage`;
CREATE TABLE `t_imessage` (
  `imessage_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `interview_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_type` int(11) NOT NULL,
  `content` text NOT NULL,
  `read_flag` int(11) DEFAULT NULL,
  `no` varchar(16) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`imessage_id`),
  KEY `interview` (`interview_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_interp
-- ----------------------------
DROP TABLE IF EXISTS `t_interp`;
CREATE TABLE `t_interp` (
  `interp_id` int(11) NOT NULL AUTO_INCREMENT,
  `interview_id` bigint(20) NOT NULL,
  `planguage_id` int(11) NOT NULL,
  `dlanguage_id` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  `del_flag` tinyint(4) NOT NULL,
  PRIMARY KEY (`interp_id`),
  UNIQUE KEY `ilanguage` (`interview_id`,`dlanguage_id`,`planguage_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=523 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_interview
-- ----------------------------
DROP TABLE IF EXISTS `t_interview`;
CREATE TABLE `t_interview` (
  `interview_id` bigint(20) unsigned NOT NULL,
  `i_finshed_interview_id` bigint(20) DEFAULT NULL,
  `patient_id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) DEFAULT NULL,
  `interpreter_id` bigint(20) DEFAULT NULL,
  `reserved_starttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reserved_endtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `chistory_id` bigint(20) DEFAULT NULL,
  `trans_chistory_id` bigint(20) DEFAULT NULL,
  `planguage_id` int(11) DEFAULT NULL,
  `dlanguage_id` int(11) DEFAULT NULL,
  `need_interpreter` tinyint(1) DEFAULT NULL,
  `status` decimal(2,0) NOT NULL,
  `sub_status` int(11) DEFAULT NULL,
  `cancel_cause_id` tinyint(4) DEFAULT NULL,
  `cancel_cause_note` text,
  `interview_starttime` timestamp NULL DEFAULT NULL,
  `interview_endtime` timestamp NULL DEFAULT NULL,
  `interview_seconds` int(11) DEFAULT NULL,
  `record_video` varchar(128) DEFAULT NULL,
  `patient_status` tinyint(1) DEFAULT NULL,
  `patient_starttime` timestamp NULL DEFAULT NULL,
  `patient_leavetime` timestamp NULL DEFAULT NULL,
  `patient_ip` varchar(15) DEFAULT NULL,
  `doctor_status` tinyint(1) DEFAULT NULL,
  `doctor_starttime` timestamp NULL DEFAULT NULL,
  `doctor_leavetime` timestamp NULL DEFAULT NULL,
  `doctor_ip` varchar(15) DEFAULT NULL,
  `interpreter_status` tinyint(1) DEFAULT NULL,
  `interpreter_starttime` timestamp NULL DEFAULT NULL,
  `interpreter_leavetime` timestamp NULL DEFAULT NULL,
  `interpreter_ip` varchar(15) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `d_cost` decimal(10,2) DEFAULT NULL,
  `i_cost` decimal(10,2) DEFAULT NULL,
  `cunit` varchar(3) DEFAULT NULL,
  `filesize` decimal(11,2) DEFAULT NULL,
  `doctor_sign` varchar(50) DEFAULT NULL,
  `prescription` varchar(255) DEFAULT NULL,
  `trans_prescription` varchar(255) DEFAULT NULL,
  `chat_file` varchar(255) DEFAULT NULL,
  `change_time_at` timestamp NULL DEFAULT NULL,
  `org_reserved_starttime` timestamp NULL DEFAULT NULL,
  `pay_id` varchar(10) DEFAULT NULL,
  `pay_time` timestamp NULL DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refund_time` timestamp NULL DEFAULT NULL,
  `notify_interp_time` timestamp NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`interview_id`),
  KEY `visitor_id` (`patient_id`) USING BTREE,
  KEY `del_flag` (`del_flag`),
  KEY `need_interp` (`need_interpreter`,`interpreter_id`) USING BTREE,
  KEY `reserved_starttime` (`reserved_starttime`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_message
-- ----------------------------
DROP TABLE IF EXISTS `t_message`;
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
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_patch
-- ----------------------------
DROP TABLE IF EXISTS `t_patch`;
CREATE TABLE `t_patch` (
  `patch_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NULL DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`patch_id`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_patch_record
-- ----------------------------
DROP TABLE IF EXISTS `t_patch_record`;
CREATE TABLE `t_patch_record` (
  `patch_id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `create_time` datetime NOT NULL,
  `update_time` datetime DEFAULT NULL,
  `del_flag` decimal(1,0) NOT NULL,
  PRIMARY KEY (`patch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for t_reguser
-- ----------------------------
DROP TABLE IF EXISTS `t_reguser`;
CREATE TABLE `t_reguser` (
  `reguser_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_type` decimal(2,0) NOT NULL,
  `password` varchar(40) NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `sex` decimal(1,0) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `other_tel` varchar(20) DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `introduction` text,
  `d_title` varchar(50) DEFAULT NULL,
  `d_post` varchar(50) DEFAULT NULL,
  `d_depart_id` int(11) DEFAULT NULL,
  `d_hospital_id` int(11) DEFAULT NULL,
  `d_fee` int(11) DEFAULT NULL,
  `i_age` int(11) DEFAULT NULL,
  `diplomas` text,
  `passports` varchar(255) DEFAULT NULL,
  `languages` text,
  `diseases` text,
  `status` tinyint(1) DEFAULT NULL,
  `reject_note` varchar(255) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user_id` bigint(20) DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `update_user_id` bigint(20) DEFAULT NULL,
  `del_flag` tinyint(1) NOT NULL,
  PRIMARY KEY (`reguser_id`),
  KEY `del_flag` (`del_flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- View structure for v_udisease
-- ----------------------------
DROP VIEW IF EXISTS `v_udisease`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER  VIEW `v_udisease` AS SELECT u.user_id, d.disease_id, d.disease_name, d.disease_name_l
FROM m_udisease u
INNER JOIN m_disease d ON u.disease_id=d.disease_id ;

-- ----------------------------
-- View structure for v_ulang
-- ----------------------------
DROP VIEW IF EXISTS `v_ulang`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER  VIEW `v_ulang` AS SELECT u.user_id, l.language_id, l.language_name, l.sort
FROM m_ulang u
INNER JOIN m_language l ON u.language_id=l.language_id ;

-- ----------------------------
-- Function structure for hash_password
-- ----------------------------
DROP FUNCTION IF EXISTS `hash_password`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `hash_password`(data VARCHAR(256)) RETURNS varchar(40) CHARSET utf8
BEGIN
DECLARE secret_key VARCHAR(128) DEFAULT 'teleclinic=3b780d30988cf88f61e27fe803a8c4cf27/markcus:2017';

DECLARE ipad,opad BINARY(64);
DECLARE hexkey CHAR(128);
DECLARE hmac CHAR(40);

SET hexkey = RPAD(HEX(secret_key),128,"0");


/* process in 64-bit blocks to avoid overflow when converting to decimal*/
SET ipad = UNHEX(CONCAT(
LPAD(CONV(CONV( MID(hexkey,1  ,16), 16, 10 ) ^ CONV( '3636363636363636', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,17 ,16), 16, 10 ) ^ CONV( '3636363636363636', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,33 ,16), 16, 10 ) ^ CONV( '3636363636363636', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,49 ,16), 16, 10 ) ^ CONV( '3636363636363636', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,65 ,16), 16, 10 ) ^ CONV( '3636363636363636', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,81 ,16), 16, 10 ) ^ CONV( '3636363636363636', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,97 ,16), 16, 10 ) ^ CONV( '3636363636363636', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,113,16), 16, 10 ) ^ CONV( '3636363636363636', 16, 10 ),10,16),16,"0")
));

SET opad = UNHEX(CONCAT(
LPAD(CONV(CONV( MID(hexkey,1  ,16), 16, 10 ) ^ CONV( '5c5c5c5c5c5c5c5c', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,17 ,16), 16, 10 ) ^ CONV( '5c5c5c5c5c5c5c5c', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,33 ,16), 16, 10 ) ^ CONV( '5c5c5c5c5c5c5c5c', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,49 ,16), 16, 10 ) ^ CONV( '5c5c5c5c5c5c5c5c', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,65 ,16), 16, 10 ) ^ CONV( '5c5c5c5c5c5c5c5c', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,81 ,16), 16, 10 ) ^ CONV( '5c5c5c5c5c5c5c5c', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,97 ,16), 16, 10 ) ^ CONV( '5c5c5c5c5c5c5c5c', 16, 10 ),10,16),16,"0"),
LPAD(CONV(CONV( MID(hexkey,113,16), 16, 10 ) ^ CONV( '5c5c5c5c5c5c5c5c', 16, 10 ),10,16),16,"0")
));

SET hmac = SHA1(CONCAT(opad,UNHEX(SHA1(CONCAT(ipad,data)))));

RETURN hmac;

END
;;
DELIMITER ;
