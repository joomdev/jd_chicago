(function ($, scope) {

    function NextendExpertMode(app, allowed) {
        this.app = 'system';
        this.key = 'IsExpert';
        this.isExpert = 0;

        this.style = $('<div style="display: none;"></div>').appendTo('body');

        if (!allowed) {
            this.switches = $();
            this.disable(false);
        } else {

            this.switches = $('.n2-expert-switch')
                .on('click', $.proxy(this.switchExpert, this, true));

            this.load();
            if (!this.isExpert) {
                this.disable(false);
            }

            $.jStorage.listenKeyChange(this.app + this.key, $.proxy(this.load, this));
        }
    };

    NextendExpertMode.prototype.load = function () {
        var isExpert = parseInt($.jStorage.get(this.app + this.key, 0));
        if (isExpert != this.isExpert) {
            this.switchExpert(false, false);
        }
    };

    NextendExpertMode.prototype.set = function (value, needSet) {
        this.isExpert = value;
        if (needSet) {
            $.jStorage.set(this.app + this.key, value);
        }
    };

    NextendExpertMode.prototype.switchExpert = function (needSet, e) {
        if (e) {
            e.preventDefault();
        }
        if (!this.isExpert) {
            this.enable(needSet);
        } else {
            this.disable(needSet);
        }
    };

    NextendExpertMode.prototype.measureElement = function () {
        var el = null,
            scrollTop = $(window).scrollTop(),
            cutoff = scrollTop + 62,
            cutoffBottom = scrollTop + $(window).height() - 100;
        $('.n2-content-area > .n2-heading-bar,.n2-content-area > .n2-form-tab ,#n2-admin .n2-content-area form > .n2-form > .n2-form-tab').each(function () {
            var $el = $(this);
            if ($el.offset().top > cutoff) {
                if (!$el.hasClass('n2-heading-bar')) {
                    el = $el;
                }
                return false;
            } else if ($el.offset().top + $el.height() > cutoffBottom) {
                if (!$el.hasClass('n2-heading-bar')) {
                    el = $el;
                }
                return false;
            }
        });
        this.measuredElement = el;
    };

    NextendExpertMode.prototype.scrollToMeasured = function () {

        if (this.measuredElement !== null) {
            while (this.measuredElement.length && !this.measuredElement.is(':VISIBLE')) {
                this.measuredElement = this.measuredElement.prev();
            }
            if (this.measuredElement.length != 0) {
                $('html,body').scrollTop(this.measuredElement.offset().top - 102);
            }
        }
    };

    NextendExpertMode.prototype.enable = function (needSet) {
        this.measureElement();
        this.changeStyle('');
        this.set(1, needSet);
        this.switches.addClass('n2-active');
        $('html').addClass('n2-in-expert');

        if (needSet) {
            this.scrollToMeasured();
        }
    };

    NextendExpertMode.prototype.disable = function (needSet) {
        this.measureElement();
        this.changeStyle('.n2-expert{display: none !important;}');
        this.set(0, needSet);
        this.switches.removeClass('n2-active');
        $('html').removeClass('n2-in-expert');

        if (needSet) {
            this.scrollToMeasured();
        }
    };

    NextendExpertMode.prototype.changeStyle = function (style) {
        this.style.html('<style type="text/css">' + style + '</style>');
    };

    scope.NextendExpertMode = NextendExpertMode

})(n2, window);