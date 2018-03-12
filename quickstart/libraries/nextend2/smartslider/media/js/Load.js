(function ($, scope, undefined) {

    function NextendSmartSliderLoad(smartSlider, parameters) {
        this.smartSlider = smartSlider;
        this.spinnerKey = '';

        this.id = smartSlider.sliderElement.attr('id');

        this.parameters = $.extend({
            fade: 1,
            scroll: 0,
            spinner: ''
        }, parameters);

        this.spinner = $(this.parameters.spinner);

        this.deferred = $.Deferred();
    };


    NextendSmartSliderLoad.prototype.start = function () {
        if (this.parameters.scroll) {

            var $window = $(window);
            $window.on('scroll.' + this.id, $.proxy(this.onScroll, this));
            this.onScroll();

        } else if (this.parameters.fade) {
            this.loadingArea = $('#' + this.id + '-placeholder').eq(0);
            this.showSpinner('fadePlaceholder');
            n2c.log('Fade on load - start wait');

            $.when(this.smartSlider.responsive.ready, this.smartSlider.backgroundImages.load).done($.proxy(this.showSlider, this));

        } else {
            this.smartSlider.responsive.ready.done($.proxy(function () {
                this.showSlider();
            }, this));
        }
    };

    NextendSmartSliderLoad.prototype.onScroll = function () {
        var $window = $(window);
        if (($window.scrollTop() + $window.height() > (this.smartSlider.sliderElement.offset().top + 100))) {

            n2c.log('Fade on scroll - reached');

            $.when(this.smartSlider.responsive.ready, this.smartSlider.backgroundImages.load).done($.proxy(this.showSlider, this));
            $window.off('scroll.' + this.id);
        }
    };

    NextendSmartSliderLoad.prototype.showSlider = function () {
        n2c.log('Images loaded');

        $.when.apply($, this.smartSlider.widgetDeferreds).done($.proxy(function () {
            n2c.log('Event: BeforeVisible');
            this.smartSlider.responsive.doResize();
            this.smartSlider.sliderElement.trigger('BeforeVisible');

            n2c.log('Fade start');
            this.smartSlider.sliderElement.addClass('n2-ss-loaded');

            this.removeSpinner('fadePlaceholder');
            $('#' + this.id + '-placeholder').remove();
            this.loadingArea = this.smartSlider.sliderElement;

            this.deferred.resolve();
        }, this));
    };

    NextendSmartSliderLoad.prototype.loaded = function (fn) {
        this.deferred.done(fn);
    },

        NextendSmartSliderLoad.prototype.showSpinner = function (spinnerKey) {
            this.spinnerKey = spinnerKey;
            this.spinner.appendTo(this.loadingArea);
        };

    NextendSmartSliderLoad.prototype.removeSpinner = function (spinnerKey) {
        if (this.spinnerKey == spinnerKey) {
            this.spinner.detach();
            this.spinnerKey = '';
        }
    };

    scope.NextendSmartSliderLoad = NextendSmartSliderLoad;

})(n2, window);