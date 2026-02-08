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
			<form id="forget_form" class="forget-form" method="post">
				<h3 class="form-title"><?php l("MARKCUS国际医疗视频会诊");?></h3>

				<p><?php l("新密码将会发送至你的注册手机");?></h3>

				<div class="form-group">
                   	<?php $this->mUser->input("user_name", array("placeholder" => _l("真实姓名"), "required" => "required", "maxlength" => 50)); ?>
				</div>
				<div class="form-group">
                    <?php $this->mUser->tel("mobile", true, array("placeholder" => _l("手机号"), "required" => "required", "maxlength" => 25)); ?>
				</div>

				<?php 
				if ($this->reseted_password > 0) { 
				?>
				<div class="alert alert-danger">
					<span style="word-break:break-all">
						<?php
						l(_err_msg($this->reseted_password));
						if ($this->admin_mobiles)
						//	l("（电话:%s）", $admin_mobiles);
							l("（电话:%s）", $this->admin_mobiles);
						?>
					</span>
				</div>
				<?php
				}
				?>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary btn-lg btn-block"><?php l("找回密码"); ?></button>
				</div>

				<div class="row other-links">
					<div class="col-xs-12">
						<?php l($this->contact_phone);?>
					</div>
					<div class="col-xs-12 text-right">
						<a href="login"><?php l("登录 >>"); ?></a>		
					</div>
				</div>
			</form>
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

				<?php 
				if ($this->reseted_password == ERR_OK) {
				?>
				alertBox("<?php l("密码找回");?>", "<?php l("已发送新密码"); ?>");
				<?php
				}
				?>

				$('#forget_form').validate($.extend({
					rules : {
						user_name: {
							required: true
						},
						mobile: {
							required: true,
							mobile: true
						}
					},

					// Messages for form validation
					messages : {
						user_name: {
							required: '<?php l('请输入真实姓名。');?>'
						},
						mobile: {
							required: '<?php l('请输入手机号码。');?>',
							mobile: '<?php l('请输入正确的手机号。');?>'
						}
					}
				}, getValidationRules()));

				$(window).resize(function() {
					w_height = $(window).height();
					$('body > .container').height(w_height);

					h = $('body > .container > form').height();
					$('body > .container > form').css('margin-top', (w_height - h) / 3) ;
					$('body > .container').removeClass('transparent');
				});
				$(window).resize();
			});
		</script>
	</body>
</html>
