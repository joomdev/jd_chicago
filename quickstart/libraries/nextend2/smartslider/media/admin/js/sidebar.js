(function (smartSlider, $, scope, undefined) {

    function NextendSmartSliderSidebar() {
        NextendAdminVerticalPane.prototype.constructor.call(this, $('.n2-layers-tab'), $('#n2-ss-layers-items-list').css('overflow', 'auto'), $('#n2-tabbed-layer-item-animation-tabs > .n2-tabs').css('overflow', 'auto'));

        smartSlider.sidebarManager = this;

        this.panelHeading = $('#layeritemeditorpanel').find('.n2-sidebar-tab-switcher .n2-td');


        var sidebar = $('#n2-ss-slide-sidebar');

        var contentTop = sidebar.parent().siblings('.n2-td').offset().top - $('#wpadminbar, .navbar').height();

        var onScrollCB = $.proxy(function () {
            if ($(window).scrollTop() > contentTop) {
                sidebar.addClass("n2-sidebar-fixed");
            } else {
                sidebar.removeClass("n2-sidebar-fixed");
            }
        }, this);

        sidebar.css({
            width: sidebar.width()
        });

        this.lateInit();
        $(window).scroll(onScrollCB);
        onScrollCB();

        new NextendSmartSliderEditorSidebarSlides();
        new NextendSmartSliderSidebarLayout();
    };

    NextendSmartSliderSidebar.prototype = Object.create(NextendAdminVerticalPane.prototype);
    NextendSmartSliderSidebar.prototype.constructor = NextendSmartSliderSidebar;

    NextendSmartSliderSidebar.prototype.loadDefaults = function () {

        NextendAdminVerticalPane.prototype.loadDefaults.apply(this, arguments);

        this.key = 'smartsliderSlideSidebarRatio';
    };

    NextendSmartSliderSidebar.prototype.switchTab = function (tab) {
        this.panelHeading.eq(tab).trigger('click');
    };

    NextendSmartSliderSidebar.prototype.getExcludedHeight = function () {
        var h = 0;
        h += $('#n2-ss-slide-editor-main-tab').outerHeight();
        h += $('#n2-ss-item-container').outerHeight();
        h += $('#n2-tabbed-layer-item-animation-tabs > .n2-labels').outerHeight();
        h += this.tab.find('.n2-sidebar-pane-sizer').outerHeight();
        h += 1; // border
        return h;
    };
    scope.NextendSmartSliderSidebar = NextendSmartSliderSidebar;

    function NextendSmartSliderEditorSidebarSlides() {

        var tab = $('#n2-ss-slides');

        NextendAdminSinglePane.prototype.constructor.call(this, tab, tab.find('.n2-ss-slides-container').css('overflow', 'auto'));

        $('.n2-slides-tab-label').on('click.n2-slides-init', $.proxy(function (e) {
            this.lateInit();
            $(e.target).off('click.n2-slides-init');
        }, this));
    }

    NextendSmartSliderEditorSidebarSlides.prototype = Object.create(NextendAdminSinglePane.prototype);
    NextendSmartSliderEditorSidebarSlides.prototype.constructor = NextendSmartSliderEditorSidebarSlides;

    NextendSmartSliderEditorSidebarSlides.prototype.getExcludedHeight = function () {
        var h = 0;
        h += $('#n2-ss-slide-editor-main-tab').outerHeight();
        h += $('.n2-slides-tab .n2-definition-list').outerHeight(true);
        h += 2; // border
        return h;
    };

    scope.NextendSmartSliderEditorSidebarSlides = NextendSmartSliderEditorSidebarSlides;


    function NextendSmartSliderSidebarSlides() {

        var tab = $('#n2-ss-slides');

        var sidebar = tab.parents('.n2-sidebar-inner');
        var contentTop = sidebar.parent().siblings('.n2-td').offset().top - $('#wpadminbar, .navbar').height();

        sidebar.css({
            width: sidebar.width()
        });

        $(window).scroll($.proxy(function () {
            if ($(window).scrollTop() > contentTop) {
                sidebar.addClass("n2-sidebar-fixed");
            } else {
                sidebar.removeClass("n2-sidebar-fixed");
            }
        }, this)).trigger('scroll');

        NextendAdminSinglePane.prototype.constructor.call(this, tab, tab.find('.n2-ss-slides-container').css('overflow', 'auto'));

        this.lateInit();
    }

    NextendSmartSliderSidebarSlides.prototype = Object.create(NextendAdminSinglePane.prototype);
    NextendSmartSliderSidebarSlides.prototype.constructor = NextendSmartSliderSidebarSlides;

    NextendSmartSliderSidebarSlides.prototype.getExcludedHeight = function () {
        var h = 0;
        h += $('#n2-ss-slide-editor-main-tab').outerHeight();
        h += $('.n2-sidebar .n2-definition-list').outerHeight(true);
        h += 2; // border
        return h;
    };

    scope.NextendSmartSliderSidebarSlides = NextendSmartSliderSidebarSlides;


    function NextendSmartSliderSidebarLayout() {

        var tab = $('.n2-layouts-tab');

        NextendAdminVerticalPane.prototype.constructor.call(this, tab, tab.find('.n2-lightbox-sidebar-list'), tab.find('.n2-ss-history-list'));

        $('.n2-layouts-tab-label').on('click.n2-layout-init', $.proxy(function (e) {
            this.lateInit();
            $(e.target).off('click.n2-layout-init');
        }, this));
    }

    NextendSmartSliderSidebarLayout.prototype = Object.create(NextendAdminVerticalPane.prototype);
    NextendSmartSliderSidebarLayout.prototype.constructor = NextendSmartSliderSidebarLayout;

    NextendSmartSliderSidebarLayout.prototype.loadDefaults = function () {

        NextendAdminVerticalPane.prototype.loadDefaults.apply(this, arguments);
        this.key = 'smartsliderLayoutSidebarRatio';
    };

    NextendSmartSliderSidebarLayout.prototype.getExcludedHeight = function () {
        var h = 0;
        h += $('#n2-ss-slide-editor-main-tab').outerHeight();
        h += this.tab.find('.n2-sidebar-row').outerHeight() * 2;
        h += this.tab.find(' > div > ul').outerHeight();
        h += this.tab.find('.n2-sidebar-pane-sizer').outerHeight();
        h += 1; // border
        return h;
    };

    scope.NextendSmartSliderSidebarLayout = NextendSmartSliderSidebarLayout;

})(nextend.smartSlider, n2, window);