(function ($, scope, undefined) {
    function NextendSmartSliderBackgroundImages(slider) {
        this.device = null;

        this.load = $.Deferred();

        this.slider = slider;
        this.slides = this.slider.realSlides;

        this.lazyLoad = slider.parameters.lazyLoad;
        this.lazyLoadNeighbor = slider.parameters.lazyLoadNeighbor;

        this.deviceDeferred = $.Deferred();

        /**
         * @type {NextendSmartSliderBackgroundImage[]}
         */
        this.backgroundImages = [];
        for (var i = 0; i < this.slides.length; i++) {
            var image = this.slides.eq(i).find('.n2-ss-slide-background');
            if (image.length > 0) {
                this.backgroundImages[i] = new NextendSmartSliderBackgroundImage(i, image, this);
            } else {
                this.backgroundImages[i] = false;
            }
            this.slides.eq(i).data('slideBackground', this.backgroundImages[i]);
        }

        this.slider.sliderElement.one('SliderDevice', $.proxy(this.onSlideDeviceChangedFirst, this));

    };

    NextendSmartSliderBackgroundImages.prototype.getBackgroundImages = function () {
        return this.backgroundImages;
    };

    NextendSmartSliderBackgroundImages.prototype.onSlideDeviceChangedFirst = function (e, device) {
        this.onSlideDeviceChanged(e, device);
        this.deviceDeferred.resolve();
        this.slider.sliderElement.on('SliderDevice', $.proxy(this.onSlideDeviceChanged, this));

        if (this.lazyLoad == 1) {
            this.preLoad = this.preLoadLazyNeighbor;

            this.load = $.when(this.preLoad(this.slider.currentSlideIndex));
        } else if (this.lazyLoad == 2) { // delayed
            $(window).load($.proxy(this.preLoadAll, this));

            this.load = $.when(this.preLoad(this.slider.currentSlideIndex));
        } else {
            this.load = $.when.apply($, this.preLoadAll());
        }
    };

    NextendSmartSliderBackgroundImages.prototype.onSlideDeviceChanged = function (e, device) {
        this.device = device;
        for (var i = 0; i < this.backgroundImages.length; i++) {
            if (this.backgroundImages[i]) {
                this.backgroundImages[i].onSlideDeviceChanged(device);
            }
        }
    };

    NextendSmartSliderBackgroundImages.prototype.changed = function (i) {
        if (this.lazyLoad == 1 || this.lazyLoad == 2) {
            if (i == this.slider.currentSlideIndex) {
                this.preLoad(i);
            }
        } else {
            this.preLoad(i);
        }
    };

    NextendSmartSliderBackgroundImages.prototype.preLoadCurrent = function () {
        this.preLoad(this.slider.currentSlideIndex);
    };

    NextendSmartSliderBackgroundImages.prototype.preLoadAll = function () {
        var deferreds = [];
        for (var i = 0; i < this.backgroundImages.length; i++) {
            deferreds.push(this._preLoad(i));
        }
        return deferreds;
    };

    NextendSmartSliderBackgroundImages.prototype.preLoad = function (i) {
        return this._preLoad(i);
    };

    NextendSmartSliderBackgroundImages.prototype.preLoadLazyNeighbor = function (i) {

        var lazyLoadNeighbor = this.lazyLoadNeighbor,
            deferreds = [this._preLoad(i)];

        if (lazyLoadNeighbor) {
            var j = 0,
                k = i;
            while (j < lazyLoadNeighbor) {
                k--;
                if (k < 0) {
                    k = this.backgroundImages.length - 1;
                }
                deferreds.push(this._preLoad(k));
                j++;
            }
            j = 0;
            k = i;
            while (j < lazyLoadNeighbor) {
                k++;
                if (k >= this.backgroundImages.length) {
                    k = 0;
                }
                deferreds.push(this._preLoad(k));
                j++;
            }
        }
        this.slider.load.showSpinner('backgroundImage' + i);
        return $.when.apply($, deferreds).done($.proxy(function () {
            this.slider.load.removeSpinner('backgroundImage' + i);
        }, this));
    };

    NextendSmartSliderBackgroundImages.prototype._preLoad = function (i) {
        if (this.backgroundImages[i]) {
            return this.backgroundImages[i].preLoad();
        } else {
            return true
        }
    };

    NextendSmartSliderBackgroundImages.prototype.hack = function () {
        for (var i = 0; i < this.backgroundImages.length; i++) {
            if (this.backgroundImages[i]) {
                this.backgroundImages[i].hack();
            }
        }
    };

    scope.NextendSmartSliderBackgroundImages = NextendSmartSliderBackgroundImages;

    function NextendSmartSliderBackgroundImage(i, element, manager) {
        this.responsiveElement = false;
        this.loadStarted = false;

        this.i = i;
        this.element = element;
        this.manager = manager;
        this.loadDeferred = $.Deferred();

        var image = element.find('.n2-ss-slide-background-image');
        this.image = image;
        if (image.hasClass('n2-ss-slide-simple')) {
            this.mode = 'simple';
            this.currentSrc = image.attr('src');
        } else if (image.hasClass('n2-ss-slide-fill')) {
            this.mode = 'fill';
            this.currentSrc = image.attr('src');
        } else if (image.hasClass('n2-ss-slide-fit')) {
            this.mode = 'fit';
            this.currentSrc = image.attr('src');
        } else if (image.hasClass('n2-ss-slide-stretch')) {
            this.mode = 'stretch';
            this.currentSrc = image.attr('src');
        } else if (image.hasClass('n2-ss-slide-center')) {
            this.mode = 'center';
            var matches = image.css('backgroundImage').match(/url\(["]*([^)"]+)["]*\)/i);
            if (matches.length > 0) {
                this.currentSrc = matches[1];
            }
        } else if (image.hasClass('n2-ss-slide-tile')) {
            this.mode = 'tile';
            var matches = image.css('backgroundImage').match(/url\(["]*([^)"]+)["]*\)/i);
            if (matches.length > 0) {
                this.currentSrc = matches[1];
            }
        } else {
            this.mode = 'fill';
            this.currentSrc = '';
        }

        this.hash = element.data('hash');
        this.desktopSrc = element.data('desktop');
        this.tabletSrc = element.data('tablet');
        this.mobileSrc = element.data('mobile');
        var opacity = element.data('opacity');
        if (opacity >= 0 && opacity < 1) {
            this.opacity = opacity;
        }

        if (manager.slider.isAdmin) {
            this._change = this.change;
            this.change = this.changeAdmin;
        }

        this.listenImageManager();

    };

    NextendSmartSliderBackgroundImage.prototype.fixNatural = function (DOMelement) {
        var img = new Image();
        img.src = DOMelement.src;
        DOMelement.naturalWidth = img.width;
        DOMelement.naturalHeight = img.height;
    };

    NextendSmartSliderBackgroundImage.prototype.preLoad = function () {
        if (this.loadDeferred.state() == 'pending') {
            this.loadStarted = true;
            this.manager.deviceDeferred.done($.proxy(function () {
                this.onSlideDeviceChanged(this.manager.device);
                this.element.imagesLoaded($.proxy(function () {
                    this.isLoaded = true;
                    var imageNode = this.image[0];
                    if (imageNode.tagName == 'IMG' && typeof imageNode.naturalWidth === 'undefined') {
                        this.fixNatural(imageNode);
                    }
                    this.loadDeferred.resolve(this.element);
                }, this));
            }, this));
        }
        return this.loadDeferred;
    };

    NextendSmartSliderBackgroundImage.prototype.afterLoaded = function () {
        return $.when(this.loadDeferred, this.manager.slider.responsive.ready);
    };

    NextendSmartSliderBackgroundImage.prototype.onSlideDeviceChanged = function (device) {
        var newSrc = this.desktopSrc;
        if (device.device == 'mobile') {
            if (this.mobileSrc) {
                newSrc = this.mobileSrc;
            } else if (this.tabletSrc) {
                newSrc = this.tabletSrc;
            }
        } else if (device.device == 'tablet') {
            if (this.tabletSrc) {
                newSrc = this.tabletSrc;
            }
        }
        this.change(newSrc, '', this.mode);
    };

    /**
     * @param {NextendSmartSliderResponsiveElementBackgroundImage} responsiveElement
     */
    NextendSmartSliderBackgroundImage.prototype.addResponsiveElement = function (responsiveElement) {
        this.responsiveElement = responsiveElement;
    };

    NextendSmartSliderBackgroundImage.prototype.listenImageManager = function () {
        if (this.hash != '') {
            $(window).on(this.hash, $.proxy(this.onImageManagerChanged, this));
        }
    };

    NextendSmartSliderBackgroundImage.prototype.notListenImageManager = function () {
        if (this.hash != '') {
            $(window).off(this.hash, null, $.proxy(this.onImageManagerChanged, this));
        }
    };

    NextendSmartSliderBackgroundImage.prototype.onImageManagerChanged = function (e, imageData) {
        this.tabletSrc = imageData.tablet.image;
        this.mobileSrc = imageData.mobile.image;
        if (this.manager.device.device == 'tablet' || this.manager.device.device == 'mobile') {
            this.onSlideDeviceChanged(this.manager.device);
        }
    };

    NextendSmartSliderBackgroundImage.prototype.changeDesktop = function (src, alt, newMode) {
        this.notListenImageManager();
        this.desktopSrc = src;
        this.hash = md5(src);
        this.change(src, alt, newMode);

        if (src != '') {
            var img = new Image();
            img.addEventListener("load", $.proxy(function () {
                $.when(nextend.imageManager.getVisual(src))
                    .done($.proxy(function (visual) {
                        this.onImageManagerChanged(null, visual.value);
                        this.listenImageManager();
                    }, this));
            }, this), false);
            img.src = nextend.imageHelper.fixed(src);
        } else {
            this.tabletSrc = '';
            this.mobileSrc = '';
        }
    };

    NextendSmartSliderBackgroundImage.prototype.changeAdmin = function (src, alt, newMode) {
        if (this.manager.slider.parameters.dynamicHeight) {
            newMode = 'simple';
        }
        this._change(nextend.imageHelper.fixed(src), alt, newMode);
    };

    NextendSmartSliderBackgroundImage.prototype.change = function (src, alt, newMode) {
        if (this.currentSrc != src || this.mode != newMode) {
            if (this.loadStarted) {
                n2c.log('Slide background changed: ', src);
                var node = null;
                switch (newMode) {
                    case 'simple':
                        node = $('<img src="' + src + '" class="n2-ss-slide-background-image n2-ss-slide-simple" />');
                        break;
                    case 'fill':
                        node = $('<img src="' + src + '" class="n2-ss-slide-background-image n2-ss-slide-fill" />');
                        this.responsiveElement.setCentered();
                        break;
                    case 'fit':
                        node = $('<img src="' + src + '" class="n2-ss-slide-background-image n2-ss-slide-fit" />');
                        this.responsiveElement.setCentered();
                        break;
                    case 'stretch':
                        node = $('<img src="' + src + '" class="n2-ss-slide-background-image n2-ss-slide-stretch" />');
                        this.responsiveElement.unsetCentered();
                        break;
                    case 'center':
                        node = $('<div style="background-image: url(\'' + src + '\');" class="n2-ss-slide-background-image n2-ss-slide-center"></div>');
                        this.responsiveElement.unsetCentered();
                        break;
                    case 'tile':
                        node = $('<div style="background-image: url(\'' + src + '\');" class="n2-ss-slide-background-image n2-ss-slide-tile"></div>');
                        this.responsiveElement.unsetCentered();
                        break;
                }
                if (src == '') {
                    node.css('display', 'none');
                }
                node.css('opacity', this.opacity);
                this.image
                    .replaceWith(node)
                    .remove();
                this.responsiveElement.element = this.image = node;
                this.currentSrc = src;
                this.mode = newMode;

                if (this.loadDeferred.state() == 'pending') {
                    this.loadDeferred.resolve();
                }
                this.loadDeferred = $.Deferred();
                this.manager.changed(this.i);

                switch (newMode) {
                    case 'fill':
                    case 'fit':
                        this.afterLoaded().done($.proxy(function () {
                            this.responsiveElement.afterLoaded();
                            this.responsiveElement.refreshRatio();
                            this.responsiveElement._refreshResize();
                        }, this));
                        break;
                    case 'stretch':
                    case 'center':
                    case 'tile':
                    case 'simple':
                        this.responsiveElement._refreshResize();
                        break;
                }
            }
        }
    };

    NextendSmartSliderBackgroundImage.prototype.setOpacity = function (opacity) {
        this.opacity = opacity;
        this.image.css('opacity', opacity);
    };

    NextendSmartSliderBackgroundImage.prototype.hack = function () {
        NextendTween.set(this.element, {
            rotation: 0.0001
        });
    };

    scope.NextendSmartSliderBackgroundImage = NextendSmartSliderBackgroundImage;

})(n2, window);