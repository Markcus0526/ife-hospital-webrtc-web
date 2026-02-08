<!DOCTYPE html>
<html lang="<?php p(_lang()); ?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="<?php l(PRODUCT_NAME); ?>">
		<meta name="author" content="">

		<base href="<?php p(SITE_BASEURL);?>">

		<link rel="shortcut icon" href="ico/favicon.png">
		<link rel="icon" href="ico/favicon.ico" type="image/x-icon">

		<title><?php l(PRODUCT_NAME); ?></title>

		<link href="css/main.css" rel="stylesheet">
		<link href="css/layout.css" rel="stylesheet">

		<link href="css/custom_<?php p(_lang()); ?>.css" rel="stylesheet">

		<?php $this->include_css(); ?>

		<!-- Just for debugging purposes. Don't actually copy this line! -->
		<!--[if lt IE 9]><script src="js/ie8-responsive-file-warning.js"></script><![endif]-->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
		<![endif]-->
	</head>

	<body class="login register">
		<div class="container">
			<!-- BEGIN LOGIN FORM -->
			<div class="register-form">
				<div class="login-link">
					<?php l("已有一个账号"); ?><a href="login"><?php l("登录"); ?></a>
				</div>
				<h3 class="form-title"><?php l("MARKCUS国际医疗视频会诊");?></h3>

				<ul class="nav nav-tabs nav-justified">
					<li class="active">
						<a href="#tab_register_patient" data-toggle="tab">
							<?php l("患者"); ?>
						</a>
					</li>
					<li>
						<a href="#tab_register_doctor" data-toggle="tab">
							<?php l("专家"); ?>
						</a>
					</li>
					<li>
						<a href="#tab_register_interpreter" data-toggle="tab">
							<?php l("翻译"); ?>
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab_register_patient">
						<?php $this->mReguser->id_prefix = "patient_"; ?>
						<form id="patient_form" class="form-horizontal" action="api/register/save" method="post">
							<input type="hidden" name="user_type" value="<?php p(UTYPE_PATIENT); ?>">
							<div class="form-group">
								<label class="control-label col-md-3" for="patient_user_name"><?php l("真实姓名"); ?> :</label>
								<div class="col-md-9">
			                   		<?php $this->mReguser->input("user_name", array("placeholder" => _l("真实姓名"), "required" => "required", "maxlength" => 50)); ?>
			                   	</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="patient_sex"><?php l("性&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;别"); ?> :</label>
								<div class="col-md-9">
			                   		<?php $this->mReguser->radio("sex", CODE_SEX); ?>
			                   	</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="patient_password"><?php l("密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->password("password", array("placeholder" => _l("密码"), "required" => "required", "autocomplete" => "off", "maxlength" => 50)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="patient_mobile"><?php l("手机号码"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->tel("mobile", true, array("placeholder" => _l("手机号"), "required" => "required", "maxlength" => 25)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="patient_passkey"><?php l("验&nbsp;&nbsp;证&nbsp;&nbsp;码"); ?> :</label>
								<div class="col-md-9">
									<div class="input-group">
					                    <?php $this->mReguser->input("passkey", array("placeholder" => _l("验证码"), "required" => "required", "autocomplete" => "off", "maxlength" => 10)); ?>
					                    <span class="input-group-btn">
											<button mobile="mobile" class="btn btn-primary send-passkey" type="button"><?php l("点击获取"); ?><span></span></button>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="patient_email"><?php l("电子邮箱"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->input_email("email", array("placeholder" => _l("电子邮箱"), "required" => "required", "maxlength" => 80)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="patient_home_address"><?php l("家庭住址"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->input("home_address", array("placeholder" => _l("家庭住址"), "required" => "required", "maxlength" => 255)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="patient_passports"><?php l("身份证件"); ?> :</label>
								<div class="col-md-9">
									<ul class="attach-list margin-bottom-0" id="ul_patient_passports">
		                            </ul>
									<a href="common/upload/patient_passports" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
										 <i class="mdi-content-add"></i>
									</a>
									<?php $this->mReguser->input("passports", array("class" => "input-null")); ?>
								</div>
							</div>

							<div class="form-actions">
								<button type="submit" class="btn btn-primary btn-lg btn-block"><?php l("注 册"); ?></button>
							</div>
						</form>
					</div>
					<div class="tab-pane" id="tab_register_doctor">
						<?php $this->mReguser->id_prefix = "doctor_"; ?>
						<form id="doctor_form" class="form-horizontal" action="api/register/save" method="post">
							<input type="hidden" name="user_type" value="<?php p(UTYPE_DOCTOR); ?>">

							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_user_name"><?php l("真实姓名"); ?> :</label>
								<div class="col-md-9">
			                   		<?php $this->mReguser->input("user_name", array("placeholder" => _l("真实姓名"), "required" => "required", "maxlength" => 50)); ?>
			                   	</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_password"><?php l("密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->password("password", array("placeholder" => _l("密码"), "required" => "required", "autocomplete" => "off", "maxlength" => 50)); ?>
			                   	</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_mobile"><?php l("手机号码"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->tel("mobile", true, array("placeholder" => _l("手机号"), "required" => "required", "maxlength" => 25)); ?>
			                   	</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_passkey"><?php l("验&nbsp;&nbsp;证&nbsp;&nbsp;码"); ?> :</label>
								<div class="col-md-9">
									<div class="input-group">
					                    <?php $this->mReguser->input("passkey", array("placeholder" => _l("验证码"), "required" => "required", "autocomplete" => "off", "maxlength" => 10)); ?>
					                    <span class="input-group-btn">
											<button mobile="mobile" class="btn btn-primary send-passkey" type="button"><?php l("点击获取"); ?><span></span></button>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_email"><?php l("电子邮箱"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->input_email("email", array("placeholder" => _l("电子邮箱"), "required" => "required", "maxlength" => 80)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_languages"><?php l("精通语言"); ?> :</label>
								<div class="col-md-9">
									<?php $this->mReguser->select_language("languages", _l("精通语言"), false, array("required" => "required")); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_diseases"><?php l("疾病专长"); ?> :</label>
								<div class="col-md-9">
									<?php $this->mReguser->select_disease("diseases", _l("疾病专长")); ?>
								</div>
								<div class="control-label-other">
									<?php l("(可多选)"); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_d_title"><?php l("职&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;称"); ?> :</label>
								<div class="col-md-9">
									<?php $this->mReguser->textarea("d_title", 4, array("placeholder" => _l("请输入您的职称。"), "required" => "required")); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_d_depart"><?php l("所属科室"); ?> :</label>
								<div class="col-md-9">
									<?php $this->mReguser->input("d_depart", array("placeholder" => _l("所属科室"), "required" => "required", "maxlength" => 255)); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_hospitals"><?php l("所属医院"); ?> :</label>
								<div class="col-md-9">
									<?php $this->mReguser->select_hospital("hospitals", null, true, array("style" => "opacity:0")); ?>
								</div>
								<div class="control-label-other">
									<?php l("(可多选)"); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_introduction"><?php l("简&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;介"); ?> :</label>
								<div class="col-md-9">
									<?php $this->mReguser->textarea("introduction", 4, array("placeholder" => _l("请输入您的简介。"))); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="doctor_diplomas"><?php l("资格证书"); ?> :<br/><?php l("（或简历）");?></label>
								<div class="col-md-9">
									<ul class="attach-list margin-bottom-0" id="ul_doctor_diplomas">
		                            </ul>
									<a href="common/upload/doctor_diplomas" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
										 <i class="mdi-content-add"></i>
									</a>
									<?php $this->mReguser->input("diplomas", array("class" => "input-null")); ?>
								</div>
							</div>

							<div class="form-actions">
								<button type="submit" class="btn btn-primary btn-lg btn-block"><?php l("注 册"); ?></button>
							</div>
						</form>
					</div>
					<div class="tab-pane" id="tab_register_interpreter">
						<?php $this->mReguser->id_prefix = "interpreter_"; ?>
						<form id="interpreter_form" class="form-horizontal" action="api/register/save" method="post">
							<input type="hidden" name="user_type" value="<?php p(UTYPE_INTERPRETER); ?>">

							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_user_name"><?php l("真实姓名"); ?> :</label>
								<div class="col-md-9">
			                   		<?php $this->mReguser->input("user_name", array("placeholder" => _l("真实姓名"), "required" => "required", "maxlength" => 50)); ?>
			                   	</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_sex"><?php l("性&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;别"); ?> :</label>
								<div class="col-md-9">
			                   		<?php $this->mReguser->radio("sex", CODE_SEX); ?>
			                   	</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_password"><?php l("密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->password("password", array("placeholder" => _l("密码"), "required" => "required", "autocomplete" => "off", "maxlength" => 50)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_mobile"><?php l("手机号码"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->tel("mobile", true, array("placeholder" => _l("手机号"), "required" => "required", "maxlength" => 25)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_passkey"><?php l("验&nbsp;&nbsp;证&nbsp;&nbsp;码"); ?> :</label>
								<div class="col-md-9">
									<div class="input-group">
					                    <?php $this->mReguser->input("passkey", array("placeholder" => _l("验证码"), "required" => "required", "autocomplete" => "off", "maxlength" => 10)); ?>
					                    <span class="input-group-btn">
											<button mobile="mobile" class="btn btn-primary send-passkey" type="button"><?php l("点击获取"); ?><span></span></button>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_email"><?php l("电子邮箱"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->input_email("email", array("placeholder" => _l("电子邮箱"), "required" => "required", "maxlength" => 80)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_i_age"><?php l("译&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;龄"); ?> :</label>
								<div class="col-md-9">
			                   		<?php $this->mReguser->input_number("i_age", array("placeholder" => _l("译龄"), "required" => "required", "max" => "90", "class" => "text-left")); ?>
			                   	</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_languages"><?php l("精通语言"); ?> :</label>
								<div class="col-md-9">
									<?php $this->mReguser->select_language("languages", _l("精通语言")); ?>
								</div>
								<div class="control-label-other">
									<?php l("(可多选)"); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_home_address"><?php l("家庭住址"); ?> :</label>
								<div class="col-md-9">
			                    	<?php $this->mReguser->input("home_address", array("placeholder" => _l("家庭住址"), "required" => "required", "maxlength" => 255)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_introduction"><?php l("简&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;介"); ?> :</label>
								<div class="col-md-9">
									<?php $this->mReguser->textarea("introduction", 5, array("placeholder" => _l("请输入您的简介。"))); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_diplomas"><?php l("资格证书"); ?> :</label>
								<div class="col-md-9">
									<ul class="attach-list margin-bottom-0" id="ul_interpreter_diplomas">
		                            </ul>
									<a href="common/upload/interpreter_diplomas" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
										 <i class="mdi-content-add"></i>
									</a>
									<?php $this->mReguser->input("diplomas", array("class" => "input-null")); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-3" for="interpreter_passports"><?php l("身份证件"); ?> :</label>
								<div class="col-md-9">
									<ul class="attach-list margin-bottom-0" id="ul_interpreter_passports">
		                            </ul>
									<a href="common/upload/interpreter_passports" class="btn-upload fancybox file-upload" fancy-width=600 fancy-height=480>
										 <i class="mdi-content-add"></i>
									</a>
									<?php $this->mReguser->input("passports", array("class" => "input-null")); ?>
								</div>
							</div>

							<div class="form-actions">
								<button type="submit" class="btn btn-primary btn-lg btn-block"><?php l("注 册"); ?></button>
							</div>
						</form>
					</div>
				</div>

				<div class="row other-links">
					<div class="col-xs-12">
						<?php l("注册即代表同意"); ?><a href="javascript:;" id="contract_link" class="active"><?php l("《注册协议》"); ?></a>
					</div>
				</div>
			</div>
			<!-- END LOGIN FORM -->
		</div>
		<!-- END LOGIN -->

		<div id="contract_view" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?php l("《注册协议》"); ?></h4>
					</div>
					<div class="modal-body contract-container">
						<?php $this->mReguser->detail_html("contract"); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-close-cancel-form" data-dismiss="modal"><?php l("确认"); ?></button>
					</div>
				</div>
			</div>
		</div>

        <script src="js/vendor.min.js?<?php p(VERSION); ?>" type="text/javascript"></script>
        <script src="js/bootstrap-datepicker/js/locales/bootstrap-datepicker.<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>
		<script src="js/select2/select2_locale_<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>

		<script src="<?php p(_js_url("js/app"));?>" type="text/javascript"></script>

		<?php $this->include_js(); ?>

		<script src="js/lang.<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>
		<script src="<?php p(_js_url("js/utility"));?>" type="text/javascript"></script>

		<?php $this->include_viewjs(); ?>

		<script type="text/javascript">
			function onUploadComplete(files, upload_id, upload_type)
			{
				attaches = $('#' + upload_id).val();
				if (attaches != "") attaches += ";";
				$('#' + upload_id).val(attaches + files);

				refreshAttaches(upload_id);
			}

			function refreshAttaches(upload_id)
			{
				var lks = "";
				var attaches = $('#' + upload_id).val();
				if (attaches != "" && attaches != undefined)
				{
					var as = attaches.split(';');
					for (i = 0; i < as.length; i ++)
					{
						var fs = as[i].split(':');
						lks += "<li>";
						lks += downLink(fs[0], fs[1], fs[2], "<?php l("下载"); ?>");
						lks += "<a href='javascript:;' class='remove-attach' no=" + i + " upload_id=" + upload_id + "><i class='mdi-content-remove-circle'></i></a>";
						lks += "</li>";
					}
					$('#ul_' + upload_id).html(lks);

					if (as.length >= 3)
						$('#ul_' + upload_id + " ~ .file-upload").hide();
					else 
						$('#ul_' + upload_id + " ~ .file-upload").show();

					$('.remove-attach').unbind('click').bind('click', 
						function() {
							var no = $(this).attr('no');
							var upload_id = $(this).attr('upload_id');
							var attaches = $('#' + upload_id).val();
							var new_attaches = "";
							if (attaches != "")
							{
								var as = attaches.split(';');
								for (i = 0; i < as.length; i ++)
								{
									if (i != no)
									{
										if (new_attaches != "")
											new_attaches += ";";
										new_attaches += as[i];
									}
								}
							}
							$('#' + upload_id).val(new_attaches);
							refreshAttaches(upload_id);
						});
				}
				else {
					$('#ul_' + upload_id).html(lks);
				}
			}

			$(function() {
				App.init("<?php p(HOME_BASE); ?>", "<?php p(_lang()); ?>"); 
				App.initQuickSidebar(); 

				// patient
				$('#patient_form').validate($.extend({
					rules : {
						user_name: {
							required: true,
							real_name: true
						},
						sex: {
							required: true
						},
						password: {
							required: true,
							pwd_min_length: <?php p(PASSWORD_MIN_LENGTH); ?>
							<?php if (PASSWORD_STRENGTH == 1) { ?>
							,pwd_strength: true
							<?php } ?>
						},
						mobile: {
							required: true,
							mobile: true
						},
						passkey: {
							passkey: true,
							required: true
						},
						email: {
							required: true,
							email: true
						},
						home_address: {
							required: true
						},
						passports: {
							required: true
						}
					},

					// Messages for form validation
					messages : {
						user_name: {
							required: '<?php l('请输入姓名。');?>',
							real_name: '<?php l('姓名由任意长度的汉字、字母、符号组成。');?>'
						},
						sex: {
							required: '<?php l('请选择性别。');?>'
						},
						password: {
							required: '<?php l('请输入密码。');?>',
							<?php if (PASSWORD_STRENGTH == 1) { ?>
							pwd_min_length: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>',
							pwd_strength: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>'
							<?php } else { ?>
							pwd_min_length: '<?php l("密码的最小大小是" . PASSWORD_MIN_LENGTH . "个字符。");?>'
							<?php } ?>
						},
						mobile: {
							required: '<?php l('请输入手机号码。');?>',
							mobile: '<?php l('请输入正确的手机号码。');?>'
						},
						passkey: {
							passkey:  function() {
								if ($('#patient_passkey').prop('valid_passkey') === null)
									return '';
								return '<?php l('请输入正确的验证码。');?>'
							},
							required: '<?php l('请输入验证码。');?>'
						},
						email: {
							required: '<?php l('请输入电子邮箱。');?>',
							email: '<?php l('请输入正确的电子邮箱。');?>'
						},
						home_address: {
							required: '<?php l('请输入家庭住址。');?>'
						},
						passports: {
							required: '<?php l('请上传身份证件。');?>'
						}
					}
				}, getValidationRules()));

				$('#patient_form').ajaxForm({
					dataType : 'json',
					beforeSubmit: function() {
						$('[type="submit"]').prop('disabled', true);
					},
					success: function(res, statusText, xhr, form) {
						try {
							if (res.err_code == 0)
							{
								alertBox("<?php l('提示');?>", "<?php l('用户注册成功。');?>", function() {
										goto_url("login");
									}, 3500);
								return;
							}
							else {
								$('[type="submit"]').prop('disabled', false);
								errorBox("<?php l('错误发生');?>", res.err_msg);
							}
						}
						finally {
						}
					}
				});	

				// doctor
				$('#doctor_form').validate($.extend({
					rules : {
						user_name: {
							required: true,
							real_name: true
						},
						d_title: {
							required: true
						},
						d_depart: {
							required: true
						},
						'hospitals[]': {
							required: true
						},
						'languages': {
							required: true
						},
						'diseases[]': {
							required: true
						},
						introduction: {
							required: true
						},
						password: {
							required: true,
							pwd_min_length: <?php p(PASSWORD_MIN_LENGTH); ?>
							<?php if (PASSWORD_STRENGTH == 1) { ?>
							,pwd_strength: true
							<?php } ?>
						},
						mobile: {
							required: true,
							mobile: true
						},
						passkey: {
							passkey: true,
							required: true
						},
						diplomas: {
							required: true
						},
						email: {
							required: true,
							email: true
						}
					},

					// Messages for form validation
					messages : {
						user_name: {
							required: '<?php l('请输入姓名。');?>',
							real_name: '<?php l('姓名由任意长度的汉字、字母、符号组成。');?>'
						},
						d_title: {
							required: '<?php l('请输入职称。');?>'
						},
						d_depart: {
							required: '<?php l('请输入所属科室。');?>'
						},
						'hospitals[]': {
							required: '<?php l('请选择所属医院。');?>'
						},
						'languages': {
							required: '<?php l('请选择精通语言。');?>'
						},
						'diseases[]': {
							required: '<?php l('请选择疾病专长。');?>'
						},
						introduction: {
							required: '<?php l('请输入您的简介。');?>'
						},
						password: {
							required: '<?php l('请输入密码。');?>',
							<?php if (PASSWORD_STRENGTH == 1) { ?>
							pwd_min_length: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>',
							pwd_strength: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>'
							<?php } else { ?>
							pwd_min_length: '<?php l("密码的最小大小是" . PASSWORD_MIN_LENGTH . "个字符。");?>'
							<?php } ?>
						},
						mobile: {
							required: '<?php l('请输入手机号码。');?>',
							mobile: '<?php l('请输入正确的手机号码。');?>'
						},
						passkey: {
							passkey:  function() {
								if ($('#doctor_passkey').prop('valid_passkey') === null)
									return '';
								return '<?php l('请输入正确的验证码。');?>'
							},
							required: '<?php l('请输入验证码。');?>'
						},
						diplomas: {
							required: '<?php l('请上传资格证书。');?>'
						},
						email: {
							required: '<?php l('请输入电子邮箱。');?>',
							email: '<?php l('请输入正确的电子邮箱。');?>'
						}
					}
				}, getValidationRules()));

				$('#doctor_form').ajaxForm({
					dataType : 'json',
					beforeSubmit: function() {
						$('[type="submit"]').prop('disabled', true);
					},
					success: function(res, statusText, xhr, form) {
						try {
							if (res.err_code == 0)
							{
								alertBox("<?php l('提示');?>", "<?php l('用户注册成功，但您的账户还没激活，请耐心等待管理员的审核。');?>", function() {
										goto_url("login");
									}, 3500);
								return;
							}
							else {
								$('[type="submit"]').prop('disabled', false);
								errorBox("<?php l('错误发生');?>", res.err_msg);
							}
						}
						finally {
						}
					}
				});	

				// interpreter
				$('#interpreter_form').validate($.extend({
					rules : {
						user_name: {
							required: true,
							real_name: true
						},
						i_age: {
							required: true
						},
						'languages[]': {
							required: true
						},
						introduction: {
							required: true
						},
						sex: {
							required: true
						},
						password: {
							required: true,
							pwd_min_length: <?php p(PASSWORD_MIN_LENGTH); ?>
							<?php if (PASSWORD_STRENGTH == 1) { ?>
							,pwd_strength: true
							<?php } ?>
						},
						mobile: {
							required: true,
							mobile: true
						},
						passkey: {
							passkey: true,
							required: true
						},
						introduction: {
							required: true
						},
						diplomas: {
							required: true
						},
						email: {
							required: true,
							email: true
						},
						home_address: {
							required: true
						},
						passports: {
							required: true
						}
					},

					// Messages for form validation
					messages : {
						user_name: {
							required: '<?php l('请输入姓名。');?>',
							real_name: '<?php l('姓名由任意长度的汉字、字母、符号组成。');?>'
						},
						i_age: {
							required: '<?php l('请输入译龄。');?>',
							max: '<?php l('请输入译龄。');?>'
						},
						'languages[]': {
							required: '<?php l('请选择精通语言。');?>'
						},
						introduction: {
							required: '<?php l('请输入您的简介。');?>'
						},
						sex: {
							required: '<?php l('请选择性别。');?>'
						},
						password: {
							required: '<?php l('请输入密码。');?>',
							<?php if (PASSWORD_STRENGTH == 1) { ?>
							pwd_min_length: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>',
							pwd_strength: '<?php l("密码由8~16位数字、字母、符号组成，不允许有空格"); ?>'
							<?php } else { ?>
							pwd_min_length: '<?php l("密码的最小大小是" . PASSWORD_MIN_LENGTH . "个字符。");?>'
							<?php } ?>
						},
						mobile: {
							required: '<?php l('请输入手机号码。');?>',
							mobile: '<?php l('请输入正确的手机号码。');?>'
						},
						passkey: {
							passkey: function() {
								if ($('#interpreter_passkey').prop('valid_passkey') === null)
									return '';
								return '<?php l('请输入正确的验证码。');?>'
							},
							required: '<?php l('请输入验证码。');?>'
						},
						introduction: {
							required: '<?php l('请输入您的简介。');?>'
						},
						diplomas: {
							required: '<?php l('请上传资格证书。');?>'
						},
						email: {
							required: '<?php l('请输入电子邮箱。');?>',
							email: '<?php l('请输入正确的电子邮箱。');?>'
						},
						home_address: {
							required: '<?php l('请输入家庭住址。');?>'
						},
						passports: {
							required: '<?php l('请上传身份证件。');?>'
						}
					}
				}, getValidationRules()));

				$('#interpreter_form').ajaxForm({
					dataType : 'json',
					beforeSubmit: function() {
						$('[type="submit"]').prop('disabled', true);
					},
					success: function(res, statusText, xhr, form) {
						try {
							if (res.err_code == 0)
							{
								alertBox("<?php l('提示');?>", "<?php l('用户注册成功，但您的账户还没激活，请耐心等待管理员的审核。');?>", function() {
										goto_url("login");
									}, 3500);
								return;
							}
							else {
								$('[type="submit"]').prop('disabled', false);
								errorBox("<?php l('错误发生');?>", res.err_msg);
							}
						}
						finally {
						}

						$('#patient_form').resetTelNumber();
					}
				});	

				$('[name="languages"]').selectpicker()
					.on('change', function() { $(this).valid();});
				$('[name="languages[]"]').selectpicker()
					.on('change', function() { $(this).valid();});
				$('[name="diseases[]"]').selectpicker()
					.on('change', function() { $(this).valid();});

				$('[name="hospitals[]"]').selectpicker()
					.on('change', function() { $(this).valid();});

				$('[name="passkey"]').change(function() {
					var $this = $(this);
					var form = $this.closest('form');
					var mobile_control = form.find('[name="mobile"]');
					var mobile = mobile_control.val();
					intlTelInput = mobile_control.data('plugin_intlTelInput');
					if (intlTelInput) {
						mobile = intlTelInput.getNumber();
					}
					var passkey = $this.val();
					if (passkey == '') {
						$this.prop('valid_passkey', false)
						$this.valid();;	
						return;
					}
					$this.prop('valid_passkey', null);
					App.callAPI("api/common/check_passkey",
						{
							phone_num: mobile,
							passkey: passkey
						}
					)
					.done(function(res) {
						$this.prop('valid_passkey', res.valid);
						$this.closest('.form-group').find('.help-block-info').addClass('hide');
						$this.valid();
					})
			        .fail(function(res) {
			        	errorBox("<?php l('错误发生');?>", res.err_msg);
			        });
				});

				$('#contract_link').click(function() {
					$('#contract_view').modal('show');
				});

			});
		</script>
	</body>
</html>
