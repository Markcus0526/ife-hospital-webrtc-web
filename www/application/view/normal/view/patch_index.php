<!DOCTYPE html>
<html lang="en">
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

    <body class="page-container-bg-solid">
        <div class="page-container">
            <div class="page-content">
                <div class="portlet light">
	                <h3 class="logo"><?php l(PRODUCT_NAME); ?></h3>
					<h1><?php l("更新向导");?></h1>
					<p><?php l("此向导将帮您更新");?></p>
					<form id="form" action="patch/patch_ajax" class="form-signin form-horizontal" method="post">
						<div class="content-page">
							<div class="row">
								<div class="col-sm-12">
									<table class="table table-striped table-hover table-bordered">
										<tr>
											<th width="90px"><?php l("版本号");?></th>
											<th><?php l("说明");?></th>
										</tr>
									<?php 
										foreach($this->must_patches as $p) {
									?>
										<tr>
											<td><?php p($p["version"]); ?></td>
											<td><?php p(_str2html($p["description"])); ?></td>
										</tr>
									<?php 
										}
									?>
									</table>
									<div class="text-right">
										<button type="button" class="btn btn-primary" id="btnStart"><i class="fa fa-check"></i> <?php l("开始");?></button>
									</div>
								</div>
							</div>
						</div>
					    <div class="clr"></div>
					</form>
				</div>
		    </div> <!-- /container -->
		</div>

		<?php include_once(_template("module/debug.php")); ?>

        <script src="js/jquery.min.js" type="text/javascript"></script>
        <script src="js/vendor.min.js?<?php p(VERSION); ?>" type="text/javascript"></script>
        <script src="js/bootstrap-datepicker/js/locales/bootstrap-datepicker.<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>
		<script src="js/select2/select2_locale_<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>

		<script src="<?php p(_js_url("js/app"));?>" type="text/javascript"></script>

		<?php $this->include_js(); ?>

		<script src="js/lang.<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>
		<script src="<?php p(_js_url("js/utility"));?>" type="text/javascript"></script>

		<?php $this->include_viewjs(); ?>

		<script type="text/javascript">
			disable_alarm = true;

			$(function () {
				$('#form').ajaxForm({
					dataType : 'json',
					success: function(res, statusText, xhr, form) {
						try {
							if (res.err_code == 0)
							{
								alertBox("<?php l('更新完成');?>", "<?php l('系统更新完成。以后您会使用系统。');?>", function() {
									goto_url("<?php p(_url("sysman/version_history"));?>");
								});
							}
							else {
								errorBox("<?php l('更新失败');?>", "<?php l('对不起，更新失败了。');?>");
							}
						}
						finally {
						}
					}
				});

				$('#btnStart').click(function() {
					confirmBox("<?php l('提示');?>", "<?php l('确定要开始本更新?');?>", function() {
						$('#form').submit();
					});
				});
			});
		</script>
	</body>
</html>