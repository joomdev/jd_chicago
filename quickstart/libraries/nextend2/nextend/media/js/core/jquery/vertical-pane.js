(function (smartSlider, $, scope, undefined) {

    function NextendAdminSinglePane(tab, mainPane) {
        this.loadDefaults();

        this.topOffset = $('#wpadminbar, .navbar-fixed-top').height();

        this.tab = tab;
        this.mainPane = mainPane;
    }

    NextendAdminSinglePane.prototype.loadDefaults = function () {
        this.ratio = 1;
        this.excludedHeight = 0;
    };

    NextendAdminSinglePane.prototype.lateInit = function () {
        this.calibrate();

        $(window).on('resize', $.proxy(this.resize, this));
        $(window).one('load', $.proxy(this.calibrate, this));
    };

    NextendAdminSinglePane.prototype.calibrate = function () {
        this.excludedHeight = this.getExcludedHeight();
        this.resize();
    };

    NextendAdminSinglePane.prototype.getExcludedHeight = function () {
        return 0;
    };

    NextendAdminSinglePane.prototype.resize = function () {
        this.targetHeight = window.innerHeight - this.topOffset - this.excludedHeight;
        this.changeRatio(this.ratio);
    };

    NextendAdminSinglePane.prototype.changeRatio = function (ratio) {
        this.mainPane.height(this.targetHeight);
    };

    scope.NextendAdminSinglePane = NextendAdminSinglePane;

    function NextendAdminVerticalPane(tab, mainPane, bottomPane) {

        NextendAdminSinglePane.prototype.constructor.apply(this, arguments);

        if (this.key) {
            this.ratio = $.jStorage.get(this.key, this.ratio);
        }

        this.bottomPane = bottomPane;
    }

    NextendAdminVerticalPane.prototype = Object.create(NextendAdminSinglePane.prototype);
    NextendAdminVerticalPane.prototype.constructor = NextendAdminVerticalPane;

    NextendAdminVerticalPane.prototype.loadDefaults = function () {

        NextendAdminSinglePane.prototype.loadDefaults.apply(this, arguments);

        this.key = false;
        this.ratio = 0.5;
        this.originalRatio = 0.5;
    };

    NextendAdminVerticalPane.prototype.lateInit = function () {

        NextendAdminSinglePane.prototype.lateInit.apply(this, arguments);

        this.tab.find(".n2-sidebar-pane-sizer").draggable({
            axis: 'y',
            scroll: false,
            start: $.proxy(this.start, this),
            drag: $.proxy(this.drag, this)
        });
    };

    NextendAdminVerticalPane.prototype.start = function (event, ui) {
        this.originalRatio = this.ratio;
    };

    NextendAdminVerticalPane.prototype.drag = function (event, ui) {
        var ratio = this.originalRatio + ui.position.top / this.targetHeight;


        if (ratio < 0.1) {
            ratio = 0.1;
            ui.position.top = (ratio - this.originalRatio) * this.targetHeight;
        } else if (ratio > 0.9) {
            ratio = 0.9;
            ui.position.top = (ratio - this.originalRatio) * this.targetHeight;
        }

        this.changeRatio(ratio);

        ui.position.top = 0;
    };

    NextendAdminVerticalPane.prototype.changeRatio = function (ratio) {
        var h = parseInt(this.targetHeight * this.ratio);
        this.mainPane.height(h);
        this.bottomPane.height(this.targetHeight - h - 1);
        this.ratio = ratio;
        if (this.key) {
            $.jStorage.set(this.key, ratio);
        }
    };

    scope.NextendAdminVerticalPane = NextendAdminVerticalPane;

})(nextend.smartSlider, n2, window);