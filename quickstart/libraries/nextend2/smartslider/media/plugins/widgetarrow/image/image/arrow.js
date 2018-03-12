(function ($, scope, undefined) {
    function NextendSmartSliderWidgetArrowImage(id, desktopRatio, tabletRatio, mobileRatio) {
        this.slider = window[id];

        this.slider.started($.proxy(this.start, this, id, desktopRatio, tabletRatio, mobileRatio));
    };

    NextendSmartSliderWidgetArrowImage.prototype.start = function (id, desktopRatio, tabletRatio, mobileRatio) {
        if (this.slider.sliderElement.data('arrow')) {
            return false;
        }
        this.slider.sliderElement.data('arrow', this);

        this.deferred = $.Deferred();

        this.slider.sliderElement
            .on('SliderDevice', $.proxy(this.onDevice, this))
            .trigger('addWidget', this.deferred);

        this.previous = $('#' + id + '-arrow-previous').on('click', $.proxy(function () {
            this.slider.previous();
        }, this));

        this.previousResize = this.previous.find('.n2-resize');
        if (this.previousResize.length == 0) {
            this.previousResize = this.previous;
        }


        this.next = $('#' + id + '-arrow-next').on('click', $.proxy(function () {
            this.slider.next();
        }, this));

        this.nextResize = this.next.find('.n2-resize');
        if (this.nextResize.length == 0) {
            this.nextResize = this.next;
        }

        this.desktopRatio = desktopRatio;
        this.tabletRatio = tabletRatio;
        this.mobileRatio = mobileRatio;

        $.when(this.previous.imagesLoaded(), this.next.imagesLoaded()).always($.proxy(this.loaded, this));
    };

    NextendSmartSliderWidgetArrowImage.prototype.loaded = function () {
        this.previousWidth = this.previousResize.width();
        this.previousHeight = this.previousResize.height();

        this.nextWidth = this.nextResize.width();
        this.nextHeight = this.nextResize.height();
        this.onDevice(null, {device: this.slider.responsive.getDeviceMode()});

        this.deferred.resolve();
    };

    NextendSmartSliderWidgetArrowImage.prototype.onDevice = function (e, device) {
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
        this.previousResize.width(this.previousWidth * ratio);
        this.previousResize.height(this.previousHeight * ratio);
        this.nextResize.width(this.nextWidth * ratio);
        this.nextResize.height(this.nextHeight * ratio);
    };


    scope.NextendSmartSliderWidgetArrowImage = NextendSmartSliderWidgetArrowImage;
})(n2, window);