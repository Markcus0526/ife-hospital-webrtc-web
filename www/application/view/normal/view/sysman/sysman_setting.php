<section>
	<div class="page-bar">
		<ul class="page-breadcrumb">
			<li>
				<span><?php l("当前位置"); ?> :</span>
			</li>
			<li>
				<?php l("系统管理");?>
				<i class="fa fa-angle-right"></i>
			</li>
			<li>
				<?php l("系统设置");?>
			</li>
		</ul>
	</div>

	<form id="form" action="api/sysman/save_setting" class="form-horizontal" method="post">
		<div class="form-body">
			<div class="row">
				<div class="col-md-6">
					<h3>1.<?php l("数据库设置");?></h3>
					<div class="form-group">
						<label class="control-label col-md-4" for="db_hostname">MySQL<?php l("服务器地址");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("db_hostname", array("placeholder" => "例: localhost, 192.168.224.55")); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="db_user"><?php l("用户名");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("db_user", array("placeholder" =>  _l("必须需要数据库创建权限"))); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="db_password"><?php l("密码");?></label>
						<div class="col-md-8">
							<?php $mConfig->password("db_password"); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="db_name"><?php l("数据库名");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("db_name", array("placeholder" => "例: teleclinic")); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="db_name"><?php l("端口");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("db_port", array("placeholder" => "例: 3306")); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-offset-4 col-md-8">
							<button type="button" class="btn btn-testdb btn-xs"><i class="fa fa-warning"></i> <?php l("连接检测");?></button>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h3>2. <?php l("邮件设置");?></h3>
					<div class="form-group">
						<label class="control-label col-md-4" for="mail_from"><?php l("发件人邮箱");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("mail_from", array("placeholder" => "例: yang@163.com")); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="mail_fromname"><?php l("发件人名称");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("mail_fromname"); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="mail_smtp_auth">SMTP<?php l("验证");?></label>
						<div class="col-md-8">
							<?php $mConfig->checkbox_single("mail_smtp_auth", _l("使用SMTP验证")); ?>
						</div>
					</div>
					<div class="form-group" id="group_mail_smtp_use_ssl">
						<label class="control-label col-md-4" for="mail_smtp_use_ssl">SMTP SSL<?php l("验证");?></label>
						<div class="col-md-8">
							<?php $mConfig->checkbox_single("mail_smtp_use_ssl", _l("使用SSL验证")); ?>
						</div>
					</div>
					<div class="form-group" id="group_mail_smtp_server">
						<label class="control-label col-md-4" for="mail_smtp_server">SMTP<?php l("服务器地址");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("mail_smtp_server", array("placeholder" => "例: mail.163.com")); ?>
						</div>
					</div>
					<div class="form-group" id="group_mail_smtp_user">
						<label class="control-label col-md-4" for="mail_smtp_user">SMTP<?php l("帐号");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("mail_smtp_user", array("placeholder" => "例: yang")); ?>
						</div>
					</div>
					<div class="form-group" id="group_mail_smtp_password">
						<label class="control-label col-md-4" for="mail_smtp_password">SMTP<?php l("密码");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->password("mail_smtp_password"); ?>
						</div>
					</div>
					<div class="form-group" id="group_mail_smtp_port">
						<label class="control-label col-md-4" for="mail_smtp_port">SMTP<?php l("端口");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("mail_smtp_port", array("placeholder" => "例: 25")); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<h3>3.<?php l("录像设置");?></h3>
					<div class="form-group">
						<label class="control-label col-md-4" for="recording_api"><?php l("录像服务器URL");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("recording_api", array("placeholder" => "例: https://teleclinic.record.com/")); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<h3>4.<?php l("在线支付设置");?></h3>
					<h4>- <?php l("银联在线支付");?></h4>
					<div class="form-group">
						<label class="control-label col-md-4" for="chinapay_merchantid"><?php l("商家号");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("chinapay_merchantid"); ?>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("签名证书路径");?></label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mChinapayConfig->signCertPath); ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="chinapay_signcert_pwd"><?php l("签名证书密码");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->password("chinapay_signcert_pwd"); ?>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("加密证书路径");?></label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mChinapayConfig->encryptCertPath); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("验签中级证书路径");?></label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mChinapayConfig->middleCertPath); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("验签根证书路径");?></label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mChinapayConfig->rootCertPath); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4"><?php l("日志打印路径");?></label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mChinapayConfig->logFilePath); ?></p>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h3>&nbsp;</h3>
					<h4>- <?php l("PayPal在线支付");?></h4>
					<div class="form-group">
						<label class="control-label col-md-4" for="paypal_email_address"><?php l("PayPal账号（邮件地址）");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("paypal_email_address"); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="paypal_identity_token"><?php l("Identity Token");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("paypal_identity_token"); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="paypal_cert_id"><?php l("Cert ID");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("paypal_cert_id"); ?>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4" for="paypal_ewp_private_key_pwd"><?php l("Certificate PKCS12 (.p12)");?> </label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mPaypal->ewpCertPath); ?></p>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4" for="paypal_ewp_private_key_pwd"><?php l("Private Key");?> </label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mPaypal->ewpPrivateKeyPath); ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="paypal_ewp_private_key_pwd"><?php l("Private Key密码");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->password("paypal_ewp_private_key_pwd"); ?>
						</div>
					</div>
					<div class="form-group static">
						<label class="control-label col-md-4" for="paypal_ewp_private_key_pwd"><?php l("PayPal Certificate");?> </label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mPaypal->paypalCertPath); ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="paypal_sandbox_client_id"><?php l("Sandbox Client ID");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("paypal_sandbox_client_id"); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="paypal_sandbox_client_secret"><?php l("Sandbox Client Secret");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("paypal_sandbox_client_secret"); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="paypal_client_id"><?php l("Client ID");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("paypal_client_id"); ?>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="paypal_client_secret"><?php l("Client Secret");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("paypal_client_secret"); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<h3>5.<?php l("汇率查询");?></h3>
					<div class="form-group">
						<label class="control-label col-md-4" for="exrate_api_url"><?php l("汇率查询API的URL");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<p class="form-control-static"><?php p($mConfig->exrate_api_url); ?></p>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h3>&nbsp;</h3>
					<div class="form-group">
						<label class="control-label col-md-4" for="exrate_api_key"><?php l("应用APPKEY");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php $mConfig->input("exrate_api_key"); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<h3>6.<?php l("会诊相关设置");?></h3>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_pay_limit"><?php l("会诊支付有效期限");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<?php $mConfig->input_number("interview_pay_limit", array("placeholder" => "例: 30")); ?>
								<span class="input-group-addon">
									<?php l("分钟"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_alarm_start"><?php l("会诊前提醒");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<?php $mConfig->input_number("interview_alarm_start", array("placeholder" => "例: 30")); ?>
								<span class="input-group-addon">
									<?php l("分钟"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_offline_limit"><?php l("自动结束会诊");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">
									<?php l("专家没在会诊开始"); ?>
								</span>
								<?php $mConfig->input_number("interview_offline_limit", array("placeholder" => "例: 15")); ?>
								<span class="input-group-addon">
									<?php l("分钟内上线"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_reoffline_limit"><?php l("自动结束会诊");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">
									<?php l("专家离线后没在"); ?>
								</span>
								<?php $mConfig->input_number("interview_reoffline_limit", array("placeholder" => "例: 5")); ?>
								<span class="input-group-addon">
									<?php l("分钟内上线"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_alarm_offline_limit"><?php l("温馨提示");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">
									<?php l("专家在会诊开始"); ?>
								</span>
								<?php $mConfig->input_number("interview_alarm_offline_limit", array("placeholder" => "例: 5")); ?>
								<span class="input-group-addon">
									<?php l("分钟内仍没上线"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_no_interp_alarm_limit"><?php l("短信通知管理员");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">
									<?php l("会诊前"); ?>
								</span>
								<?php $mConfig->input_number("interview_no_interp_alarm_limit", array("placeholder" => "例: 24")); ?>
								<span class="input-group-addon">
									<?php l("小时仍没有翻译接单"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_patient_reservable_time_after_now"><?php l("患者可以预约");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">
									<?php l("距当前时间"); ?>
								</span>
								<?php $mConfig->input_number("interview_patient_reservable_time_after_now", array("placeholder" => "例: 24")); ?>
								<span class="input-group-addon">
									<?php l("小时后的会诊时间"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_doctor_changable_time_after_now"><?php l("专家可以更改");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">
									<?php l("至距当前时间"); ?>
								</span>
								<?php $mConfig->input_number("interview_doctor_changable_time_after_now", array("placeholder" => "例: 24")); ?>
								<span class="input-group-addon">
									<?php l("小时后的会诊时间"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="interview_doctor_settable_time_after_now"><?php l("专家可以设置");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon">
									<?php l("距当前时间"); ?>
								</span>
								<?php $mConfig->input_number("interview_doctor_settable_time_after_now", array("placeholder" => "例: 24")); ?>
								<span class="input-group-addon">
									<?php l("小时后的会诊时间"); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-4" for="save_record_limit"><?php l("会诊视频存储时间");?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<?php $mSettings->input_number("save_record_limit", array("placeholder" => "例: 24")); ?>
								<span class="input-group-addon">
									<?php l("天"); ?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-actions text-right">
			<button type="button" class="btn btn-primary" id="btnstart"><i class="fa fa-check"></i> <?php l("保存"); ?></button>
		</div>
	</form>
</section>