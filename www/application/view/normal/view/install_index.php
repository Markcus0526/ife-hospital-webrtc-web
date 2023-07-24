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
                    <p>此向导将帮您安装<?php l(PRODUCT_NAME); ?></p>

                    <form id="form" action="install/start_ajax" class="form-signin form-horizontal" method="post">
                        <?php $mConfig->hidden("step"); ?>
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>1. <?php l("环境检验"); ?></h3>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Apache版本 </label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?php p(apache_get_version()); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">MySQL Extension </label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?php $mConfig->installed("installed_mysql"); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">mbstring Extension </label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?php $mConfig->installed("installed_mbstring"); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h3>&nbsp;</h3>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">PHP版本</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?php p(phpversion()); $mConfig->input("require_php_ver", array("class"=>"input-null")); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">gd Extension </label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?php $mConfig->installed("installed_gd"); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4">SimpleXML Extension </label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?php $mConfig->installed("installed_simplexml"); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>2. <?php l("数据库设置");?></h3>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="db_hostname">MySQL<?php l("服务器地址");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("db_hostname", array("placeholder" => "例: localhost, 192.168.224.55")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="db_user"><?php l("用户名");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("db_user", array("placeholder" => _l("必须需要数据库创建权限"))); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="db_password"><?php l("密码");?></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->password("db_password"); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="db_name"><?php l("数据库名");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("db_name", array("placeholder" => "例: teleclinic")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="db_name"><?php l("端口");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("db_port", array("placeholder" => "例: 3306")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-offset-4 col-md-5">
                                            <button type="button" class="btn btn-testdb btn-xs"><i class="fa fa-warning"></i> <?php l("连接检测");?></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h3>3. <?php l("邮件设置");?></h3>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="mail_from"><?php l("发件人邮箱");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("mail_from", array("placeholder" => "例: yang@163.com")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="mail_fromname"><?php l("发件人名称");?> <span class="required">*</span></label>
                                        <div class="col-md-6">
                                            <?php $mConfig->input("mail_fromname"); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="mail_smtp_auth">SMTP<?php l("验证");?></label>
                                        <div class="col-md-6">
                                            <?php $mConfig->checkbox_single("mail_smtp_auth", _l("使用SMTP验证")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group" id="group_mail_smtp_use_ssl">
                                        <label class="control-label col-md-4" for="mail_smtp_use_ssl">SMTP SSL<?php l("验证");?></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->checkbox_single("mail_smtp_use_ssl", _l("使用SSL验证")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group" id="group_mail_smtp_server">
                                        <label class="control-label col-md-4" for="mail_smtp_server">SMTP<?php l("服务器地址");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("mail_smtp_server", array("placeholder" => "例: mail.163.com")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group" id="group_mail_smtp_user">
                                        <label class="control-label col-md-4" for="mail_smtp_user">SMTP<?php l("帐号");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("mail_smtp_user", array("placeholder" => "例: yang")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group" id="group_mail_smtp_password">
                                        <label class="control-label col-md-4" for="mail_smtp_password">SMTP<?php l("密码");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->password("mail_smtp_password"); ?>
                                        </div>
                                    </div>
                                    <div class="form-group" id="group_mail_smtp_port">
                                        <label class="control-label col-md-4" for="mail_smtp_port">SMTP<?php l("端口");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("mail_smtp_port", array("placeholder" => "例: 25")); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>4. <?php l("系统其他设置");?></h3>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="admin_mobile"><?php l("超级管理员帐号");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->input("admin_mobile", array("placeholder" => "例: admin")); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="admin_password"><?php l("密码");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->password("admin_password"); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="admin_password_confirm"><?php l("密码确认");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->password("admin_password_confirm"); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="install_sample"><?php l("实例数据");?> <span class="required">*</span></label>
                                        <div class="col-md-5">
                                            <?php $mConfig->checkbox_single("install_sample", _l("安装实例数据")); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-primary" id="btnstart"><i class="fa fa-check"></i> <?php l("开始安装");?></button>
                            </div>
                        </div>
                    </form>
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
                        admin_name: {
                            required: true
                        },
                        admin_mobile: {
                            required: true
                        },
                        admin_password: {
                            required: true
                        },
                        admin_password_confirm: {
                            equalTo: $('#admin_password')
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
                        }
                    },

                    // Messages for form validation
                    messages : {
                        require_php_ver: {
                            required: "<?php l('此系统可以运行PHP ' . MIN_PHP_VER . '以上的环境');?>"
                        },
                        installed_mysql: {
                            required: "<?php l('为使用数据库，必须需要安装MySQL扩张。');?>"
                        },
                        installed_mbstring: {
                            required: "<?php l('为支持跨国言语，必须需要安装mbstring扩张。');?>"
                        },
                        installed_simplexml: {
                            required: "<?php l('为写出XML文件，必须需要安装SimpleXML扩张。');?>"
                        },
                        installed_gd: {
                            required: "<?php l('为处理画像，必须需要安装gd扩张。');?>"
                        },
                        db_hostname: {
                            required: "<?php l('请输入MySQL服务器地址。');?>"
                        },
                        db_user: {
                            required: "<?php l('请输入用户名。');?>"
                        },
                        db_name: {
                            required: "<?php l('请输入数据库名。');?>"
                        },
                        db_port: {
                            required: "<?php l('请输入端口。');?>",
                            digits: "<?php l('请输入数值。');?>"
                        },
                        admin_name: {
                            required: "<?php l('请输入超级管理员账户。');?>"
                        },
                        admin_mobile: {
                            required: "<?php l('请输入超级管理员账户。');?>",
                        },
                        admin_password: {
                            required: "<?php l('请输入超级管理员密码。');?>"
                        },
                        admin_password_confirm: {
                            equalTo: "<?php l('请输入一样的密码。');?>"
                        },
                        mail_from: {
                            required: "<?php l('送信用邮件地址。');?>",
                            email: "<?php l('不是有效的邮件地址。');?>"
                        },
                        mail_fromname: {
                            required: "<?php l('请输入送信用用户名。');?>"
                        },
                        mail_smtp_server: {
                            required: "<?php l('请输入SMTP服务器地址。');?>"
                        },
                        mail_smtp_user: {
                            required: "<?php l('请输入SMTP用户名。');?>"
                        },
                        mail_smtp_password: {
                            required: "<?php l('请输入SMTP密码。');?>"
                        },
                        mail_smtp_port: {
                            required: "<?php l('请输入SMTP端口。');?>",
                            digits: "<?php l('请输入数值');?>"
                        }
                    }
                }, getValidationRules()));

                $('#form').ajaxForm({
                    dataType : 'json',
                    success: function(res, statusText, xhr, form) {
                        try {
                            if (ret.err_code == 0)
                            {
                                var step = parseInt($('#step').val());
                                if (step == 3)
                                {
                                    hideMask();
                                    alertBox("<?php l('安装完成');?>", "<?php l('系统安装完成。以后您会使用系统。');?>", function() {
                                        goto_url("sysman/setting");
                                    });
                                    return;
                                }
                                $('#step').val(step + 1);
                                $('#form').submit();
                            }
                            else {
                                hideMask();
                                errorBox("<?php l('安装失败');?>", "<?php l('对不起，安装失败了。');?>");
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
                        var ret = confirm("<?php l('确定要安装系统吗');?>");
                        if (ret)
                        {
                            $('#form').submit();
                            showMask(true, "安装系中...");
                        }
                    }
                });

                $('.btn-testdb').click(function() {
                    $.ajax({
                        url :"install/testdb_ajax",
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
                                alertBox("<?php l('连接成功');?>", "<?php l('系统会连接数据库服务器。');?>");
                            }
                            else {
                                errorBox("<?php l('连接失败');?>", "<?php l('系统不能连接数据库服务器，请您确认服务器设置参数。');?>");
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
                        $('#group_mail_smtp_use_ssl').show();
                        $('#group_mail_smtp_server').show();
                        $('#group_mail_smtp_user').show();
                        $('#group_mail_smtp_password').show();
                        $('#group_mail_smtp_port').show();
                    }
                    else {
                        $('#group_mail_smtp_use_ssl').hide();
                        $('#group_mail_smtp_server').hide();
                        $('#group_mail_smtp_user').hide();
                        $('#group_mail_smtp_password').hide();
                        $('#group_mail_smtp_port').hide();
                    }
                });
            });
        </script>
    </body>
</html>