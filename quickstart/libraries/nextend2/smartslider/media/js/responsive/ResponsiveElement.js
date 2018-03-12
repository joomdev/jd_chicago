(function ($, scope, undefined) {

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /**
     * @constructor
     * @param responsive {NextendSmartSliderResponsive} caller object
     * @param group {String}
     * @param element {jQuery}
     * @param cssProperties {Array} Array of properties which will be responsive
     * @param name {String} we will register the changed values for this namespace in the global NextendSmartSliderResponsive objects' responsiveDimensions property
     */
    function NextendSmartSliderResponsiveElement(responsive, group, element, cssProperties, name) {
        this.loadDefaults();
        this._lastRatio = 1;
        this.responsive = responsive;

        this.group = group;

        this.element = element;

        this.lazyload = this.responsive.slider.parameters.lazyload.enabled;

        this._readyDeferred = $.Deferred();

        if (typeof name !== 'undefined') {
            this.name = name;
        } else {
            this.name = null;
        }

        this.tagName = element.prop("tagName");

        this.data = {};

        this.helper = {
            /**
             * Holds the current element's parent element, which is required for the centered mode
             */
            parent: null,
            /**
             * Holds the current element's parent original width and height for images
             */
            parentProps: null,
            /**
             * If font size is enabled for the current element, this will hold the different font sized for the different devices
             */
            fontSize: false,
            /**
             * If this is enabled, the responsive mode will try to position the actual element into the center of the parent element
             */
            centered: false
        };

        if (!this.customLoad) {
            switch (this.tagName) {
                case 'IMG':
                    var parent = element.parent();
                    // The images doesn't have their original(not the real dimension, it is the place
                    // what was taken right after the load) width and height values in the future.
                    // So we will calculate the original size from the parent element size
                    // We will assume that the image was 100% width to its parent
                    this.helper.parentProps = {
                        width: parent.width(),
                        height: parent.height()
                    }
                    // Images might not have proper height and width values when not loaded
                    // Let's wait for them
                    if (this.lazyload) {
                        // Lazy load happens much later than the imagesloaded, but this is why it is lazy :)
                        element.on('lazyloaded', $.proxy(this._lateInitIMG, this, cssProperties));
                    } else {
                        element.imagesLoaded($.proxy(this._lateInitIMG, this, cssProperties));
                    }
                    break;
                // We don't have anything to wait so we can start our later initialization
                default:
                    this._lateInit(cssProperties);
            }
        } else {
            this.customLoad(cssProperties);
        }

    };

    NextendSmartSliderResponsiveElement.prototype.loadDefaults = function () {
        this.customLoad = false;
        this.lazyload = false;
    };

    NextendSmartSliderResponsiveElement.prototype._lateInit = function (cssProperties) {

        this._cssProperties = cssProperties;

        this.reloadDefault();

        /**
         * If font-size is responsive on the element, we init this feature on the element.
         */
        if ($.inArray('fontSize', cssProperties) != -1) {

            this.data['fontSize'] = this.element.data('fontsize');

            this.helper.fontSize = {
                fontSize: this.element.data('fontsize'),
                desktopPortrait: this.element.data('minfontsizedesktopportrait'),
                desktopLandscape: this.element.data('minfontsizedesktoplandscape'),
                tabletPortrait: this.element.data('minfontsizetabletportrait'),
                tabletLandscape: this.element.data('minfontsizetabletlandscape'),
                mobilePortrait: this.element.data('minfontsizemobileportrait'),
                mobileLandscape: this.element.data('minfontsizemobilelandscape')
            };

            // Sets the proper font size for the current mode
            //this.setFontSizeByMode(this.responsive.mode.mode);

            // When the mode changes we have to adjust the original font size value in the data
            this.responsive.sliderElement.on('SliderDeviceOrientation', $.proxy(this.onModeChange, this));
        }

        // Our resource is finished with the loading, so we can enable the normal resize method.
        this.resize = this._resize;

        // We are ready
        this._readyDeferred.resolve();
    };

    NextendSmartSliderResponsiveElement.prototype.reloadDefault = function () {

        for (var i = 0; i < this._cssProperties.length; i++) {
            var propName = this._cssProperties[i];
            this.data[propName] = parseInt(this.element.css(propName));
        }
        if (this.name) {
            var d = this.responsive.responsiveDimensions;
            for (var k in this.data) {
                d['start' + capitalize(this.name) + capitalize(k)] = this.data[k];
            }
        }
    };

    NextendSmartSliderResponsiveElement.prototype._lateInitIMG = function (cssProperties, e) {

        // As our background images has 100% width, we know that the original img size was the same as the parent's width.
        // Then we can calculate the original height of the img as the parent element's ratio might not the same as the background image

        var width = this.element.width(),
            height = this.element.height();

        height = parseInt(this.helper.parentProps.width / width * height);
        width = this.helper.parentProps.width;

        var widthIndex = $.inArray('width', cssProperties);
        if (widthIndex != -1) {
            cssProperties.splice(widthIndex, 1);
            this.data['width'] = width;
        }
        var heightIndex = $.inArray('height', cssProperties);
        if (heightIndex != -1) {
            cssProperties.splice(heightIndex, 1);
            this.data['height'] = height;
        }
        this._lateInit(cssProperties);
    };

    /**
     * You can use it as the normal jQuery ready, except it check for the current element list
     * @param {function} fn
     */
    NextendSmartSliderResponsiveElement.prototype.ready = function (fn) {
        this._readyDeferred.done(fn);
    };

    /**
     * When the element list is not loaded yet, we have to add the current resize call to the ready event.
     * @example You have an image which is not loaded yet, but a resize happens on the browser. We have to make the resize later when the image is ready!
     * @param responsiveDimensions
     * @param ratio
     */
    NextendSmartSliderResponsiveElement.prototype.resize = function (responsiveDimensions, ratio) {
        this.ready($.proxy(this.resize, this, responsiveDimensions, ratio));
        this._lastRatio = ratio;
    };

    NextendSmartSliderResponsiveElement.prototype._resize = function (responsiveDimensions, ratio, timeline, duration) {
        if (this.name && typeof responsiveDimensions[this.name] === 'undefined') {
            responsiveDimensions[this.name] = {};
        }

        var to = {};
        for (var propName in this.data) {
            var value = this.data[propName] * ratio;
            if (typeof this[propName + 'Prepare'] == 'function') {
                value = this[propName + 'Prepare'](value);
            }

            if (this.name) {
                responsiveDimensions[this.name][propName] = value;
            }
            to[propName] = value;
        }
        if (timeline) {
            timeline.to(this.element, duration, to, 0);
        } else {
            this.element.css(to);

            if (this.helper.centered) {
                // when centered feature enabled we have to set the proper margins for the element to make it centered
                if (n2const.isIOS && this.tagName == 'IMG') {
                    // If this fix not applied, IOS might not calculate the correct width and height for the image
                    this.element.css({
                        marginLeft: 1,
                        marginTop: 1
                    });
                }
                this.element.css({
                    marginLeft: parseInt((this.helper.parent.width() - this.element.width()) / 2),
                    marginTop: parseInt((this.helper.parent.height() - this.element.height()) / 2)
                });
            }
        }
        this._lastRatio = ratio;
    };

    NextendSmartSliderResponsiveElement.prototype._refreshResize = function () {
        this.responsive.ready.done($.proxy(function () {
            this._resize(this.responsive.responsiveDimensions, this.responsive.lastRatios[this.group]);
        }, this));
    };

    NextendSmartSliderResponsiveElement.prototype.widthPrepare = function (value) {
        return Math.round(value);
    };

    NextendSmartSliderResponsiveElement.prototype.heightPrepare = function (value) {
        return Math.round(value);
    };

    NextendSmartSliderResponsiveElement.prototype.marginLeftPrepare = function (value) {
        return parseInt(value);
    };

    NextendSmartSliderResponsiveElement.prototype.marginRightPrepare = function (value) {
        return parseInt(value);
    };

    NextendSmartSliderResponsiveElement.prototype.lineHeightPrepare = function (value) {
        return value + 'px';
    };

    NextendSmartSliderResponsiveElement.prototype.fontSizePrepare = function (value) {
        var mode = this.responsive.getNormalizedModeString();
        if (value < this.helper.fontSize[mode]) {
            return this.helper.fontSize[mode];
        }
        return value;
    };

    /**
     * Enables the centered feature on the current element.
     */
    NextendSmartSliderResponsiveElement.prototype.setCentered = function () {
        this.helper.parent = this.element.parent();
        this.helper.centered = true;
    };
    NextendSmartSliderResponsiveElement.prototype.unsetCentered = function () {
        this.helper.centered = false;
    };
    NextendSmartSliderResponsiveElement.prototype.onModeChange = function () {
        this.setFontSizeByMode();
    };

    /**
     * Changes the original font size based on the current mode and also updates the current value on the element.
     * @param mode
     */
    NextendSmartSliderResponsiveElement.prototype.setFontSizeByMode = function () {
        this.element.css('fontSize', this.fontSizePrepare(this.data['fontSize'] * this._lastRatio));
    };
    scope.NextendSmartSliderResponsiveElement = NextendSmartSliderResponsiveElement;


    function NextendSmartSliderResponsiveElementBackgroundImage(responsive, backgroundImage, group, element, cssProperties, name) {

        this.ratio = -1;
        this.relativeRatio = 1;

        this.backgroundImage = backgroundImage;

        NextendSmartSliderResponsiveElement.prototype.constructor.call(this, responsive, group, element, cssProperties, name);

        backgroundImage.addResponsiveElement(this);
    };

    NextendSmartSliderResponsiveElementBackgroundImage.prototype = Object.create(NextendSmartSliderResponsiveElement.prototype);
    NextendSmartSliderResponsiveElementBackgroundImage.prototype.constructor = NextendSmartSliderResponsiveElementBackgroundImage;

    NextendSmartSliderResponsiveElementBackgroundImage.prototype.customLoad = function (cssProperties) {
        var parent = this.element.parent();
        // The images doesn't have their original(not the real dimension, it is the place
        // what was taken right after the load) width and height values in the future.
        // So we will calculate the original size from the parent element size
        // We will assume that the image was 100% width to its parent
        this.helper.parentProps = {
            width: parent.width(),
            height: parent.height()
        }
        this.backgroundImage.afterLoaded().done($.proxy(function () {
            this._lateInitIMG(cssProperties);
        }, this));
    };

    NextendSmartSliderResponsiveElementBackgroundImage.prototype._lateInitIMG = function (cssProperties, e) {
        if (this.backgroundImage.mode == 'fill' || this.backgroundImage.mode == 'fit' || this.backgroundImage.mode == 'simple') {
            this.refreshRatio();
            if (!this.responsive.slider.parameters.dynamicHeight) {
                this.setCentered();
            }
        }

        this._lateInit(cssProperties);
    };

    NextendSmartSliderResponsiveElementBackgroundImage.prototype.afterLoaded = function () {
        if (this.backgroundImage.mode == 'fill' || this.backgroundImage.mode == 'fit' || this.backgroundImage.mode == 'simple') {
            this.refreshRatio();
            if (!this.responsive.slider.parameters.dynamicHeight) {
                this.setCentered();
            }
        }
    };

    NextendSmartSliderResponsiveElementBackgroundImage.prototype._resize = function (responsiveDimensions, ratio, timeline, duration) {
        if (this.responsive.slider.parameters.dynamicHeight) {
            this.element.css({
                width: '100%',
                height: '100%'
            });
        } else {
            var slideOuter = responsiveDimensions.slideouter || responsiveDimensions.slide;

            var slideOuterRatio = slideOuter.width / slideOuter.height;
            if (this.backgroundImage.mode == 'fill') {
                if (slideOuterRatio > this.ratio) {
                    this.element.css({
                        width: '100%',
                        height: 'auto'
                    });
                } else {
                    this.element.css({
                        width: 'auto',
                        height: '100%'
                    });
                }
            } else if (this.backgroundImage.mode == 'fit') {
                if (slideOuterRatio < this.ratio) {
                    this.element.css({
                        width: '100%',
                        height: 'auto'
                    });
                } else {
                    this.element.css({
                        width: 'auto',
                        height: '100%'
                    });
                }
            }
        }

        NextendSmartSliderResponsiveElement.prototype._resize.call(this, responsiveDimensions, ratio, timeline, duration);
    };

    NextendSmartSliderResponsiveElementBackgroundImage.prototype.refreshRatio = function () {
        var w = this.element.prop('naturalWidth'),
            h = this.element.prop('naturalHeight');
        this.ratio = w / h;
        var slideW = this.responsive.responsiveDimensions.startSlideWidth,
            slideH = this.responsive.responsiveDimensions.startSlideHeight;
        this.relativeRatio = (slideW / slideH) / this.ratio;
    };

    scope.NextendSmartSliderResponsiveElementBackgroundImage = NextendSmartSliderResponsiveElementBackgroundImage;

})(n2, window);