<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner">
        <div class="page-logo">
            <!--
            <div class="menu-toggler sidebar-toggler">
            </div>
            -->
            <a href="home">
                <img src="img/logo_<?php p(_lang()); ?>.png" alt="logo" class="logo-default"/>
            </a>
        </div>
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
        </a>
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right additional-nav">
                <li>
                    <a href="home">
                        <i class="ln-icon-home2"></i> <?php l("首页"); ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="sidebar-toggler">
                        <i class="ln-icon-list"></i> <?php l("导航"); ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                        <?php p(_lang_name()); ?> &nbsp;<i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                    <?php 
                        $language = new languageModel;
                        $err = $language->select("language_code IS NOT NULL");
                        while ($err == ERR_OK) { 
                    ?>
                        <li><a role="menuitem" tabindex="-1" href="?lang=<?php p($language->language_code);?>"><?php $language->detail_l("language_name");?></a></li>
                    <?php
                            $err = $language->fetch();
                        }
                    ?>
                    </ul>
                </li>
            </ul>
        </div>
        <?php
        $notices = noticeModel::get_all();
        if (count($notices) > 0) {
        ?>
        <div class="cyclebar">
            <i class="mdi-av-volume-up text-primary"></i>
            <ul class="notice-list">
                <?php
                foreach ($notices as $notice) {
                ?>
                <li>
                    <?php p(_l_model($notice, "content")); ?>
                </li>
                <?php 
                }
                ?>      
            </ul>
        </div>
        <?php
        }
        ?>
    </div>
</div>