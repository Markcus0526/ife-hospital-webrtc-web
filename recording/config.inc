<?php
define('VERSION',		'1.0');

define('DEBUG_MODE',		0);
define('ENABLE_SSL',		1);

/* System Time Zone */
define('TIME_ZONE',		'GMT+800');

/* Frontend setting */
define('FRONTEND_APP',		'application');

/* DB related */
define('DB_HOSTNAME',		'www.markcus.vip');
define('DB_USER',		'root');
define('DB_PASSWORD',		'yiliaoadmin!2018');
define('DB_NAME',		'teleclinic');
define('DB_PORT',		3306);

/* SCS(signaling chat server) related */;
define('SCS_URL',		'wss://scs.markcus.vip:443');
define('ICE_SERVERS',		'[{url:"stun:stun1.l.google.com:19302"},{url:"stun:stun2.l.google.com:19302"},{url:"stun:stun3.l.google.com:19302"},{url:"stun:stun4.l.google.com:19302"},{url:"stun:stun.stunprotocol.org:3478"}]');

/* SMS related */;
define('SMS_ENABLE',		1);
define('SMS_USER',		'52963');
define('SMS_PASSWORD',		'6f4fio');
define('SMS_LIMITTIME',		20);

/* E-mail related */
define('MAIL_ENABLE',		1);
define('MAIL_FROM',		'quantest@yeah.net');
define('MAIL_FROMNAME',		'MARKCUS全球远程医疗会诊系统');
define('MAIL_SMTP_AUTH',		1);
define('MAIL_SMTP_USE_SSL',		0);
define('MAIL_SMTP_SERVER',		'smtp.yeah.net');
define('MAIL_SMTP_USER',		'quantest');
define('MAIL_SMTP_PASSWORD',		'abc123!');
define('MAIL_SMTP_PORT',		'25');

/* Security policy */
define('PASSWORD_MIN_LENGTH',		5);
define('LOGIN_FAIL_LOCK',		5);
define('RANDOM_SEED',		'teleclinic');
define('DIST_INC_COUNT',		2);
define('DIST_INC_NO',		0);

/* Language */
define('DEFAULT_LANGUAGE',		'zh-CN');

define('FFMPEG',		'/www/recording/resource/ffmpeg/ffmpeg');
define('FFPROBE',		'/www/recording/resource/ffmpeg/ffprobe');

/* Remain records */
define('RECORD_REMAIN',		true);
