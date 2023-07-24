/**
Core script to handle the entire theme and core functions
**/
var App = function () {
    var getResponsiveBreakpoint = function(size) {
        // bootstrap responsive breakpoints
        var sizes = {
            'xs' : 480,     // extra small
            'sm' : 768,     // small
            'md' : 992,     // medium
            'lg' : 1200     // large
        };

        return sizes[size] ? sizes[size] : 0; 
    }
    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;

    var resizeHandlers = [];

    var layoutImgPath = 'admin/layout/img/';

    var layoutCssPath = 'admin/layout/css/';

    var resBreakpointMd = getResponsiveBreakpoint('md');

    var homeBase = "";
    
    var callAPI = function(url, params) {
        var req = $.ajax({
            url : homeBase + url,
            data : JSON.stringify(params),
            type : "post",
            dataType : 'json',
            contentType: false,
            processData: false,
            success : function(data){
                return data;
            },
            error : function() {
                return -1;
            }
        });

        var obj = {};
        defer = $.Deferred();
        defer.promise( obj );

        req.done(function(ret) {
            if (ret.err_code == 0)
                defer.resolve(ret);
            else
                defer.reject(ret);
        })
        .fail(function(ret) {
            ret = {
                err_code: -1,
                err_msg: "サーバーの呼出が失敗しました。"
            }

            defer.reject(ret);
        });

        return defer;
    }

    var handleInit = function(_homeBase) {

        if ($('body').css('direction') === 'rtl') {
            isRTL = true;
        }

        isIE8 = !!navigator.userAgent.match(/MSIE 8.0/);
        isIE9 = !!navigator.userAgent.match(/MSIE 9.0/);
        isIE10 = !!navigator.userAgent.match(/MSIE 10.0/);

        if (isIE10) {
            $('html').addClass('ie10'); // detect IE10 version
        }

        if (isIE10 || isIE9 || isIE8) {
            $('html').addClass('ie'); // detect IE10 version
        }

        homeBase = _homeBase;
    };

    // To get the correct viewport width based on  http://andylangton.co.uk/articles/javascript/get-viewport-size-javascript/
    var getViewPort = function() {
        var e = window,
            a = 'inner';
        if (!('innerWidth' in window)) {
            a = 'client';
            e = document.documentElement || document.body;
        }

        return {
            width: e[a + 'Width'],
            height: e[a + 'Height']
        };
    }

    var scrollTo = function(el, offeset) {
        var pos = (el && el.size() > 0) ? el.offset().top : 0;

        if (el) {
            if ($('body').hasClass('page-header-fixed')) {
                pos = pos - $('.page-header').height();
            }
            pos = pos + (offeset ? offeset : -1 * el.height());
        }

        $('html,body').animate({
            scrollTop: pos
        }, 'slow');
    }

    var startPageLoading = function(options) {
        if (options && options.animate) {
            $('.page-spinner-bar').remove();
            $('body').append('<div class="page-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');
        } else {
            $('.page-loading').remove();
            $('body').append('<div class="page-loading"><img src="' + this.getGlobalImgPath() + 'loading-spinner-grey.gif"/>&nbsp;&nbsp;<span>' + (options && options.message ? options.message : 'Loading...') + '</span></div>');
        }
    }

    var stopPageLoading = function() {
        $('.page-loading, .page-spinner-bar').remove();
    }

    // runs callback functions set by addResponsiveHandler().
    var _runResizeHandlers = function() {
        // reinitialize other subscribed elements
        for (var i = 0; i < resizeHandlers.length; i++) {
            var each = resizeHandlers[i];
            each.call();
        }
    };

    // handle the layout reinitialization on window resize
    var handleOnResize = function() {
        var resize;
        if (isIE8) {
            var currheight;
            $(window).resize(function() {
                if (currheight == document.documentElement.clientHeight) {
                    return; //quite event since only body resized not window.
                }
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function() {
                    _runResizeHandlers();
                }, 50); // wait 50ms until window resize finishes.                
                currheight = document.documentElement.clientHeight; // store last body client height
            });
        } else {
            $(window).resize(function() {
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function() {
                    _runResizeHandlers();
                }, 50); // wait 50ms until window resize finishes.
            });
        }
    };

    //public function to add callback a function which will be called on window resize
    var addResizeHandler = function(func) {
        resizeHandlers.push(func);
    }

    //public functon to call _runresizeHandlers
    var runResizeHandlers = function() {
        _runResizeHandlers();
    }

    var initSlimScroll = function(el) {
        $(el).each(function() {
            if ($(this).attr("data-initialized")) {
                return; // exit
            }

            var height;

            if ($(this).attr("data-height")) {
                height = $(this).attr("data-height");
            } else {
                height = $(this).css('height');
            }

            $(this).slimScroll({
                allowPageScroll: true, // allow page scroll when the element scroll is ended
                size: '7px',
                color: ($(this).attr("data-handle-color") ? $(this).attr("data-handle-color") : '#bbb'),
                wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                railColor: ($(this).attr("data-rail-color") ? $(this).attr("data-rail-color") : '#eaeaea'),
                position: isRTL ? 'left' : 'right',
                height: height,
                alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
                railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
                disableFadeOut: true
            });

            $(this).attr("data-initialized", "1");
        });
    }

    var destroySlimScroll = function(el) {
        $(el).each(function() {
            if ($(this).attr("data-initialized") === "1") { // destroy existing instance before updating the height
                $(this).removeAttr("data-initialized");
                $(this).removeAttr("style");

                var attrList = {};

                // store the custom attribures so later we will reassign.
                if ($(this).attr("data-handle-color")) {
                    attrList["data-handle-color"] = $(this).attr("data-handle-color");
                }
                if ($(this).attr("data-wrapper-class")) {
                    attrList["data-wrapper-class"] = $(this).attr("data-wrapper-class");
                }
                if ($(this).attr("data-rail-color")) {
                    attrList["data-rail-color"] = $(this).attr("data-rail-color");
                }
                if ($(this).attr("data-always-visible")) {
                    attrList["data-always-visible"] = $(this).attr("data-always-visible");
                }
                if ($(this).attr("data-rail-visible")) {
                    attrList["data-rail-visible"] = $(this).attr("data-rail-visible");
                }

                $(this).slimScroll({
                    wrapperClass: ($(this).attr("data-wrapper-class") ? $(this).attr("data-wrapper-class") : 'slimScrollDiv'),
                    destroy: true
                });

                var the = $(this);

                // reassign custom attributes
                $.each(attrList, function(key, value) {
                    the.attr(key, value);
                });

            }
        });
    }

    //* BEGIN:CORE HANDLERS *//
    // this function handles responsive layout on screen size resize or mobile device rotate.

    // Set proper height for sidebar and content. The content and sidebar height must be synced always.
    var handleSidebarAndContentHeight = function () {
        var content = $('.page-content');
        var sidebar = $('.page-sidebar');
        var body = $('body');
        var height;

        if (body.hasClass("page-footer-fixed") === true && body.hasClass("page-sidebar-fixed") === false) {
            var available_height = getViewPort().height - $('.page-footer').outerHeight() - $('.page-header').outerHeight();
            if (content.height() < available_height) {
                content.attr('style', 'min-height:' + available_height + 'px');
            }
        } else {
            if (body.hasClass('page-sidebar-fixed')) {
                height = _calculateFixedSidebarViewportHeight();
                if (body.hasClass('page-footer-fixed') === false) {
                    height = height - $('.page-footer').outerHeight();
                }
            } else {
                var headerHeight = $('.page-header').outerHeight();
                var footerHeight = $('.page-footer').outerHeight();

                if (getViewPort().width < resBreakpointMd) {
                    height = getViewPort().height - headerHeight - footerHeight;
                } else {
                    height = sidebar.height() + 20;
                }

                if ((height + headerHeight + footerHeight) <= getViewPort().height) {
                    height = getViewPort().height - headerHeight - footerHeight;
                }
            }
            content.attr('style', 'min-height:' + height + 'px');
        }
    };

    // Handle sidebar menu links
    var handleSidebarMenuActiveLink = function(mode, el) {
        var url = location.hash.toLowerCase();    

        var menu = $('.page-sidebar-menu');

        if (mode === 'click' || mode === 'set') {
            el = $(el);
        } else if (mode === 'match') {
            menu.find("li > a").each(function() {
                var path = $(this).attr("href").toLowerCase();       
                // url match condition         
                if (path.length > 1 && url.substr(1, path.length - 1) == path.substr(1)) {
                    el = $(this);
                    return; 
                }
            });
        }

        if (!el || el.size() == 0) {
            return;
        }

        if (el.attr('href').toLowerCase() === 'javascript:;' || el.attr('href').toLowerCase() === '#') {
            return;
        }        

        var slideSpeed = parseInt(menu.data("slide-speed"));
        var keepExpand = menu.data("keep-expanded");

        // disable active states
        menu.find('li.active').removeClass('active');
        menu.find('li > a > .selected').remove();

        if (menu.hasClass('page-sidebar-menu-hover-submenu') === false) {
            menu.find('li.open').each(function(){
                if ($(this).children('.sub-menu').size() === 0) {
                    $(this).removeClass('open');
                    $(this).find('> a > .arrow.open').removeClass('open');
                }                             
            }); 
        } else {
             menu.find('li.open').removeClass('open');
        }

        el.parents('li').each(function () {
            $(this).addClass('active');
            $(this).find('> a > span.arrow').addClass('open');

            if ($(this).parent('ul.page-sidebar-menu').size() === 1) {
                $(this).find('> a').append('<span class="selected"></span>');
            }
            
            if ($(this).children('ul.sub-menu').size() === 1) {
                $(this).addClass('open');
            }
        });

        if (mode === 'click') {
            if (getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                $('.page-header .responsive-toggler').click();
            }
        }
    };

    // Handle sidebar menu
    var handleSidebarMenu = function () {
        // handle sidebar link click
        $('.page-sidebar').on('click', 'li > a', function (e) {
            var hasSubMenu = $(this).next().hasClass('sub-menu');

            if (getViewPort().width >= resBreakpointMd && $(this).parents('.page-sidebar-menu-hover-submenu').size() === 1) { // exit of hover sidebar menu
                return;
            }

            if (hasSubMenu === false) {
                if (getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                    $('.page-header .responsive-toggler').click();
                }
                return;
            }

            if ($(this).next().hasClass('sub-menu always-open')) {
                return;
            }

            var parent = $(this).parent().parent();
            var the = $(this);
            var menu = $('.page-sidebar-menu');
            var sub = $(this).next();

            var autoScroll = menu.data("auto-scroll");
            var slideSpeed = parseInt(menu.data("slide-speed"));
            var keepExpand = menu.data("keep-expanded");

            if (keepExpand !== true) {
                parent.children('li.open').children('a').children('.arrow').removeClass('open');
                parent.children('li.open').children('.sub-menu:not(.always-open)').slideUp(slideSpeed);
                parent.children('li.open').removeClass('open');
            }

            var slideOffeset = -200;

            if (sub.is(":visible")) {
                $('.arrow', $(this)).removeClass("open");
                $(this).parent().removeClass("open");
                sub.slideUp(slideSpeed, function () {
                    if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                        if ($('body').hasClass('page-sidebar-fixed')) {
                            menu.slimScroll({
                                'scrollTo': (the.position()).top
                            });
                        } else {
                            scrollTo(the, slideOffeset);
                        }
                    }
                    handleSidebarAndContentHeight();
                });
            } else if (hasSubMenu) {
                $('.arrow', $(this)).addClass("open");
                $(this).parent().addClass("open");
                sub.slideDown(slideSpeed, function () {
                    if (autoScroll === true && $('body').hasClass('page-sidebar-closed') === false) {
                        if ($('body').hasClass('page-sidebar-fixed')) {
                            menu.slimScroll({
                                'scrollTo': (the.position()).top
                            });
                        } else {
                            scrollTo(the, slideOffeset);
                        }
                    }
                    handleSidebarAndContentHeight();
                });
            }

            e.preventDefault();
        });

        // handle ajax links within sidebar menu
        $('.page-sidebar').on('click', ' li > a.ajaxify', function (e) {
            e.preventDefault();
            scrollTop();

            var url = $(this).attr("href");
            var menuContainer = $('.page-sidebar ul');
            var pageContent = $('.page-content');
            var pageContentBody = $('.page-content .page-content-body');

            menuContainer.children('li.active').removeClass('active');
            menuContainer.children('arrow.open').removeClass('open');

            $(this).parents('li').each(function () {
                $(this).addClass('active');
                $(this).children('a > span.arrow').addClass('open');
            });
            $(this).parents('li').addClass('active');

            if (getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                $('.page-header .responsive-toggler').click();
            }

            startPageLoading();

            var the = $(this);
            
            $.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                success: function (res) {
                    if (the.parents('li.open').size() === 0) {
                        $('.page-sidebar-menu > li.open > a').click();
                    }

                    stopPageLoading();
                    pageContentBody.html(res);
                    App.fixContentHeight(); // fix content height
                    Metronic.initAjax(); // initialize core stuff
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    stopPageLoading();
                    pageContentBody.html('<h4>Could not load the requested content.</h4>');
                }
            });
        });

        // handle ajax link within main content
        $('.page-content').on('click', '.ajaxify', function (e) {
            e.preventDefault();
            scrollTop();

            var url = $(this).attr("href");
            var pageContent = $('.page-content');
            var pageContentBody = $('.page-content .page-content-body');

            startPageLoading();

            if (getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                $('.page-header .responsive-toggler').click();
            }

            $.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                success: function (res) {
                    stopPageLoading();
                    pageContentBody.html(res);
                    App.fixContentHeight(); // fix content height
                    Metronic.initAjax(); // initialize core stuff
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    pageContentBody.html('<h4>Could not load the requested content.</h4>');
                    stopPageLoading();
                }
            });
        });

        // handle scrolling to top on responsive menu toggler click when header is fixed for mobile view
        $(document).on('click', '.page-header-fixed-mobile .page-header .responsive-toggler', function(){
            scrollTop(); 
        });      
     
        // handle sidebar hover effect        
        handleFixedSidebarHoverEffect();

        // handle the search bar close
        $('.page-sidebar').on('click', '.sidebar-search .remove', function (e) {
            e.preventDefault();
            $('.sidebar-search').removeClass("open");
        });

        // handle the search query submit on enter press
        $('.page-sidebar .sidebar-search').on('keypress', 'input.form-control', function (e) {
            if (e.which == 13) {
                $('.sidebar-search').submit();
                return false; //<---- Add this line
            }
        });

        // handle the search submit(for sidebar search and responsive mode of the header search)
        $('.sidebar-search .submit').on('click', function (e) {
            e.preventDefault();
            if ($('body').hasClass("page-sidebar-closed")) {
                if ($('.sidebar-search').hasClass('open') === false) {
                    if ($('.page-sidebar-fixed').size() === 1) {
                        $('.page-sidebar .sidebar-toggler').click(); //trigger sidebar toggle button
                    }
                    $('.sidebar-search').addClass("open");
                } else {
                    $('.sidebar-search').submit();
                }
            } else {
                $('.sidebar-search').submit();
            }
        });

        // handle close on body click
        if ($('.sidebar-search').size() !== 0) {
            $('.sidebar-search .input-group').on('click', function(e){
                e.stopPropagation();
            });

            $('body').on('click', function() {
                if ($('.sidebar-search').hasClass('open')) {
                    $('.sidebar-search').removeClass("open");
                }
            });
        }
    };

    // Helper function to calculate sidebar height for fixed sidebar layout.
    var _calculateFixedSidebarViewportHeight = function () {
        var sidebarHeight = getViewPort().height - $('.page-header').outerHeight();
        if ($('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - $('.page-footer').outerHeight();
        }

        return sidebarHeight;
    };

    // Handles fixed sidebar
    var handleFixedSidebar = function () {
        var menu = $('.page-sidebar-menu');

        destroySlimScroll(menu);

        if ($('.page-sidebar-fixed').size() === 0) {
            handleSidebarAndContentHeight();
            return;
        }

        if (getViewPort().width >= resBreakpointMd) {
            menu.attr("data-height", _calculateFixedSidebarViewportHeight());
            initSlimScroll(menu);
            handleSidebarAndContentHeight();
        }
    };

    // Handles sidebar toggler to close/hide the sidebar.
    var handleFixedSidebarHoverEffect = function () {
        var body = $('body');
        if (body.hasClass('page-sidebar-fixed')) {
            $('.page-sidebar').on('mouseenter', function () {
                if (body.hasClass('page-sidebar-closed')) {
                    $(this).find('.page-sidebar-menu').removeClass('page-sidebar-menu-closed');
                }
            }).on('mouseleave', function () {
                if (body.hasClass('page-sidebar-closed')) {
                    $(this).find('.page-sidebar-menu').addClass('page-sidebar-menu-closed');
                }
            });
        }
    };

    // Hanles sidebar toggler
    var handleSidebarToggler = function () {
        var body = $('body');
        if ($.cookie && $.cookie('sidebar_closed') === '1' && getViewPort().width >= resBreakpointMd) {
            $('body').addClass('page-sidebar-closed');
            $('.page-sidebar-menu').addClass('page-sidebar-menu-closed');
        }

        // handle sidebar show/hide
        $('body').on('click', '.sidebar-toggler', function (e) {
            var sidebar = $('.page-sidebar');
            var sidebarMenu = $('.page-sidebar-menu');
            $(".sidebar-search", sidebar).removeClass("open");

            if (body.hasClass("page-sidebar-closed")) {
                body.removeClass("page-sidebar-closed");
                sidebarMenu.removeClass("page-sidebar-menu-closed");
                if ($.cookie) {
                    $.cookie('sidebar_closed', '0');
                }
            } else {
                body.addClass("page-sidebar-closed");
                sidebarMenu.addClass("page-sidebar-menu-closed");
                if (body.hasClass("page-sidebar-fixed")) {
                    sidebarMenu.trigger("mouseleave");
                }
                if ($.cookie) {
                    $.cookie('sidebar_closed', '1');
                }
            }

            $(window).trigger('resize');
        });
    };

    // Handles the horizontal menu
    var handleHorizontalMenu = function () {
        //handle tab click
        $('.page-header').on('click', '.hor-menu a[data-toggle="tab"]', function (e) {
            e.preventDefault();
            var nav = $(".hor-menu .nav");
            var active_link = nav.find('li.current');
            $('li.active', active_link).removeClass("active");
            $('.selected', active_link).remove();
            var new_link = $(this).parents('li').last();
            new_link.addClass("current");
            new_link.find("a:first").append('<span class="selected"></span>');
        });

        // handle search box expand/collapse        
        $('.page-header').on('click', '.search-form', function (e) {
            $(this).addClass("open");
            $(this).find('.form-control').focus();

            $('.page-header .search-form .form-control').on('blur', function (e) {
                $(this).closest('.search-form').removeClass("open");
                $(this).unbind("blur");
            });
        });

        // handle hor menu search form on enter press
        $('.page-header').on('keypress', '.hor-menu .search-form .form-control', function (e) {
            if (e.which == 13) {
                $(this).closest('.search-form').submit();
                return false;
            }
        });

        // handle header search button click
        $('.page-header').on('mousedown', '.search-form.open .submit', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).closest('.search-form').submit();
        });

        // handle hover dropdown menu for desktop devices only
        $('[data-hover="megamenu-dropdown"]').not('.hover-initialized').each(function() {   
            $(this).dropdownHover(); 
            $(this).addClass('hover-initialized'); 
        });
        
        $(document).on('click', '.mega-menu-dropdown .dropdown-menu', function (e) {
            e.stopPropagation();
        });
    };

    // Handles Bootstrap Tabs.
    var handleTabs = function () {
        // fix content height on tab click
        $('body').on('shown.bs.tab', 'a[data-toggle="tab"]', function () {
            handleSidebarAndContentHeight();
        });
    };

    // Handles the go to top button at the footer
    var handleGoTop = function () {
        var offset = 300;
        var duration = 500;

        if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {  // ios supported
            $(window).bind("touchend touchcancel touchleave", function(e){
               if ($(this).scrollTop() > offset) {
                    $('.scroll-to-top').fadeIn(duration);
                } else {
                    $('.scroll-to-top').fadeOut(duration);
                }
            });
        } else {  // general 
            $(window).scroll(function() {
                if ($(this).scrollTop() > offset) {
                    $('.scroll-to-top').fadeIn(duration);
                } else {
                    $('.scroll-to-top').fadeOut(duration);
                }
            });
        }
        
        $('.scroll-to-top').click(function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, duration);
            return false;
        });
    };

    // Hanlde 100% height elements(block, portlet, etc)
    var handle100HeightContent = function () {

        var target = $('.full-height-content');
        var height;

        height = getViewPort().height -
            $('.page-header').outerHeight(true) -
            $('.page-footer').outerHeight(true) -
            $('.page-title').outerHeight(true) -
            $('.page-bar').outerHeight(true);

        if (target.hasClass('portlet')) {
            var portletBody = target.find('.portlet-body');
            
            if (getViewPort().width < resBreakpointMd) {
                destroySlimScroll(portletBody.find('.full-height-content-body')); // destroy slimscroll 
                return;
            }

            height = height -
                target.find('.portlet-title').outerHeight(true) -
                parseInt(target.find('.portlet-body').css('padding-top')) -
                parseInt(target.find('.portlet-body').css('padding-bottom')) - 2;

            if (target.hasClass("full-height-content-scrollable")) {
                height = height - 35;
                portletBody.find('.full-height-content-body').css('height', height);
                initSlimScroll(portletBody.find('.full-height-content-body'));
            } else {
                portletBody.css('min-height', height);
            }
        } else {
            if (getViewPort().width < resBreakpointMd) {
                destroySlimScroll(target.find('.full-height-content-body')); // destroy slimscroll 
                return;
            }

            if (target.hasClass("full-height-content-scrollable")) {
                height = height - 35;
                target.find('.full-height-content-body').css('height', height);
                initSlimScroll(target.find('.full-height-content-body'));
            } else {
                target.css('min-height', height);
            }
        }
    };
    //* END:CORE HANDLERS *//

    var handleBootstrapSwitch = function() {
        if (!$().bootstrapSwitch) {
            return;
        }
        $('.make-switch').bootstrapSwitch();
    };

    var handleFancybox = function (parent) {
        if (!jQuery.fancybox) {
            return;
        }

        parent = parent || "";
        parent = parent + " ";
        
        jQuery(parent + ".fancybox").each(function() {
            $this = $(this);
            width = $this.attr('fancy-width');
            switch(width) {
                case "max":
                    width = $(window).width() - 60;
                    break;
                case undefined:
                case '':
                    width = undefined;
                    break;
                default:
                    width = toInt(width);
                    break;
            }
            height = $this.attr('fancy-height');
            switch(height) {
                case "max":
                    height = $(window).height() - 160;
                    break;
                case undefined:
                case '':
                    height = undefined;
                    break;
                default:
                    height = toInt(height);
                    break;
            }
            $this.fancybox({
                'type' : 'iframe',
                'width' : width,
                'height' : height,
                'autoSize': false,
                'closeBtn': false,
                'afterClose': function () {
                    $this.trigger('fancyboxAfterClose');
                }
            });
        });
        
        jQuery(parent + ".fancybox-image").fancybox({
            prevEffect : 'none',
            nextEffect : 'none'
        })

        if (jQuery(parent + ".fancybox-button").size() > 0) {            
            jQuery(parent + ".fancybox-button").fancybox({
                groupAttr: 'data-rel',
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                helpers: {
                    title: {
                        type: 'inside'
                    }
                }
            });

            $(parent + '.fancybox-video').fancybox({
                type: 'iframe'
            });
        }
    }

    ////////////////////////////////////////
    // START ---------- Handle Quick Sidebar
    ////////////////////////////////////////
    
    // Handles quick sidebar toggler
    var handleQuickSidebarToggler = function () {
        // quick sidebar toggler
        $('.top-menu .dropdown-quick-sidebar-toggler a, .page-quick-sidebar-toggler').click(function (e) {
            $('body').toggleClass('page-quick-sidebar-open'); 
        });
    };

    // Handles quick sidebar chats
    var handleQuickSidebarChat = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperChat = wrapper.find('.page-quick-sidebar-chat');

        var initChatSlimScroll = function () {
            var chatUsers = wrapper.find('.page-quick-sidebar-chat-users');
            var chatUsersHeight;

            chatUsersHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // chat user list 
            destroySlimScroll(chatUsers);
            chatUsers.attr("data-height", chatUsersHeight);
            initSlimScroll(chatUsers);

            var chatMessages = wrapperChat.find('.page-quick-sidebar-chat-user-messages');
            var chatMessagesHeight = chatUsersHeight - wrapperChat.find('.page-quick-sidebar-chat-user-form').outerHeight() - wrapperChat.find('.page-quick-sidebar-nav').outerHeight();

            // user chat messages 
            destroySlimScroll(chatMessages);
            chatMessages.attr("data-height", chatMessagesHeight);
            initSlimScroll(chatMessages);
        };

        initChatSlimScroll();
        addResizeHandler(initChatSlimScroll); // reinitialize on window resize

        wrapper.find('.page-quick-sidebar-chat-users .media-list > .media').click(function () {
            wrapperChat.addClass("page-quick-sidebar-content-item-shown");
        });

        wrapper.find('.page-quick-sidebar-chat-user .page-quick-sidebar-back-to-list').click(function () {
            wrapperChat.removeClass("page-quick-sidebar-content-item-shown");
        });

        var handleChatMessagePost = function (e) {
            e.preventDefault();

            var chatContainer = wrapperChat.find(".page-quick-sidebar-chat-user-messages");
            var input = wrapperChat.find('.page-quick-sidebar-chat-user-form .form-control');

            var text = input.val();
            if (text.length === 0) {
                return;
            }

            var preparePost = function(dir, time, name, avatar, message) {
                var tpl = '';
                tpl += '<div class="post '+ dir +'">';
                tpl += '<img class="avatar" alt="" src="' + App.getLayoutImgPath() + avatar +'.jpg"/>';
                tpl += '<div class="message">';
                tpl += '<span class="arrow"></span>';
                tpl += '<a href="#" class="name">Bob Nilson</a>&nbsp;';
                tpl += '<span class="datetime">' + time + '</span>';
                tpl += '<span class="body">';
                tpl += message;
                tpl += '</span>';
                tpl += '</div>';
                tpl += '</div>';

                return tpl;
            };

            // handle post
            var time = new Date();
            var message = preparePost('out', (time.getHours() + ':' + time.getMinutes()), "Bob Nilson", 'avatar3', text);
            message = $(message);
            chatContainer.append(message);

            var getLastPostPos = function() {
                var height = 0;
                chatContainer.find(".post").each(function() {
                    height = height + $(this).outerHeight();
                });

                return height;
            };           

            chatContainer.slimScroll({
                scrollTo: getLastPostPos()
            });

            input.val("");

            // simulate reply
            setTimeout(function(){
                var time = new Date();
                var message = preparePost('in', (time.getHours() + ':' + time.getMinutes()), "Ella Wong", 'avatar2', 'Lorem ipsum doloriam nibh...');
                message = $(message);
                chatContainer.append(message);

                chatContainer.slimScroll({
                    scrollTo: getLastPostPos()
                });
            }, 3000);
        };

        wrapperChat.find('.page-quick-sidebar-chat-user-form .btn').click(handleChatMessagePost);
        wrapperChat.find('.page-quick-sidebar-chat-user-form .form-control').keypress(function (e) {
            if (e.which == 13) {
                handleChatMessagePost(e);
                return false;
            }
        });
    };

    // Handles quick sidebar tasks
    var handleQuickSidebarAlerts = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperAlerts = wrapper.find('.page-quick-sidebar-alerts');

        var initAlertsSlimScroll = function () {
            var alertList = wrapper.find('.page-quick-sidebar-alerts-list');
            var alertListHeight;

            alertListHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // alerts list 
            destroySlimScroll(alertList);
            alertList.attr("data-height", alertListHeight);
            initSlimScroll(alertList);
        };

        initAlertsSlimScroll();
        addResizeHandler(initAlertsSlimScroll); // reinitialize on window resize
    };

    // Handles quick sidebar settings
    var handleQuickSidebarSettings = function () {
        var wrapper = $('.page-quick-sidebar-wrapper');
        var wrapperAlerts = wrapper.find('.page-quick-sidebar-settings');

        var initSettingsSlimScroll = function () {
            var settingsList = wrapper.find('.page-quick-sidebar-settings-list');
            var settingsListHeight;

            settingsListHeight = wrapper.height() - wrapper.find('.nav-justified > .nav-tabs').outerHeight();

            // alerts list 
            destroySlimScroll(settingsList);
            settingsList.attr("data-height", settingsListHeight);
            initSlimScroll(settingsList);
        };

        initSettingsSlimScroll();
        addResizeHandler(initSettingsSlimScroll); // reinitialize on window resize
    };

    // Handlesmaterial design checkboxes
    var handleMaterialDesign = function() {

        // Material design ckeckbox and radio effects
        $('body').on('click', '.md-checkbox > label, .md-radio > label', function() {
            var the = $(this);
            // find the first span which is our circle/bubble
            var el = $(this).children('span:first-child');
              
            // add the bubble class (we do this so it doesnt show on page load)
            el.addClass('inc');
              
            // clone it
            var newone = el.clone(true);  
              
            // add the cloned version before our original
            el.before(newone);  
              
            // remove the original so that it is ready to run on next click
            $("." + el.attr("class") + ":last", the).remove();
        }); 

        if ($('body').hasClass('page-md')) { 
            // Material design click effect
            // credit where credit's due; http://thecodeplayer.com/walkthrough/ripple-click-effect-google-material-design       
            var element, circle, d, x, y;
            $('body').on('click', 'a.btn, button.btn, input.btn, label.btn', function(e) { 
                element = $(this);
      
                if(element.find(".md-click-circle").length == 0) {
                    element.prepend("<span class='md-click-circle'></span>");
                }
                    
                circle = element.find(".md-click-circle");
                circle.removeClass("md-click-animate");
                
                if(!circle.height() && !circle.width()) {
                    d = Math.max(element.outerWidth(), element.outerHeight());
                    circle.css({height: d, width: d});
                }
                
                x = e.pageX - element.offset().left - circle.width()/2;
                y = e.pageY - element.offset().top - circle.height()/2;
                
                circle.css({top: y+'px', left: x+'px'}).addClass("md-click-animate");

                setTimeout(function() {
                    circle.remove();      
                }, 1000);
            });
        }

        // Floating labels
        var handleInput = function(el) {
            if (el.val() != "") {
                el.addClass('edited');
            } else {
                el.removeClass('edited');
            }
        } 

        $('body').on('keydown', '.form-md-floating-label .form-control', function(e) { 
            handleInput($(this));
        });
        $('body').on('blur', '.form-md-floating-label .form-control', function(e) { 
            handleInput($(this));
        });        

        $('.form-md-floating-label .form-control').each(function(){
            if ($(this).val().length > 0) {
                $(this).addClass('edited');
            }
        });
    }

    ////////////////////////////////////////
    // Handle Quick Sidebar ------------ END
    ////////////////////////////////////////

    //Set of functions to manage the animations of the elements
    var animateElements = function() {
        if($('.animate-if-visible').length) {
            $('.animate-if-visible').appear();
            $(document.body).on('appear', '.animate-if-visible', function(e, $affected) {
                // this code is executed for each appeared element
                var element = $(this);
                var animationOptions = element.data('animation-options');
                runAnimationTransition(element, animationOptions);
            });
        }
        if($('.animate-group').length) {
            $('.animate-group').appear();
            $(document.body).on('appear', '.animate-group', function(e, $affected) {
                var element = $(this);
                var animationInterval = 200;
                if( typeof $(this).data('animation-interval') !== 'undefined') {
                    animationInterval = parseInt($(this).data('animation-interval'));
                }
                var totalAnimations = 0;
                var elements = [];

                element.find('.animate').each(function(n) {
                    elements[n] = $(this);
                    totalAnimations++;
                });
                runAnimationGroup(elements, totalAnimations, 0, animationInterval);

            });
        }
        // Force appear. This is suitable in cases when page is in initial state (not scrolled and not resized)
        $.force_appear();
    };

    //Set all Animated Elements
    var runAnimationGroup = function(element, totalAnimations, actual, animationInterval) {
        if(actual < totalAnimations) {
            var animationOptions = element[actual].data('animation-options');
            setTimeout(function() {
                runAnimationTransition(element[actual], animationOptions);
                actual++;
                runAnimationGroup(element, totalAnimations, actual, animationInterval);
            }, animationInterval);
        }
    };
    var runElementsAnimation = function(element) {
        var animationOptions = element.data('animation-options');
        if( typeof animationOptions == 'undefined') {
            animationOptions = new Object;
            animationOptions.animation = "Fade";
        }
        switch (animationOptions.animation) {
            case 'scaleIn':
                element.css({
                    opacity: 0,
                    scale: 0.6
                });
                break;
            case 'scaleToBottom':

                original_height = element.height();

                element.data('original-height', original_height).data('original-width', element.width()).css({
                    opacity: 0,
                    transform: 'translateY(-' + original_height / 2 + 'px); scaleY(0.001)'
                });
                break;
            case 'scaleToRight':

                original_width = element.width();

                element.data('original-height', element.height()).data('original-width', original_width).css({
                    opacity: 0,
                    transform: 'translateX(-' + original_width / 2 + 'px); scaleX(0.001)'
                });
                break;
            case 'scaleToTop':

                original_height = element.height();

                element.data('original-height', original_height).data('original-width', element.width()).css({
                    opacity: 0,
                    transform: 'translateY(' + original_height / 2 + 'px); scaleY(0.001)'
                });
                break;
            case 'scaleToLeft':

                original_width = element.width();

                element.data('original-height', element.height()).data('original-width', original_width).css({
                    opacity: 0,
                    transform: 'translateX(' + original_width / 2 + 'px); scaleX(0.001)'
                });
                break;
            default :
                element.css({
                    opacity: 0
                });
                break;
        }
    };
    var runAnimateProgressBar = function() {
        if($('.animate-bar').length) {
            $('.animate-bar').appear();
            $(document.body).on('appear', '.animate-bar', function(e, $affected) {
                $(this).progressbar({
                    display_text: 'center',
                    use_percentage: true
                });
            });
        }
    };
    var runAnimationTransition = function(element, animationOptions) {
        if( typeof animationOptions == 'undefined') {
            animationOptions = new Object;
            animationOptions.animation = "fadeIn";
        }
        var animationType = animationOptions.animation;
        var animationDelay = animationOptions.delay;
        var animationDuration = animationOptions.duration;
        var animationEasing = animationOptions.easing;
        if( typeof animationType === 'undefined') {
            animationType = "fadeIn";
        }
        if( typeof animationDelay === 'undefined') {
            animationDelay = 0;
        }
        if( typeof animationDuration === 'undefined') {
            animationDuration = 300;
        }
        if( typeof animationEasing === 'undefined') {
            animationEasing = 'linear';
        }
        switch (animationType) {
            case 'scaleIn':
                element.transition({
                    opacity: 1,
                    scale: 1,
                    duration: animationDuration,
                    delay: animationDelay,
                    easing: animationEasing
                });
                break;
            case 'scaleToRight':
            case 'scaleToLeft':
                element.transition({
                    opacity: 1,
                    transform: 'scaleX(1)',
                    duration: animationDuration,
                    delay: animationDelay,
                    easing: animationEasing
                });
                break;
            case 'scaleToBottom':
            case 'scaleToTop':
                element.transition({
                    opacity: 1,
                    transform: 'scaleY(1)',
                    duration: animationDuration,
                    delay: animationDelay,
                    easing: animationEasing
                });
                break;
            default:

                animationDuration = animationDuration / 1000 * 2 + 's';
                animationDelay = animationDelay / 1000 * 2 + 's';
                element.css({
                    opacity: 1,
                    'animation-fill-mode': 'both',
                    'animation-duration': animationDuration,
                    'animation-delay': animationDelay,
                    'animation-name': animationType
                });
                break;
        }
    };

    var handleAnimations = function() {
        $('.animate-group').each(function() {
            $(this).find('.animate').each(function() {
                runElementsAnimation($(this));
            });
        });
        $('.animate-if-visible').each(function() {
            runElementsAnimation($(this));
        });
    };

    ////////////////////////////////////////
    // START ---------------- Handle Pickers
    ////////////////////////////////////////
    var handleDatePickers = function () {

        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                orientation: "right",
                autoclose: true,
                language: 'zh-cn'
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        /* Workaround to restrict daterange past date select: http://stackoverflow.com/questions/11933173/how-to-restrict-the-selectable-date-ranges-in-bootstrap-datepicker */
    }

    var handleTimePickers = function () {

        if (jQuery().timepicker) {
            $('.timepicker-default').timepicker({
                autoclose: true,
                showSeconds: true,
                minuteStep: 1
            });

            $('.timepicker-no-seconds').timepicker({
                autoclose: true,
                minuteStep: 5
            });

            $('.timepicker-24').timepicker({
                autoclose: true,
                minuteStep: 5,
                showSeconds: false,
                showMeridian: false
            });

            // handle input group button click
            $('.timepicker').parent('.input-group').on('click', '.input-group-btn', function(e){
                e.preventDefault();
                $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
            });
        }
    }

    var handleDateRangePickers = function () {
        if (!jQuery().daterangepicker) {
            return;
        }

        $('#defaultrange').daterangepicker({
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/2018',
            },
            function (start, end) {
                $('#defaultrange input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );        

        $('#defaultrange_modal').daterangepicker({
                opens: 'right',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/2018',
            },
            function (start, end) {
                $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );  

        // this is very important fix when daterangepicker is used in modal. in modal when daterange picker is opened and mouse clicked anywhere bootstrap modal removes the modal-open class from the body element.
        // so the below code will fix this issue.
        $('#defaultrange_modal').on('click', function(){
            if ($('#daterangepicker_modal').is(":visible") && $('body').hasClass("modal-open") == false) {
                $('body').addClass("modal-open");
            }
        });

        
    }

    var handleDatetimePicker = function () {

        if (!jQuery().datetimepicker) {
            return;
        }

        $(".form_datetime").datetimepicker({
            autoclose: true,
            format: "dd MM yyyy - hh:ii",
            pickerPosition: "bottom-left"
        });

        $(".form_advance_datetime").datetimepicker({
            format: "dd MM yyyy - hh:ii",
            autoclose: true,
            todayBtn: true,
            startDate: "2013-02-14 10:00",
            pickerPosition: "bottom-left",
            minuteStep: 10
        });

        $(".form_meridian_datetime").datetimepicker({
            format: "dd MM yyyy - HH:ii P",
            showMeridian: true,
            autoclose: true,
            pickerPosition: "bottom-left",
            todayBtn: true
        });

        $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
    }
    ////////////////////////////////////////
    // Handle Pickers ------------------ END
    ////////////////////////////////////////

    var handleSendPasskey = function () {
        $('.send-passkey').click(function() {
            mobile_field = $(this).attr('mobile');
            mobile_control = $(this).parents('form').find('#' + mobile_field);
            intlTelInput = mobile_control.data('plugin_intlTelInput');
            if(intlTelInput) {
                mobile = intlTelInput.getNumber();
            }
            else 
                mobile = mobile_control.val();

            if (mobile == '') {
                mobile_control.focus();
                errorBox('手机号', '请输入手机号。');
                return;
            }

            callAPI('api/common/send_passkey', {
                phone_num: mobile
            })
            .done(function(res) {
                alertBox("验证码", "成功发送短信验证码。", function() {
                    document.location.reload();
                });
            })
            .fail(function(res) {
                errorBox("失败", "失败发送短信验证码。");
            });
        });
    }

    var handleIntlTelInput = function() {
        var reformatNumber = function(el, with_dial) {
            if (with_dial === undefined)
                with_dial = true;
            intlTelInput = el.data('plugin_intlTelInput');
            fullNumber = intlTelInput.getNumber();
            prefix = "+" + intlTelInput.selectedCountryData.dialCode;
            val = el.val();

            if (fullNumber.indexOf(prefix) == 0) {
                strip_number = fullNumber.substring(prefix.length);
                if (with_dial == false)
                    el.val(strip_number);
            }

            if (with_dial) {
                if (fullNumber == prefix)
                    fullNumber = "";
                el.val(fullNumber);
            }
        }
        $(".input-intl-tel").each(function() {
            $(this).intlTelInput({
                // allowDropdown: false,
                autoHideDialCode: true,
                // autoPlaceholder: "off",
                // dropdownContainer: "body",
                // excludeCountries: ["us"],
                formatOnDisplay: true,
                // geoIpLookup: function(callback) {
                //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                //     var countryCode = (resp && resp.country) ? resp.country : "";
                //     callback(countryCode);
                //   });
                // },
                // hiddenInput: name_full,
                // initialCountry: "auto",
                nationalMode: true,
                // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                placeholderNumberType: "MOBILE",
                preferredCountries: ['cn'],
                separateDialCode: true,
                //utilsScript: "build/js/utils.js"
            });
            $(this).on('change', function() {
                reformatNumber($(this), false);
            });
            $(this).parents('form').addClass('intl-tel-form');

            reformatNumber($(this), false);
        });

        $('.intl-tel-form').on('submit', function() {
            $(this).find('.input-intl-tel').each(function() {
                reformatNumber($(this));
            });

            setTimeout("$('.intl-tel-form').resetTelNumber()", 10);
        });

        $.fn.extend({
            resetTelNumber: function() {
                $(this).find('.input-intl-tel').each(function() {
                    reformatNumber($(this), false);
                });
            }
        });
    }

    return {
        // Main init methods to initialize the layout
        //IMPORTANT!!!: Do not modify the core handlers call order.

        initHeader: function() {
            handleHorizontalMenu(); // handles horizontal menu    
        },

        setSidebarMenuActiveLink: function(mode, el) {
            handleSidebarMenuActiveLink(mode, el);
        },

        initSidebar: function() {
            //layout handlers
            handleFixedSidebar(); // handles fixed sidebar menu
            handleSidebarMenu(); // handles main menu
            handleSidebarToggler(); // handles sidebar hide/show

            addResizeHandler(handleFixedSidebar); // reinitialize fixed sidebar on window resize
        },

        initContent: function() {
            handle100HeightContent(); // handles 100% height elements(block, portlet, etc)
            handleTabs(); // handle bootstrah tabs

            addResizeHandler(handleSidebarAndContentHeight); // recalculate sidebar & content height on window resize
            addResizeHandler(handle100HeightContent); // reinitialize content height on window resize 
        },

        initFooter: function() {
            handleGoTop(); //handles scroll to top functionality in the footer
        },

        init: function (homeBase) {
            handleInit(homeBase);

            //Core handlers
            handleOnResize(); // set and handle responsive 
   
            this.initHeader();
            this.initSidebar();
            this.initContent();
            this.initFooter();
            
            // picker
            handleDatePickers();
            handleTimePickers();
            handleDatetimePicker();
            handleDateRangePickers();

            handleBootstrapSwitch();
            handleFancybox();

            handleMaterialDesign();

            // send passkey through sms
            handleSendPasskey();

            handleIntlTelInput();
        },

        initPopup: function (homeBase) {
            handleInit(homeBase);
            
            // picker
            handleDatePickers();
            handleTimePickers();
            handleDatetimePicker();
            handleDateRangePickers();

            handleBootstrapSwitch();
            handleFancybox();
        },

        initFancybox: function(parent) {
            handleFancybox(parent);
        },

        //public function to fix the sidebar and content height accordingly
        fixContentHeight: function () {
            handleSidebarAndContentHeight();
        },

        initFixedSidebarHoverEffect: function() {
            handleFixedSidebarHoverEffect();
        },

        initFixedSidebar: function() {
            handleFixedSidebar();
        },

        getLayoutImgPath: function () {
            return Metronic.getAssetsPath() + layoutImgPath;
        },

        getLayoutCssPath: function () {
            return Metronic.getAssetsPath() + layoutCssPath;
        },

        initQuickSidebar: function() {
            //layout handlers
            handleQuickSidebarToggler(); // handles quick sidebar's toggler
            handleQuickSidebarChat(); // handles quick sidebar's chats
            handleQuickSidebarAlerts(); // handles quick sidebar's alerts
            handleQuickSidebarSettings(); // handles quick sidebar's setting
        },

        callAPI: function(url, params) {
            return callAPI(url, params);
        }
    };

}();