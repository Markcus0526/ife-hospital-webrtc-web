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

	<body class="page-header-fixed page-quick-sidebar-over-content page-sidebar-closed-hide-logo page-container-bg-solid">
		<?php include_once(_template("module/header.php")); ?>

		<div class="clearfix"></div>

		<!-- BEGIN CONTAINER -->
		<div class="page-container">
			<!-- BEGIN SIDEBAR -->
			<div class="page-sidebar-wrapper">
				<?php include_once(_template("module/sidebar.php")); ?>
			</div>
			<!-- END SIDEBAR -->
			<!-- BEGIN CONTENT -->
			<div class="page-content-wrapper">
				<div class="page-content">
					<?php $this->include_view(); ?>
				</div>
			</div>
			<!-- END CONTENT -->
		</div>
		<!-- END CONTAINER -->

		<?php include_once(_template("module/debug.php")); ?>

        <script src="js/vendor.min.js?<?php p(VERSION); ?>" type="text/javascript"></script>
        <script src="js/bootstrap-datepicker/js/locales/bootstrap-datepicker.<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>
		<script src="js/select2/select2_locale_<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>

		<script src="<?php p(_js_url("js/app"));?>" type="text/javascript"></script>

		<!--<script src="js/webrtc/adapter.js?<?php p(VERSION); ?>" type="text/javascript"></script>-->

		<?php $this->include_js(); ?>

		<script src="js/lang.<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>
		<script src="<?php p(_js_url("js/utility"));?>" type="text/javascript"></script>

		<script type="text/javascript">
			$(function() {
				// check browser
				var main_version = $.browser.version.split('.'),
					main_version = main_version[0];
				if ($.browser.chrome && main_version < 47 ||
					$.browser.chrome && main_version < 25)
				{
					errorBox("<?php l('提示'); ?>", "<?php l('现在您使用的浏览器不支持互联网视频对话，为了确保正常会诊，请使用最新版的Chrome或Firefox浏览器。'); ?>");
				}
				App.init("<?php p(HOME_BASE); ?>", "<?php p(_lang()); ?>"); 
				//App.initQuickSidebar(); 
			});
		</script>

		<?php $this->include_viewjs(); ?>
	</body>
</html>
