3QC全球远程医疗会诊系统安装说明

１．	互联网服务器的安装
-	运行环境
本系统正常运行在支持PHP5.4.7以上的Apache互联网服务器下。
Windows之下，我们推荐XAMPP 1.8.3。
-	我们推荐的环境如下：
内存：８GB以上
CPU：Intel core i5以上
HDD：50G以上
OS: Windows 8，10
-	软件安装
在系统中，安装XAMPP 1.8.3。
尽量安装XAMPP在与系统驱动不一样的驱动(例如d:\xampp)中。
-	在d:\中，把「3QC全球远程医疗会诊系」压缩文件teleclinic.zip解开，teleclinic文件夹创建了。
注意： 如果存在config.inc文件，安装开始之前，必须需要删除该文件。
-	之后，在Aapache的httpd.conf(例：d:\xampp\apache\conf\httpd.conf)文件中，请添加以下的行。
Alias /teleclinic "d:\teleclinic"
<Directory " d:\teleclinic">
    AllowOverride All
    Require all granted
</Directory>
-	在php的php.ini（例：d:\xampp\php\php.ini）中，请修改以下的行。
error_reporting = E_ALL & ~E_NOTICE | E_STRICT

-	如果不激活mod_rewrite模块，请激活此模块。
LoadModule rewrite_module modules/mod_rewrite.so

-	请再启动互联网服务器。
-	请开浏览器显示本网站。
URL：　http://[服务器地址]/teleclinic/
-	将显示安装向导网页。
-	安装向导网页将自动检验系统运行环境，如果有问题，提示问题。
-	请输入为需要安装的设置信息。
-	输入设置信息之后、请点【开始安装】按钮。