<?php
define('VERSION',		'1.28');

define('DEBUG_MODE',		0);
define('ENABLE_SSL',		1);
define('AUTO_LANGUAGE_FROM_IP',		1);

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

/* Frontend server related */;
define('FRONTEND_URL',		'https://www.markcus.vip');
/* SCS(signaling chat server) related */;
define('SCS_URL',		'wss://scs.markcus.vip:443');
define('ICE_SERVERS',		'[{url:"stun:stun.ideasip.com:3478"},{url:"stun:stun3.l.google.com:19302"},{url:"stun:stun.schlund.de"},{url:"stun:stun4.l.google.com:19302"},{url:"turn:52.83.194.251:3478",username:"yiliao",credential:"yiliaoadmin2018"}]');

/* Recording server related */;
define('RECORDING_API',		'https://record.markcus.vip/');

/* SMS related */;
define('SMS_ENABLE',		1);
define('SMS_APIKEY',		'd5d6932531f7a5d34e2724bc307f969d');
define('SMS_LIMITTIME',		20);

/* E-mail related */
define('MAIL_ENABLE',		1);
define('MAIL_FROM',		'ylserver@yeah.net');
define('MAIL_FROMNAME',		'MARKCUS全球远程医疗会诊系统');
define('MAIL_SMTP_AUTH',		1);
define('MAIL_SMTP_USE_SSL',		0);
define('MAIL_SMTP_SERVER',		'smtp.yeah.net');
define('MAIL_SMTP_USER',		'ylserver@yeah.net');
define('MAIL_SMTP_PASSWORD',		'markcusgjylhzxt');
define('MAIL_SMTP_PORT',		'25');

/* 安全策略 */
define('PASSWORD_MIN_LENGTH',		8);
define('PASSWORD_STRENGTH',		1);
define('LOGIN_FAIL_LOCK',		5);
define('LOGIN_FAIL_DISABLE',		3);
define('RANDOM_SEED',		'teleclinic');
define('DIST_INC_COUNT',		2);
define('DIST_INC_NO',		0);

/* Language */
define('DEFAULT_LANGUAGE',		'zh-CN');
/* 翻译费用 */
define('INTERPRETER_FEE',		7.5);
/* 页面刷新 */
define('REFRESH_INTERVAL',		15);
/* 会诊支付期限(分钟) */
define('INTERVIEW_PAY_LIMIT',		15);
/* 会诊前提醒(分钟) */
define('INTERVIEW_ALARM_START',		5);
/* 患者能同意更改时间的期限(分钟) */
define('INTERVIEW_ACCEPT_TIME_LIMIT',		30);
/* 专家没在会诊开始*分钟内上线,自动结束会诊 */
define('INTERVIEW_OFFLINE_LIMIT',		5);
/* 专家离线后没在*分钟内上线,自动结束会诊 */
define('INTERVIEW_REOFFLINE_LIMIT',		2);
/* 专家在会诊开始*分钟内仍没上线,温馨提示 */
define('INTERVIEW_ALARM_OFFLINE_LIMIT',		2);
/* 翻译无违约金取消期限(时间) */
define('INTERVIEW_I_CANCEL_LIMIT',		24);
/* 会诊前*小时仍没有翻译接单 */
define('INTERVIEW_NO_INTERP_ALARM_LIMIT',		24);
/* 违约金 */
define('PENALTY_AMOUNT',		100);
/* 患者可以预约距当前时间*小时后的会诊时间 */
define('INTERVIEW_PATIENT_RESERVABLE_TIME_AFTER_NOW',		24);
/* 专家可以更改至距当前时间*小时后的会诊时间 */
define('INTERVIEW_DOCTOR_CHANGABLE_TIME_AFTER_NOW',		24);
/* 专家可以设置距当前时间*小时后的会诊时间 */
define('INTERVIEW_DOCTOR_SETTABLE_TIME_AFTER_NOW',		24);

/* 在线支付设置 */
define('DEBUG_PAY',		0);// 0:使用生产环境证书 1:使用测试环境证书 2:无使用在线支付，经常支付成功
/* 银联在线支付设置 */
define('CHINAPAY_MERCHANTID',		'822221048160184');
define('CHINAPAY_SIGNCERT_PWD',		'319768');
/* PayPal在线支付设置 */
define('PAYPAL_EMAIL_ADDRESS',		'info@markcusc.cn');
define('PAYPAL_IDENTITY_TOKEN',		'BoDnLYcVPVH8A-0Hw5DFdaCvgKNP1McaUOsPvNTI8DzzL4bAPh0Fnoj6Cbq');
define('PAYPAL_CERT_ID',		'88UN3HH6W3XB8');
define('PAYPAL_EWP_PRIVATE_KEY_PWD',		'');
define('PAYPAL_SANDBOX_CLIENT_ID',		'AUpXCNZFBb5iPTyeVVfxKvLqZ8nhyFoEU-f83UoDWkED8ltBGfbqxfeieQIwZTQmyt35ERhqtAB_3AC-');
define('PAYPAL_SANDBOX_CLIENT_SECRET',		'EBlndvmubzKf2GiYuXf6NxsBn7Fh88ff4IGBdctXxBPB_2jvxHDH96O03keHtmadh6p0eabFIokpvB7Z');
define('PAYPAL_CLIENT_ID',		'AbY8rAcIPSsbnnxySY0BcglOZIBGZ18iIkeMyUhVkeDy5dkt7qZAP8XMT5NuMnb0XpbHWfTxn1WKpQRR');
define('PAYPAL_CLIENT_SECRET',		'EJfc5pfzhwg7LtnDaVD6BRIVlNBi21ubX9b2M_hdZk9_6zAgPgmiQ2wKv87_lwmFo1BdQdkrr-ymw-oq');
/* 汇率查询API */
define('EXRATE_API_URL',		'http://op.juhe.cn/onebox/exchange/query?key=');
define('EXRATE_API_KEY',		'33529bd5e770a820940157919244b242');
