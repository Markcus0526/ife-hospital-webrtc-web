<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner">
        <div class="page-logo">
            <!--
            <div class="menu-toggler sidebar-toggler">
            </div>
            -->
            <a href="home">
                <img src="img/logo.png" alt="logo" class="logo-default"/>
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
                        <?php p(_code_label(CODE_LANG, _lang())); ?> &nbsp;<i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                    <?php 
                        $language = new languageModel;
                        $err = $language->select("language_code IS NOT NULL");
                        while ($err == ERR_OK) { 
                    ?>
                        <li><a role="menuitem" tabindex="-1" href="?lang=<?php p($language->language_code);?>"><?php p(_code_label(CODE_LANG, $language->language_code));?></a></li>
                    <?php
                            $err = $language->fetch();
                        }
                    ?>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="search-form">
            <div class="input-group">
                <input id="search_query" class="form-control" type="text" name="search_query" >
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit"><?php l("搜索"); ?></button>
                </span>
            </div>
        </div>
    </div>
</div>