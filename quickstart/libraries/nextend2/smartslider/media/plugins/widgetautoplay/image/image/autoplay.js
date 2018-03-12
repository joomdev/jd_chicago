(function ($, scope, undefined) {
    "use strict";
    function NextendSmartSliderWidgetAutoplayImage(id, desktopRatio, tabletRatio, mobileRatio) {

        this.slider = window[id];

        this.slider.started($.proxy(this.start, this, id, desktopRatio, tabletRatio, mobileRatio));
    };

    NextendSmartSliderWidgetAutoplayImage.prototype.start = function (id, desktopRatio, tabletRatio, mobileRatio) {

        if (this.slider.sliderElement.data('autoplay')) {
            return false;
        }
        this.slider.sliderElement.data('autoplay', this);

        this.paused = false;

        this.button = this.slider.sliderElement.find('.nextend-autoplay');

        // Autoplay not enabled, so just destroy the widget
        if (this.slider.controls.autoplay._disabled) {
            this.destroy();
        } else {
            if (!this.slider.controls.autoplay.parameters.start) {
                this.paused = true;
                this.setPaused();
            }
            this.deferred = $.Deferred();
            this.slider.sliderElement
                .on({
                    'SliderDevice.n2-widget-autoplay': $.proxy(this.onDevice, this),
                    'autoplayStarted.n2-widget-autoplay': $.proxy(this.setPlaying, this),
                    'autoplayPaused.n2-widget-autoplay': $.proxy(this.setPaused, this),
                    'autoplayDisabled.n2-widget-autoplay': $.proxy(this.destroy, this)
                })
                .trigger('addWidget', this.deferred);

            this.button.on('click', $.proxy(this.switchState, this));

            this.desktopRatio = desktopRatio;
            this.tabletRatio = tabletRatio;
            this.mobileRatio = mobileRatio;

            this.button.imagesLoaded().always($.proxy(this.loaded, this));
        }
    };

    NextendSmartSliderWidgetAutoplayImage.prototype.loaded = function () {
        this.width = this.button.width();
        this.height = this.button.height();

        this.onDevice(null, {device: this.slider.responsive.getDeviceMode()});

        this.deferred.resolve();
    };

    NextendSmartSliderWidgetAutoplayImage.prototype.onDevice = function (e, device) {
        var ratio = 1;
        switch (device.device) {
            case 'tablet':
                ratio = this.tabletRatio;
                break;
            case 'mobile':
                ratio = this.mobileRatio;
                break;
            default:
                ratio = this.desktopRatio;
        }
        this.button.width(this.width * ratio);
        this.button.height(this.height * ratio);
    };

    NextendSmartSliderWidgetAutoplayImage.prototype.switchState = function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (!this.paused) {
            this.setPaused();
            this.slider.sliderElement.triggerHandler('autoplayExtraWait', this);
        } else {
            this.setPlaying();
            this.slider.sliderElement.triggerHandler('autoplayExtraContinue', this);
        }
    };

    NextendSmartSliderWidgetAutoplayImage.prototype.setPaused = function () {
        this.paused = true;
        this.button.addClass('n2-autoplay-paused');
    };

    NextendSmartSliderWidgetAutoplayImage.prototype.setPlaying = function () {
        this.paused = false;
        this.button.removeClass('n2-autoplay-paused');
    };

    NextendSmartSliderWidgetAutoplayImage.prototype.destroy = function () {
        this.slider.sliderElement.off('.n2-widget-autoplay');
        this.button.remove();
    };


    scope.NextendSmartSliderWidgetAutoplayImage = NextendSmartSliderWidgetAutoplayImage;

})(n2, window);