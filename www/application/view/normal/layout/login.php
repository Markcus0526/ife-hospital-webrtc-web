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

		<?php $this->include_css(); ?>

		<!-- Just for debugging purposes. Don't actually copy this line! -->
		<!--[if lt IE 9]><script src="js/ie8-responsive-file-warning.js"></script><![endif]-->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
		<![endif]-->
	</head>

	<body class="login">
		<div class="container transparent">
			<div class="login-form">
				<h3 class="form-title"><?php l("3QC国际医疗视频会诊");?></h3>

				<ul class="nav nav-tabs nav-justified">
					<li class="<?php if($this->login_mode==1) p("active"); ?>">
						<a href="#tab_password_login" data-toggle="tab">
							<?php l("密码登录"); ?>
						</a>
					</li>
					<li class="<?php if($this->login_mode==2) p("active"); ?>">
						<a href="#tab_passkey_login" data-toggle="tab">
							<?php l("验证码登录"); ?>
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane <?php if($this->login_mode==1) p("active"); ?>" id="tab_password_login">
						<form id="password_login_form" method="post" class="form">
							<input type="hidden" name="login_mode" value='1'>
							<input type="hidden" name="brtt" value=''>
							<div class="form-group">
								<div class="input-icon">
									<i class="mdi-social-person-outline"></i>
									<?php $this->mUser->id_prefix = "pwd_"; ?>
			                    	<?php $this->mUser->tel("mobile", true, array("placeholder" => _l("手机号"), "autocomplete" => "off", "maxlength" => 25)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<div class="input-icon">
									<i class="mdi-action-lock-outline"></i>
			                    	<?php $this->mUser->password("password", array("placeholder" => _l("密码"), "autocomplete" => "off", "maxlength" => 50)); ?>
			                    </div>
							</div>
				
			                <?php if ($this->login_mode==1 && $this->err_login) {?>
							<div class="alert alert-danger">
								<span style="word-break:break-all">
									<?php
									l(_err_msg($this->err_login));
									if ($this->err_login == ERR_DISABLED_LOGIN)
									{
										$admin_mobiles = implode(",", userModel::get_nation_admin_mobiles($this->mUser->country_id));

										if ($admin_mobiles)
											l("（电话:%s）", $admin_mobiles);
									}
									?>
								</span>
							</div>
			                <?php } ?>
							<div class="form-actions">
								<button type="submit" class="btn btn-primary btn-lg btn-block"><?php l("登 录"); ?></button>
							</div>
						</form>
					</div>
					<div class="tab-pane <?php if($this->login_mode==2) p("active"); ?>" id="tab_passkey_login">
						<form id="passkey_login_form" method="post" class="form">
							<input type="hidden" name="login_mode" value='2'>
							<input type="hidden" name="brtt" value=''>
							<div class="form-group">
								<div class="input-icon">
									<i class="mdi-social-person-outline"></i>
									<?php $this->mUser->id_prefix = "psk_"; ?>
			                    	<?php $this->mUser->tel("mobile", true, array("placeholder" => _l("手机号"), "autocomplete" => "off", "maxlength" => 25)); ?>
			                    </div>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-icon">
										<i class="mdi-action-lock-outline"></i>
				                    	<?php $this->mUser->input("passkey", array("placeholder" => _l("验证码"), "autocomplete" => "off", "maxlength" => 10)); ?>
				                    </div>
				                    <span class="input-group-btn">
										<button id="send_passkey" mobile="mobile" class="btn btn-primary send-passkey" type="button"><?php l("点击获取"); ?><span></span></button>
									</span>
								</div>
							</div>
				
							<div class="alert alert-danger hide">
								<span style="word-break:break-all"></span>
							</div>
							<div class="form-actions">
								<button type="submit" class="btn btn-primary btn-lg btn-block"><?php l("登 录"); ?></button>
							</div>
						</form>
					</div>
				</div>

				<div class="row other-links">
					<div class="col-xs-12">
						<a href="forget"><?php l("忘记密码？"); ?></a>		
					</div>
					<div class="col-xs-12">
						<?php l("还没有账号？");?><a href="register"><?php l("点我注册"); ?></a>		
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
			$(function() {
				App.init("<?php p(HOME_BASE); ?>", "<?php p(_lang()); ?>"); 
				App.initQuickSidebar(); 

				$(".form").on('submit', function() {
					// for set client time zone
					$('[name="brtt"]').val(new Date);
				});

				$('#password_login_form').validate($.extend({
					rules : {
						mobile: {
							required: true,
							mobile: true
						},
						password: {
							required: true
						}
					},

					// Messages for form validation
					messages : {
						mobile: {
							required: '<?php l('请输入手机号码。');?>',
							mobile: '<?php l('请输入正确的手机号。');?>'
						},
						password: {
							required: '<?php l('请输入密码。');?>'
						}
					}
				}, getValidationRules()));

				$('#passkey_login_form').validate($.extend({
					rules : {
						mobile: {
							required: true,
							mobile: true
						},
						passkey: {
							passkey: true,
							required: true
						}
					},

					// Messages for form validation
					messages : {
						mobile: {
							required: '<?php l('请输入手机号码。');?>',
							mobile: '<?php l('请输入正确的手机号。');?>'
						},
						passkey: {
							passkey: function() {
								if ($('[name="passkey"]').prop('valid_passkey') === null)
									return '';
								return '<?php l('请输入正确的验证码。');?>'
							},
							required: '<?php l('请输入验证码。');?>'
						}
					}
				}, getValidationRules()));

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
							passkey: passkey,
							login: 1
						}
					)
					.done(function(res) {
						$this.prop('valid_passkey', res.valid);
						$this.closest('.form-group').find('.help-block-info').addClass('hide');
						$this.valid();
					})
			        .fail(function(res) {
			        	$('#passkey_login_form .alert').removeClass('hide');
			        	$('#passkey_login_form .alert span').text(res.err_msg);
			        });
				});
				
				$(window).resize(function() {
					w_height = $(window).height();
					$('body > .container').height(w_height);

					h = $('body > .container > div').height();
					$('body > .container > div').css('margin-top', (w_height - h) / 3) ;
					$('body > .container').removeClass('transparent');
				});
				$(window).resize();
			});
		</script>
	</body>
</html>
