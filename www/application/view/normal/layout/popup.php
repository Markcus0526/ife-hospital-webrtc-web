<!DOCTYPE html>
<html lang="<?php p(_lang()); ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <base href="<?php p(SITE_BASEURL);?>">

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

    <body class="popup">
        <?php $this->include_view(); ?>

        <script src="js/vendor.min.js?<?php p(VERSION); ?>" type="text/javascript"></script>
        <script src="js/bootstrap-datepicker/js/locales/bootstrap-datepicker.<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>
        <script src="js/select2/select2_locale_<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>

        <script src="<?php p(_js_url("js/app"));?>" type="text/javascript"></script>

        <?php $this->include_js(); ?>

        <script src="js/lang.<?php p(_lang()); ?>.js?<?php p(VERSION); ?>" type="text/javascript"></script>
        <script src="<?php p(_js_url("js/utility"));?>" type="text/javascript"></script>
        
        <script type="text/javascript">
            jQuery(document).ready(function() {
                App.initPopup("<?php p(HOME_BASE); ?>"); 
            });
        </script>
        
        <?php $this->include_viewjs(); ?>
    </body>
</html>
