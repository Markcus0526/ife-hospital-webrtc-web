<?php
	/************************* Copyright Info ***************************
	*	Project Name:		3QC World Tele Clinic System				*
	*	Framework:			MAL MVC Web Framewrok v1.0					*
	*	Author:				Quan										*
	*	Date:				2017/10/02									*
	*																	*
	*	2017 ©      ALL Rights Reserved. 								*
	************************** Copyright Info **************************/

	// 错误码
	define('ERR_OK',                            '0');
	define('ERR_SQL',                           '1');
	define('ERR_INVALID_PKEY',                  '2');
	define('ERR_NODATA',                        '3');

	define('ERR_FAILLOGIN',                     '4');
	define('ERR_FAILLOGIN_PASSKEY',             '5');
	define('ERR_ALREADYLOGIN',                  '6');
	define("ERR_ALREADY_EXISTS_INTERVIEW_SAME_PATIENT",	'7');
	define("ERR_ALREADY_USING_MOBILE",			'8');

	define('ERR_INVALID_REQUIRED',              '9');

	define('ERR_NOPRIV',                        '10');
	define('ERR_NOT_LOGINED',                   '11');
	define('ERR_FAIL_UPLOAD',                   '12');

	define('ERR_INVALID_IMAGE',                 '13');
	define('ERR_INVALID_PDF',                   '14');
	define('ERR_USER_LOCKED',                   '15');
	define('ERR_USER_UNACTIVATED',              '16');
	define('ERR_DUPLICATE_DATA',             	'18');
	define('ERR_NO_FILE',                   	'19');
	define('ERR_FAIL_SMS',						'20');

	define('ERR_ALREADYINSTALLED',              '23');
	define('ERR_INVALID_PASSKEY',				'24');
	define('ERR_INVALID_PASSWORD',				'25');
	define('ERR_UNREGISTERED_MOBILE',			'26');

	define('ERR_NOTFOUND_PAGE',                 '27');

	define('ERR_INVALID_OLDPWD',                '28');
	define('ERR_NOTFOUND_USER',                 '29');

	define('ERR_INVALID_ACTIVATE_KEY',          '35');
	define('ERR_ACTIVATE_EXPIRED',              '36');
	define('ERR_INVALID_EMAIL',                 '37');

	define('ERR_DELUSER',                       '40');

	define('ERR_CANTCONNECT',                   '41');
	define('ERR_NEWINSERTED',                   '42');

	define('ERR_FAILWRITEFILE',					'50');
	define('ERR_UPLOAD_ZEROFILE',				'51');

	define("ERR_YOU_DUP_INTERVIEW",				'60');
	define("ERR_OTHER_DUP_INTERVIEW",			'61');
	define("ERR_INVALID_DTIME",					'62');
	define("ERR_BEFORE_STARTTIME",				'63');
	define("ERR_PATIENT_DUP_INTERVIEW",			'64');
	define("ERR_YOU_ONLY_ONE_DOCTOR",			'65');
	define("ERR_ALREADY_BID_INTERVIEW",			'66');
	define("ERR_CANCELED_INTERVIEW",			'67');
	define("ERR_FINISHED_INTERVIEW",			'68');
	define("ERR_ALREADY_RESERVED_DTIME",		'69');
	define("ERR_ALREADY_CHANGED_TIME",			'70');

	define("ERR_ALREADY_USING_LCODE",			'71');

	define("ERR_ALREADY_HCOUNTRY",				'75');

	// 支付状态
	define("ERR_REFUND_INVALID",				'80');

	define("ERR_INVITE_SAME",					'81');

	// 安全性错误
	define("ERR_LOCK_PASSWORD",					'90');
	define("ERR_LOCK_PASSKEY",					'91');
	define("ERR_LOCKED",						'92');
	define("ERR_DISABLED_LOGIN",				'93');
	define("ERR_DISABLED_RESET",				'94');

	// interview for SCS server
	define('ERR_CONNECT_FAIL_API',				'1000');
	define('ERR_INVALID_PARAMETER',				'1001');
	define('ERR_ALREADY_LOGIN',					'1002');
	define('ERR_UNKNOWN_COMMAND',				'1003');
	define("ERR_INTERVIEW_NOT_PROGRESS",		'1004');

	// 内部码
	define('CODE_UTYPE',                        0);
	define('CODE_SEX',                          1);
	define('CODE_LOGTYPE',                     	2);
	define('CODE_LOCK',                         4);
	define('CODE_ENABLE',                       5);
	define('CODE_NEED',                       	6);
	define('CODE_YESNO',                      	7);
	define('CODE_ISTATUS',                     	8); // 会诊状态
	define('CODE_RSTATUS',                     	9); // 审核状态
	define('CODE_FSTATUS',                     	10); // 反馈状态
	define('CODE_CCAUSE',                     	11); // 取消原因
	define('CODE_RCAUSE',                     	12); // 拒绝理由

	define('CODE_PRIV_REG_USER',				40);
	define('CODE_PRIV_PATIENTS',				41);
	define('CODE_PRIV_DOCTORS',					42);
	define('CODE_PRIV_INTERPRETERS',			43);
	define('CODE_PRIV_HOSPITALS',				44);
	define('CODE_PRIV_CHISTORY',				45);
	define('CODE_PRIV_RESERVE',					46);
	define('CODE_PRIV_INTERVIEWS',				47);
	define('CODE_PRIV_PROFILE',					48);
	define('CODE_PRIV_STATS',					49);
	define('CODE_PRIV_FEEDBACK',				50);
	define('CODE_PRIV_SYSCHECK',				51);

	// 权限
	define('UTYPE_NONE',                        0);
	define('UTYPE_SUPER',                       1); // 超级管理员
	define('UTYPE_ADMIN',                    	2); // 普通管理员
	define('UTYPE_PATIENT', 		            16); // 患者
	define('UTYPE_DOCTOR',                      32); // 专家
	define('UTYPE_INTERPRETER',                 64); // 翻译人员
	define('UTYPE_LOGINUSER',                   UTYPE_SUPER | UTYPE_ADMIN | UTYPE_PATIENT | UTYPE_DOCTOR | UTYPE_INTERPRETER);

	define('SEX_MAN',                           0); // 男
	define('SEX_WOMAN',                         1); // 女
	
	define('LOCK_OFF',                          0); // 正常
	define('LOCK_ON',                           1); // 已锁定
	define('LOCK_DISABLE',                      2); // 已终止
	define('FAIL_PASSWORD',						0); // 密码登录失败
	define('FAIL_PASSKEY', 						1); // 验证码登录失败

	define('DISABLED',                          0); // 不能
	define('ENABLED',                           1); // 可能

	define('UNNEED', 	                        0); // 不需要
	define('NEED',	                            1); // 需要

	define('YN_NO',								0); // 否
	define('YN_YES',							1); // 是

	define('ONLINE',	                        1); // 上线
	define('OFFLINE',                           0); // 掉线

	// 会诊状态
	define("ISTATUS_NONE",						0); // 待付款
	define("ISTATUS_WAITING",					1); // 待预约
	define("ISTATUS_PAYED",						2); // 已付款
	define("ISTATUS_OPENED",					4); // 已生效
	define("ISTATUS_PROGRESSING",				6); // 进行中
	define("ISTATUS_UNFINISHED",				7); // 未完成
	define("ISTATUS_FINISHED",					8); // 已完成
	define("ISTATUS_CANCELED",					10); // 已失效
	// 会诊部分状态
	define("SISTATUS_NONE",						0);  // 
	define("SISTATUS_D_MUST_ACCEPT_PATIENT",	1);  // 专家是否接受患者
	define("SISTATUS_ALERTED_START_ALARM",		8);  // 会诊前提醒
	define("SISTATUS_CANCEL_BEFORE_PAY",		64); // 支付前取消
	define("SISTATUS_ALERT_NO_INTERP",			128);  // 无翻译接单,短信通知管理员

	// 专家时间状态
	define("TSTATE_DISABLE",					0); // 预约不可能
	define("TSTATE_ENABLE",						1); // 预约可能
	define("TSTATE_RESERVED",					2); // 已被预约
	define("TSTATE_UNRESERVABLE",				3); // 不可预约

	// 审核状态
	define("RSTATUS_NONE",						0); // 待审核
	define("RSTATUS_REJECT",					1); // 审核未通过

	// 反馈状态
	define("FSTATUS_UNREAD",					0); // 未读
	define("FSTATUS_READ",						1); // 已读

	// 取消理由
	define("CCAUSE_NOFREE",						0); // 没空
	define("CCAUSE_OTHER",						100); // 其它

	// 拒绝理由
	define("RCAUSE_OTHER",						100); // 其它

	// 货币单位
	define("CUNIT_RMB",							'rmb');	// 人民币
	define("CUNIT_USD",							'usd');	// 美元

	// 会诊状态历史
	define("IHTYPE_RESERVED",					0); // 预约会诊
	define("IHTYPE_CHANGED_COST",				1); // 会诊费变更为xxx
	define("IHTYPE_EXPIRED_PAY",				2); // 30分钟内未付款
	define("IHTYPE_PAYED",						3); // 银联/PayPal成功支付
	define("IHTYPE_INTERP_ACCEPT",				4); // 翻译接单
	define("IHTYPE_INVITE_INTERP",				5); // 指派翻译
	define("IHTYPE_BROWSED_IHISTORY",			6); // 专家已查看病历
	define("IHTYPE_START_INTERVIEW",			7); // 开始会诊
	define("IHTYPE_FINISHED_INTERVIEW",			8); // 结束会诊
	define("IHTYPE_UPLOAD_PRESCRIPT",			9); // 专家上传第二诊疗意见
	define("IHTYPE_REFUNDED",					10); // 已退款 理由：xxx
	define("IHTYPE_CLOSE",						11); // 订单已关闭 理由：xxx
	define("IHTYPE_CHANGED_TIME",				12); // 专家更改时间 原会诊时间：xxx
	define("IHTYPE_DOCTOR_CANCELED",			13); // 专家取消会诊 理由：xxx
	define("IHTYPE_PATIENT_CANCELED",			14); // 患者取消预约 理由：xxx
	define("IHTYPE_INTERP_CANCELED",			17); // 翻译取消订单 理由：xxx
	define("IHTYPE_ADMIN_CANCELED",				20); // 管理员取消预约 理由：%s
	define("IHTYPE_PATIENT_PAY_CANCELED",		21); // 患者取消预约 理由：xxx 
	define("IHTYPE_NO_INTERPRETER",				22); // 无翻译接单
	define("IHTYPE_UPLOAD_T_CHISTORY",			23); // 翻译上传翻译版病历
	define("IHTYPE_UPLOAD_T_PRESCRIPT",			24); // 翻译上传翻译版第二诊疗意见
	define("IHTYPE_CANCELED_INTERVIEW",			25); // 会诊已失效
	define("IHTYPE_UNFINISHED_INTERVIEW",		26); // 会诊未完成
	define("IHTYPE_FINISHED_INTERP",			27); // 已完成翻译
	define("IHTYPE_ACCEPTED_PATIENT",			28); // 已接受患者
	define("IHTYPE_REJECTED_PATIENT",			29); // 已拒绝患者
	define("IHTYPE_RERESERVED",					30); // 重新预约会诊

	// 评价
	define("IRATE_UNSATISFY", 					0); // 不满
	define("IRATE_SATISFY", 					1); // 满意
	define("IRATE_VERYSATISFY",					2); // 非常满意

	// Controller关联
	define('ACTIONTYPE_HTML',                   0);
	define('ACTIONTYPE_AJAXJSON',               1);
	define('ACTIONTYPE_REFRESH',                2);	

	// 日记输出
	define('LOGTYPE_ACCESS',                    '1'); // 访问
	define('LOGTYPE_OPERATION',                 '2'); // 操作
	define('LOGTYPE_WARNING',                   '4'); // 警告
	define('LOGTYPE_ERROR',                     '8'); // 错误
	define('LOGTYPE_DEBUG',                     '16'); // 调试
		
	// 编辑器
	define("EDITORTYPE_INLINE",					1);
	define("EDITORTYPE_REPLACE",				2);

	// 画像处理
	define("RESIZE_ZOOM", 						0); // Zoom in / out
	define("RESIZE_CROP", 						1); // Crop
	define("RESIZE_CONTAIN", 					2); // After zoom in/out, add margin

	define("PRIV_REG_USER_D",					1);
	define("PRIV_REG_USER_I",					2);
	define("PRIV_DETAIL",						1);
	define("PRIV_ADD",							2);
	define("PRIV_EDIT",							4);
	define("PRIV_DELETE",						8);
	define("PRIV_SET_FEE",						16);
	define("PRIV_RESERVE",						1);
	define("PRIV_INVITE",						1);
	define("PRIV_SET_COST",						2);
	define("PRIV_IHISTORY",						4);
	define("PRIV_CLOSE_INTERVIEW",				8);
	define("PRIV_PLAY",							16);
	define("PRIV_ENTER",						32);
	define("PRIV_REFUND",						64);
	define("PRIV_CHISTORY",						128);
	define("PRIV_PRESC",						256);
	define("PRIV_PROFILE_MAIN",					1);
	define("PRIV_PASSWORD",						2);
	define("PRIV_CHANGE_MOBILE",				4);
	define("PRIV_EXPORT",						2);
	define("PRIV_EXRATE",						4);
	define("PRIV_FEEDBACK",						1);
	define("PRIV_SYSCHECK",						1);

	// global error message
	$g_err_msg = "";
	function _err_msg($err, $param1=null, $param2=null)
	{
		global $g_err_msg, $g_err_msgs;
		$m = "";
		if ($g_err_msg != "")
			$m = $g_err_msg;
		else {
			$m = $g_err_msgs[$err];
			switch($err) {
				case ERR_OK:
					$m=_l('成功'); break;	
				case ERR_SQL:
					$m=_l('发生数据库错误'); break;
				case ERR_INVALID_PKEY:
					$m=_l('是个不有效的主键'); break;
				case ERR_NODATA:
					$m=_l('没有数据'); break;		
																 
				case ERR_FAILLOGIN:
					$m=_l('对不起，您输入的手机号不存在或密码不正确。'); break;
				case ERR_FAILLOGIN_PASSKEY:
					$m=_l('对不起，您输入的手机号不存在或验证码不正确。'); break;
				case ERR_ALREADYLOGIN:
					$m=_l('该账号已在其他手机上登录，不可重复登录。'); break;
				case ERR_ALREADY_EXISTS_INTERVIEW_SAME_PATIENT:
					$m=_l('同一时间段的同一患者的会诊已存在，不能重复预约。'); break;	
				case ERR_ALREADY_USING_MOBILE:
					$m=_l('该手机号已被注册'); break;	
																 
				case ERR_INVALID_REQUIRED:
					$m=_l(''); break;
																 
				case ERR_NOPRIV:
					$m=_l('您没有权限'); break;
				case ERR_NOT_LOGINED:
					$m=_l('您还没有登录'); break;
				case ERR_FAIL_UPLOAD:
					$m=_l('不能上传文件'); break;

				case ERR_INVALID_IMAGE:
					$m=_l('不是图像文件'); break;
				case ERR_INVALID_PDF:
					$m=_l('不是PDF文件'); break;

				case ERR_USER_LOCKED:
					$m=_l('密码输入错误%d次，该账号和密码被锁，请联系客服解锁。', LOGIN_FAIL_LOCK); break;
				case ERR_USER_UNACTIVATED:
					$m=_l('此账号还没审核通过。'); break;
				case ERR_DUPLICATE_DATA:
					$m=_l('此数据已登录。'); break;
				case ERR_NO_FILE:
					$m=_l('没有文件。'); break;
				case ERR_FAIL_SMS:
					$m=_l('发生短信发送错误'); break;
				
				case ERR_ALREADYINSTALLED:
					$m=_l('系统已安装。'); break;
				case ERR_INVALID_PASSKEY:
					$m=_l('请输入正确的短信验证码。'); break;
				case ERR_INVALID_PASSWORD:
					$m=_l('请输入正确的密码。'); break;
				case ERR_UNREGISTERED_MOBILE:
					$m=_l('请输入正确的手机号。'); break;

				case ERR_NOTFOUND_PAGE:
					$m=_l('对不起，该网页不存在。'); break;

				case ERR_INVALID_OLDPWD:
					$m=_l('现在的密码不正确。'); break;
				case ERR_NOTFOUND_USER:
					$m=_l('该用户不存在。'); break;

				case ERR_DELUSER:
					$m=_l('因为此用户有相关的数据，不能删除'); break;

				case ERR_CANTCONNECT:
					$m=_l('不能连接服务器。'); break;

				case ERR_NEWINSERTED:
					$m=_l('请输入账号信息'); break;

				case ERR_INVALID_ACTIVATE_KEY:
					$m=_l('邮件验证码不是有效。'); break;
				case ERR_ACTIVATE_EXPIRED:
					$m=_l('此邮件验证码已过期。'); break;
				case ERR_INVALID_EMAIL:
					$m=_l('邮件地址不正确。'); break;

				case ERR_FAILWRITEFILE:
					$m=_l('您没有文件写出的权限。'); break;

				case ERR_UPLOAD_ZEROFILE:
					$m=_l('不能上传0B的文件。'); break;

				case ERR_YOU_DUP_INTERVIEW:
					$m=_l('当前已有预约，不能重复预约。'); break;
				case ERR_OTHER_DUP_INTERVIEW:
					$m=_l('该会诊时间已被预约，请您重新预约。'); break;
				case ERR_INVALID_DTIME:
					$m=_l('该会诊时间已失效，请您重新预约。'); break;
				case ERR_BEFORE_STARTTIME:
					$m=_l('还未到会诊时间，暂不能进入！'); break;
				case ERR_PATIENT_DUP_INTERVIEW:
					$m=_l('该会诊时间已被患者预约，请您更换其他会诊时间。'); break;
				case ERR_YOU_ONLY_ONE_DOCTOR: 
					$m=_l('同一会诊时间只能预约一位专家。'); break;
				case ERR_ALREADY_BID_INTERVIEW:
					$m=_l('该订单已被其他翻译接单。'); break;
				case ERR_CANCELED_INTERVIEW:
					$m=_l('该订单已被取消。'); break;
				case ERR_FINISHED_INTERVIEW:
					$m=_l('该订单已完成。'); break;
				case ERR_ALREADY_RESERVED_DTIME:
					$m=_l('该会诊时间已被患者预约，您无法执行取消操作。'); break;
				case ERR_ALREADY_CHANGED_TIME:
					$m=_l('该订单的会诊时间被修改，请重新接单。'); break;
				case ERR_ALREADY_USING_LCODE:
					$m=_l('该语言码已被添加。'); break;
				case ERR_ALREADY_HCOUNTRY:
					$m=_l('该国家已被添加。'); break;
				
				case ERR_REFUND_INVALID:
					$m=_l('退款失败了。'); break;
				
				case ERR_INVITE_SAME:
					$m=_l('该翻译已被指派。'); break;

				case ERR_LOCK_PASSWORD:
					$m=_l('由于您密码登录失败次数已达上限，请您使用验证码登录。'); break;
				case ERR_LOCK_PASSKEY:
					$m=_l('由于您验证码登录失败次数已达上限，请您使用密码登录。'); break;
				case ERR_LOCKED:
					$m=_l('由于您登录失败次数已达上限，账户已锁定，请您24小时后再试或使用找回密码功能。'); break;
				case ERR_DISABLED_LOGIN:
				case ERR_DISABLED_RESET:
					$m=_l('由于您已连续%d天登录失败，账户已终止，请您联系管理员去解锁',LOGIN_FAIL_DISABLE); break;

				case ERR_INVALID_PARAMETER:
					$m=_l('对不起，您没有进入该会诊室的权限。'); break;
				case ERR_ALREADY_LOGIN:
					$m=_l('对不起，您在别的地方中已经进入该会诊室。'); break;
				case ERR_UNKNOWN_COMMAND:
					$m=_l('对不起，服务器不能处理您的需求。'); break;
				case ERR_INTERVIEW_NOT_PROGRESS:
					$m=_l('因为该会诊还没开始，您无法进入。'); break;
			}
		}

		return sprintf($m, $param1, $param2);
	}


	function _weekdays() {
		return array(
			_l("星期日"),
			_l("星期一"),
			_l("星期二"),
			_l("星期三"),
			_l("星期四"),
			_l("星期五"),
			_l("星期六")
		);	
	}

	function _code_labels($code)
	{
		global $g_codes;
		if (!isset($g_codes))
			$g_codes = array();

		if (isset($g_codes[$code]))
			return $g_codes[$code];

		switch ($code) {
			case CODE_UTYPE:
				$g_codes[$code] =array(
					UTYPE_NONE => '',
					UTYPE_SUPER => _l('超级管理员'),
					UTYPE_ADMIN => _l('普通管理员'),
					UTYPE_PATIENT => _l('患者'),
					UTYPE_DOCTOR => _l('专家'),
					UTYPE_INTERPRETER => _l('翻译'),
				);
				break;
			case CODE_SEX:
				$g_codes[$code] =array(
					SEX_MAN => _l('男'),
					SEX_WOMAN => _l('女'),
				);
				break;
			case CODE_LOGTYPE:
				$g_codes[$code] =array(
					LOGTYPE_ACCESS => _l('访问'),
					LOGTYPE_OPERATION => _l('操作'),
					LOGTYPE_WARNING => _l('警告'),
					LOGTYPE_ERROR => _l('错误'),
					LOGTYPE_DEBUG => _l('调试'),
				);
				break;
			case CODE_LOCK:
				$g_codes[$code] =array(
					LOCK_OFF => _l('正常'),
					LOCK_ON => _l('已锁定'),
					LOCK_DISABLE => _l('已终止'),
				);
				break;
			case CODE_ENABLE:
				$g_codes[$code] =array(
					ENABLED => _l('可能'),
					DISABLED => _l('不能'),
				);
				break;
			case CODE_NEED:
				$g_codes[$code] =array(
					UNNEED => _l('不需要'),
					NEED => _l('需要'),
				);
				break;
			case CODE_YESNO:
				$g_codes[$code] =array(
					YN_YES => _l('是'),
					YN_NO => _l('否'),
				);
				break;
			case CODE_ISTATUS:
				$g_codes[$code] =array(
					ISTATUS_NONE => _l('待付款'),
					ISTATUS_WAITING => _l('待预约'),
					ISTATUS_PAYED => _l('已付款'),
					ISTATUS_OPENED => _l('已生效'),
					ISTATUS_PROGRESSING => _l('进行中'),
					ISTATUS_UNFINISHED => _l('未完成'),
					ISTATUS_FINISHED =>_l( '已完成'),
					ISTATUS_CANCELED => _l('已失效')
				);
				break;
			case CODE_RSTATUS:
				$g_codes[$code] =array(
					RSTATUS_NONE => _l('待审核'),
					RSTATUS_REJECT => _l('审核未通过'),
				);
				break;
			case CODE_FSTATUS:
				$g_codes[$code] =array(
					FSTATUS_UNREAD => _l('未读'),
					FSTATUS_READ => _l('已读'),
				);
				break;
			case CODE_CCAUSE:
				$g_codes[$code] =array(
					CCAUSE_OTHER => _l('其它'),
				);
			case CODE_RCAUSE:
				$g_codes[$code] =array(
					RCAUSE_OTHER => _l('其它'),
				);
				break;
			case CODE_PRIV_REG_USER:
				$g_codes[$code] =array(
					PRIV_REG_USER_D => _l('审核注册专家'),
					PRIV_REG_USER_I => _l('审核注册翻译'),
				);
				break;
			case CODE_PRIV_PATIENTS:
				$g_codes[$code] =array(
					PRIV_DETAIL => _l('查看'),
					PRIV_ADD => _l('添加'),
					PRIV_EDIT => _l('修改'),
					PRIV_DELETE => _l('删除'),
				);
				break;
			case CODE_PRIV_DOCTORS:
				$g_codes[$code] =array(
					PRIV_DETAIL => _l('查看'),
					PRIV_ADD => _l('添加'),
					PRIV_EDIT => _l('修改'),
					PRIV_DELETE => _l('删除'),
					PRIV_SET_FEE => _l('设置会诊费'),
				);
				break;
			case CODE_PRIV_INTERPRETERS:
				$g_codes[$code] =array(
					PRIV_DETAIL => _l('查看'),
					PRIV_ADD => _l('添加'),
					PRIV_EDIT => _l('修改'),
					PRIV_DELETE => _l('删除'),
				);
				break;
			case CODE_PRIV_HOSPITALS:
				$g_codes[$code] =array(
					PRIV_ADD => _l('添加'),
					PRIV_EDIT => _l('修改'),
					PRIV_DELETE => _l('删除'),
				);
				break;
			case CODE_PRIV_CHISTORY:
				$g_codes[$code] =array(
					PRIV_DETAIL => _l('查看'),
					PRIV_ADD => _l('添加'),
					PRIV_EDIT => _l('修改'),
					PRIV_DELETE => _l('删除'),
				);
				break;
			case CODE_PRIV_RESERVE:
				$g_codes[$code] =array(
					PRIV_RESERVE => _l('预约会诊'),
				);
				break;
			case CODE_PRIV_INTERVIEWS:
				$g_codes[$code] =array(
					PRIV_INVITE => _l('指派'),
					PRIV_SET_COST => _l('修改会诊费'),
					PRIV_IHISTORY => _l('查看订单详情'),
					PRIV_CLOSE_INTERVIEW => _l('关闭订单'),
					PRIV_PLAY => _l('回放'),
					PRIV_ENTER => _l('进入会诊室'),
					PRIV_REFUND => _l('退款'),
					PRIV_CHISTORY => _l('查看和下载病历'),
					PRIV_PRESC => _l('查看和下载第二诊疗意见'),
				);
				break;
			case CODE_PRIV_PROFILE:
				$g_codes[$code] =array(
					PRIV_PROFILE_MAIN => _l('基本信息'),
					PRIV_PASSWORD => _l('更改密码'),
					PRIV_CHANGE_MOBILE => _l('更改手机'),
				);
				break;
			case CODE_PRIV_STATS:
				$g_codes[$code] =array(
					PRIV_DETAIL => _l('查看'),
					PRIV_EXPORT => _l('导出'),
					PRIV_EXRATE => _l('查询汇率'),
				);
				break;
			case CODE_PRIV_FEEDBACK:
				$g_codes[$code] =array(
					PRIV_FEEDBACK => _l('用户反馈')
				);
				break;
			case CODE_PRIV_SYSCHECK:
				$g_codes[$code] =array(
					PRIV_SYSCHECK => _l('检测设备')
				);
				break;
			default;
				return array();
		}

		return $g_codes[$code];
	}

	function _code_label($code, $val) 
	{
		$codes = _code_labels($code);
		if (isset($codes[$val]))
			return $codes[$val];
		return null;
	}
	
	/*************************** The END ********************************
	*   Don't add new code below this line, thanks.						*
	*	2017 ©      ALL Rights Reserved. 								*
	**************************** The END *******************************/