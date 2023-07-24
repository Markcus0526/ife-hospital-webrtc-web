<!DOCTYPE html>
<html lang="<?php p(_lang()); ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php p(PRODUCT_NAME); ?>">
        <meta name="author" content="">

        <base href="<?php p(SITE_BASEURL);?>">

        <link rel="shortcut icon" href="ico/favicon.png">
        <link rel="icon" href="ico/favicon.ico" type="image/x-icon">

        <title><?php p(PRODUCT_NAME); ?></title>

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

    <body>

        <div class="clearfix"></div>

        <h1><?php p($this->err_title); ?></h1>
        <p><?php p($this->err_msg); ?></p>

        <?php include_once(_template("module/debug.php")); ?>

        <script src="js/vendor.min.js?<?php p(VERSION); ?>" type="text/javascript"></script>
        
        <script src="js/app.js?<?php p(VERSION); ?>" type="text/javascript"></script>

        <?php $this->include_js(); ?>

        <script src="js/utility.js?<?php p(VERSION); ?>" type="text/javascript"></script>

        <?php $this->include_viewjs(); ?>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                App.init("<?php p(HOME_BASE); ?>"); 
                App.initQuickSidebar(); 
            });
        </script>
    </body>
</html>