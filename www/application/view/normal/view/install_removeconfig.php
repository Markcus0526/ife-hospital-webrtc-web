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
                    <h1>欢迎<?php l(PRODUCT_NAME); ?>安装向导</h1>
                    <p><?php l("此向导将帮您安装"); ?><?php l(PRODUCT_NAME); ?>。</p>

                    <div class="main">
                        <div class="important"><?php l("如果您想重新安装该系统，请先删除config.inc文件和单击下面的“确认”按钮。"); ?><br/> <?php l("安装之后，现在系统的所有数据都将初始化，请备份系统数据。"); ?></div>

                        <div class="text-right">
                            <a href="home" class="btn btn-default"><i class="fa fa-times"></i> <?php l("返回首页"); ?></a>
                            <a href="install" class="btn btn-primary"><i class="fa fa-check"></i> <?php l("确认"); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
