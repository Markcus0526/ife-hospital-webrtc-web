<script type="text/javascript">
disable_alarm = true;

$(function () {

	var $form = $('#form').validate($.extend({
		rules : {
			require_php_ver: {
				required: true
			},
			installed_mysql: {
				required: true
			},
			installed_mbstring: {
				required: true
			},
			installed_simplexml: {
				required: true
			},
			installed_gd: {
				required: true
			},
			db_hostname: {
				required: true
			},
			db_user: {
				required: true
			},
			db_name: {
				required: true
			},
			db_port: {
				required: true,
				digits: true
			},
			mail_from: {
				required: true,
				email: true
			},
			mail_fromname: {
				required: true
			},
			mail_smtp_server: {
				required: true
			},
			mail_smtp_user: {
				required: true
			},
			mail_smtp_password: {
				required: true
			},
			mail_smtp_port: {
				required: true,
				digits: true
			},
			recording_api: {
				required: true
			},
			chinapay_merchantid: {
				required: true
			},
			chinapay_signcert_pwd: {
				required: true
			},
			paypal_email_address: {
				required: true
			},
			paypal_identity_token: {
				required: true
			},
			paypal_cert_id: {
				required: true
			},
			paypal_sandbox_client_id: {
				required: true
			},
			paypal_sandbox_client_secret: {
				required: true
			},
			paypal_client_id: {
				required: true
			},
			paypal_client_secret: {
				required: true
			},			
			interview_pay_limit: {
				required: true,
				min: 1
			},
			interview_alarm_start: {
				required: true,
				min: 1
			},
			interview_offline_limit: {
				required: true,
				min: 1
			},
			interview_reoffline_limit: {
				required: true,
				min: 1
			},
			interview_alarm_offline_limit: {
				required: true,
				min: 1
			},
			interview_no_interp_alarm_limit: {
				required: true,
				min: 1
			},
			interview_patient_reservable_time_after_now: {
				required: true,
				min: 1
			},
			interview_doctor_changable_time_after_now: {
				required: true,
				min: 1
			},
			interview_doctor_settable_time_after_now: {
				required: true,
				min: 1
			},
			save_record_limit: {
				required: true,
				min: 1
			},
		},

		// Messages for form validation
		messages : {
			require_php_ver: {
				required: "<?php l("本系统正常运行在PHP %s以上的环境。", MIN_PHP_VER); ?>"
			},
			installed_mysql: {
				required: "<?php l("为使用数据库，必须需要安装MySQL扩张。");?>"
			},
			installed_mbstring: {
				required: "<?php l("为支持跨语语言，必须需要按章mbstring扩张。");?>"
			},
			installed_simplexml: {
				required: "<?php l("为支持XML，必须需要按章SimpleXML扩张。");?>"
			},
			installed_gd: {
				required: "<?php l("为支持图像处理，必须需要按章gd扩张。");?>"
			},
			db_hostname: {
				required: "<?php l("请输入MySQL服务器地址。");?>"
			},
			db_user: {
				required: "<?php l("请输入MySQL用户名。");?>"
			},
			db_name: {
				required: "<?php l("请输入MySQL数据库名。");?>"
			},
			db_port: {
				required: "<?php l("请输入MySQL数据库端口。");?>",
				digits: "<?php l("请输入数值。");?>"
			},
			mail_from: {
				required: "<?php l("请输入送信元邮件地址。");?>",
				email: "<?php l("不是有效的邮件地址");?>"
			},
			mail_fromname: {
				required: "<?php l("请输入送信元邮件的用户名。");?>"
			},
			mail_smtp_server: {
				required: "<?php l("请输入SMTP服务器地址。");?>"
			},
			mail_smtp_user: {
				required: "<?php l("请输入SMTP用户名。");?>"
			},
			mail_smtp_password: {
				required: "<?php l("请输入SMTP密码。");?>"
			},
			mail_smtp_port: {
				required: "<?php l("请输入SMTP端口。");?>",
				digits: "<?php l("请输入数值。");?>"
			},
			recording_api: {
				required: "<?php l("该项不为空");?>",
			},
			chinapay_signcert_pwd: {
				required: "<?php l("该项不为空");?>",
			},
			paypal_email_address: {
				required: "<?php l("该项不为空");?>",
			},
			paypal_identity_token: {
				required: "<?php l("该项不为空");?>",
			},
			paypal_cert_id: {
				required: "<?php l("该项不为空");?>",
			},
			paypal_sandbox_client_id: {
				required: "<?php l("该项不为空");?>",
			},
			paypal_sandbox_client_secret: {
				required: "<?php l("该项不为空");?>",
			},
			paypal_client_id: {
				required: "<?php l("该项不为空");?>",
			},
			paypal_client_secret: {
				required: "<?php l("该项不为空");?>",
			},
			chinapay_merchantid: {
				required: "<?php l("该项不为空");?>",
			},
			interview_pay_limit: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			interview_alarm_start: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			interview_offline_limit: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			interview_reoffline_limit: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			interview_alarm_offline_limit: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			interview_no_interp_alarm_limit: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			interview_patient_reservable_time_after_now: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			interview_doctor_changable_time_after_now: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			interview_doctor_settable_time_after_now: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
			save_record_limit: {
				required: "<?php l("该项不为空");?>",
				min: "<?php l("请输入0以上的数值");?>"
			},
		}
	}, getValidationRules()));

	$('#form').ajaxForm({
		dataType : 'json',
		success: function(res, statusText, xhr, form) {
			try {
				if (res.err_code == 0)
				{
					alertBox("<?php l("提示"); ?>", "<?php l("成功保存设置"); ?>", function() {
					});
					return;
				}
				else {
					hideMask();
					errorBox("<?php l("错误"); ?>", "<?php l("对不起，在系统设置中发生错误"); ?>");
					$('#step').val(0);
				}
			}
			finally {
			}
		}
	});

	$('#btnstart').click(function() {		
		if ($('#form').valid())
		{
			var ret = confirmBox("<?php l("确认"); ?>", "<?php l("是否确定保存该系统设置"); ?>", function() {
				$('#form').submit();
			});
		}
	});

	$('.btn-testdb').click(function() {
		$.ajax({
			url :"sysman/testdb_ajax",
			type : "post",
			dataType : 'json',
			data : { 
				db_hostname : $('#db_hostname').val(), 
				db_user : $('#db_user').val(), 
				db_password : $('#db_password').val(), 
				db_name : $('#db_name').val()
			},
			success : function(data){
				if (data.err_code == 0)
				{
					alertBox("<?php l("提示"); ?>", "<?php l("数据库连接成功"); ?>");
				}
				else {
					errorBox("<?php l("错误"); ?>", "<?php l("对不起，不能连接数据库，请再确认数据库设置"); ?>");
				}
			},
			error : function() {
			},
			complete : function() {
			}
		});
	});

	$('#mail_smtp_auth').change(function() {
		if ($(this).isChecked())
		{
			$('#group_mail_smtp_server').show();
			$('#group_mail_smtp_user').show();
			$('#group_mail_smtp_password').show();
			$('#group_mail_smtp_port').show();
		}
		else {
			$('#group_mail_smtp_server').hide();
			$('#group_mail_smtp_user').hide();
			$('#group_mail_smtp_password').hide();
			$('#group_mail_smtp_port').hide();
		}
	});

	$('#mail_smtp_auth').change();
});
</script>