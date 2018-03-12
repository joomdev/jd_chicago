(function ($, scope, undefined) {

    var isTablet = null,
        isMobile = null;

    function NextendSmartSliderResponsive(slider, parameters) {
        if (slider.isAdmin) {
            this.doResize = NextendThrottle(this.doResize, 50);
        }

        if (typeof nextend.fontsDeferred === 'undefined') {
            this.triggerResize = this._triggerResize;
        }


        this.fixedEditRatio = 1;
        this.normalizeTimeout = null;
        this.delayedResizeAdded = false;

        this.deviceMode = NextendSmartSliderResponsive.DeviceMode.UNKNOWN;
        this.orientationMode = NextendSmartSliderResponsive.OrientationMode.SCREEN;
        this.orientation = NextendSmartSliderResponsive.DeviceOrientation.UNKNOWN;
        this.lastRatios = {
            ratio: -1
        };
        this.normalizedMode = 'unknownUnknown';

        slider.responsive = this;

        this.widgetMargins = {
            Top: [],
            Right: [],
            Bottom: [],
            Left: []
        };
        this.staticSizes = {
            paddingTop: 0,
            paddingRight: 0,
            paddingBottom: 0,
            paddingLeft: 0
        };
        this.enabledWidgetMargins = [];

        this.slider = slider;
        this.sliderElement = slider.sliderElement;

        var ready = this.ready = $.Deferred();
        this.sliderElement.one('SliderResize', function () {
            ready.resolve();
        });

        this.containerElementPadding = this.sliderElement.parent();
        this.containerElement = this.containerElementPadding.parent();
        this.parameters = $.extend({
            desktop: 1,
            tablet: 1,
            mobile: 1,

            onResizeEnabled: true,
            type: 'auto',
            downscale: true,
            upscale: false,
            minimumHeight: 0,
            maximumHeight: 0,
            minimumHeightRatio: 0,
            maximumHeightRatio: {
                desktopLandscape: 0,
                desktopPortrait: 0,
                mobileLandscape: 0,
                mobilePortrait: 0,
                tabletLandscape: 0,
                tabletPortrait: 0
            },
            maximumSlideWidth: 0,
            maximumSlideWidthLandscape: 0,
            maximumSlideWidthRatio: -1,
            maximumSlideWidthTablet: 0,
            maximumSlideWidthTabletLandscape: 0,
            maximumSlideWidthMobile: 0,
            maximumSlideWidthMobileLandscape: 0,
            maximumSlideWidthConstrainHeight: 0,
            forceFull: 0,
            verticalOffsetSelectors: '',

            focusUser: 0,
            focusAutoplay: 0,

            deviceModes: {
                desktopLandscape: 1,
                desktopPortrait: 0,
                mobileLandscape: 0,
                mobilePortrait: 0,
                tabletLandscape: 0,
                tabletPortrait: 0
            },
            normalizedDeviceModes: {
                unknownUnknown: ["unknown", "Unknown"],
                desktopPortrait: ["desktop", "Portrait"]
            },
            verticalRatioModifiers: {
                unknownUnknown: 1,
                desktopLandscape: 1,
                desktopPortrait: 1,
                mobileLandscape: 1,
                mobilePortrait: 1,
                tabletLandscape: 1,
                tabletPortrait: 1
            },
            minimumFontSizes: {
                desktopLandscape: 0,
                desktopPortrait: 0,
                mobileLandscape: 0,
                mobilePortrait: 0,
                tabletLandscape: 0,
                tabletPortrait: 0
            },
            ratioToDevice: {
                Portrait: {
                    tablet: 0,
                    mobile: 0
                },
                Landscape: {
                    tablet: 0,
                    mobile: 0
                }
            },
            sliderWidthToDevice: {
                desktopLandscape: 0,
                desktopPortrait: 0,
                mobileLandscape: 0,
                mobilePortrait: 0,
                tabletLandscape: 0,
                tabletPortrait: 0
            },

            basedOn: 'combined',
            desktopPortraitScreenWidth: 1200,
            tabletPortraitScreenWidth: 800,
            mobilePortraitScreenWidth: 440,
            tabletLandscapeScreenWidth: 1024,
            mobileLandscapeScreenWidth: 740,
            orientationMode: 'width_and_height'
        }, parameters);

        if (this.parameters.orientationMode == 'width') {
            this.orientationMode = NextendSmartSliderResponsive.OrientationMode.SCREEN_WIDTH_ONLY;
        }

        nextend.smallestZoom = Math.min(Math.max(parameters.sliderWidthToDevice.mobilePortrait, 120), 380);

        switch (this.parameters.basedOn) {
            case 'screen':
                break;
            default:
                if (isTablet == null) {
                    var md = new MobileDetect(window.navigator.userAgent);
                    isTablet = !!md.tablet();
                    isMobile = !!md.phone();
                }
        }

        if (!this.slider.isAdmin) {
            if (!this.parameters.desktop || !this.parameters.tablet || !this.parameters.mobile) {
                if (isTablet == null) {
                    var md = new MobileDetect(window.navigator.userAgent);
                    isTablet = !!md.tablet();
                    isMobile = !!md.phone();
                }
                if (!this.parameters.mobile && isMobile || !this.parameters.tablet && isTablet || !this.parameters.desktop && !isTablet && !isMobile) {
                    this.slider.kill();
                    return;
                }
            }
        }

        this.verticalOffsetSelectors = $(this.parameters.verticalOffsetSelectors);

        n2c.log('Responsive: Store defaults');
        this.storeDefaults();

        if (this.parameters.minimumHeight > 0) {
            this.parameters.minimumHeightRatio = this.parameters.minimumHeight / this.responsiveDimensions.startHeight;
        }

        if (this.parameters.maximumHeight > 0 && this.parameters.maximumHeight >= this.parameters.minimumHeight) {
            this.parameters.maximumHeightRatio = {
                desktopPortrait: this.parameters.maximumHeight / this.responsiveDimensions.startHeight
            };
            this.parameters.maximumHeightRatio.desktopLandscape = this.parameters.maximumHeightRatio.desktopPortrait;
            this.parameters.maximumHeightRatio.tabletPortrait = this.parameters.maximumHeightRatio.desktopPortrait;
            this.parameters.maximumHeightRatio.tabletLandscape = this.parameters.maximumHeightRatio.desktopPortrait;
            this.parameters.maximumHeightRatio.mobilePortrait = this.parameters.maximumHeightRatio.desktopPortrait;
            this.parameters.maximumHeightRatio.mobileLandscape = this.parameters.maximumHeightRatio.desktopPortrait;
        }

        if (this.parameters.maximumSlideWidth > 0) {
            this.parameters.maximumSlideWidthRatio = {
                desktopPortrait: this.parameters.maximumSlideWidth / this.responsiveDimensions.startSlideWidth,
                desktopLandscape: this.parameters.maximumSlideWidthLandscape / this.responsiveDimensions.startSlideWidth,
                tabletPortrait: this.parameters.maximumSlideWidthTablet / this.responsiveDimensions.startSlideWidth,
                tabletLandscape: this.parameters.maximumSlideWidthTabletLandscape / this.responsiveDimensions.startSlideWidth,
                mobilePortrait: this.parameters.maximumSlideWidthMobile / this.responsiveDimensions.startSlideWidth,
                mobileLandscape: this.parameters.maximumSlideWidthMobileLandscape / this.responsiveDimensions.startSlideWidth
            }

            if (this.parameters.maximumSlideWidthConstrainHeight) {
                this.parameters.maximumHeightRatio = this.parameters.maximumSlideWidthRatio;
            }
        }

        n2c.log('Responsive: First resize');
        if (typeof nextend !== 'undefined' && typeof nextend['ssBeforeResponsive'] !== 'undefined') {
            nextend['ssBeforeResponsive'].call(this);
        }

        this.onResize();
        if (this.parameters.onResizeEnabled || this.parameters.type == 'adaptive') {
            $(window).on('resize', $.proxy(this.onResize, this));
        }
    };

    NextendSmartSliderResponsive.OrientationMode = {
        SCREEN: 0,
        ADMIN_LANDSCAPE: 1,
        ADMIN_PORTRAIT: 2,
        SCREEN_WIDTH_ONLY: 3
    };
    NextendSmartSliderResponsive.DeviceOrientation = {
        UNKNOWN: 0,
        LANDSCAPE: 1,
        PORTRAIT: 2
    };
    NextendSmartSliderResponsive._DeviceOrientation = {
        0: 'Unknown',
        1: 'Landscape',
        2: 'Portrait'
    };
    NextendSmartSliderResponsive.DeviceMode = {
        UNKNOWN: 0,
        DESKTOP: 1,
        TABLET: 2,
        MOBILE: 3
    };
    NextendSmartSliderResponsive._DeviceMode = {
        0: 'unknown',
        1: 'desktop',
        2: 'tablet',
        3: 'mobile'
    };

    NextendSmartSliderResponsive.prototype.getOuterWidth = function () {
        var rd = this.responsiveDimensions;
        return rd.startSliderWidth + rd.startSliderMarginLeft + rd.startSliderMarginRight;
    };

    NextendSmartSliderResponsive.prototype.storeDefaults = function () {

        // We should use outerWidth(true) as we need proper margin calculation for the ratio
        this.responsiveDimensions = {
            startWidth: this.sliderElement.outerWidth(true),
            startHeight: this.sliderElement.outerHeight(true)
        };

        /**
         * @type {NextendSmartSliderResponsiveElement[]}
         */
        this.responsiveElements = [];

        this.helperElements = {};

        this.addResponsiveElements();

        this.margins = {
            top: this.responsiveDimensions.startSliderMarginTop,
            right: this.responsiveDimensions.startSliderMarginRight,
            bottom: this.responsiveDimensions.startSliderMarginBottom,
            left: this.responsiveDimensions.startSliderMarginLeft
        }
    };

    /**
     * @abstract
     */
    NextendSmartSliderResponsive.prototype.addResponsiveElements = function () {
    };

    /**
     * Add an element list as a single element. Other elements in the list will get the same property as the first element.
     * @param element
     * @param cssproperties
     * @param name
     */
    NextendSmartSliderResponsive.prototype.addResponsiveElement = function (element, cssproperties, group, name) {
        if (typeof group === 'undefined' || !group) {
            group = 'ratio';
        }
        var responsiveElement = new NextendSmartSliderResponsiveElement(this, group, element, cssproperties, name);
        this.responsiveElements.push(responsiveElement);
        return responsiveElement;
    };

    NextendSmartSliderResponsive.prototype.addResponsiveElementBackgroundImage = function (element, backgroundImage, cssproperties, group, name) {
        if (typeof group === 'undefined' || !group) {
            group = 'ratio';
        }
        var responsiveElement = new NextendSmartSliderResponsiveElementBackgroundImage(this, backgroundImage, group, element, cssproperties, name);
        this.responsiveElements.push(responsiveElement);
        return responsiveElement;
    };

    /**
     * Add each element from the list as a single element. It is good for image list as every image might have different dimensions
     * @param elements
     * @param cssproperties
     * @param name
     */
    NextendSmartSliderResponsive.prototype.addResponsiveElementAsSingle = function (elements, cssproperties, group, name) {
        var responsiveElements = [];
        for (var i = 0; i < elements.length; i++) {
            responsiveElements.push(this.addResponsiveElement(elements.eq(i), cssproperties.slice(0), group, name));
        }
        return responsiveElements;
    };

    NextendSmartSliderResponsive.prototype.addResponsiveElementBackgroundImageAsSingle = function (elements, backgroundImage, cssproperties, group, name) {
        var responsiveElements = [];
        for (var i = 0; i < elements.length; i++) {
            responsiveElements.push(this.addResponsiveElementBackgroundImage(elements.eq(i), backgroundImage, cssproperties.slice(0), group, name));
        }
        return responsiveElements;
    };

    NextendSmartSliderResponsive.prototype.resizeResponsiveElements = function (ratios, timeline, duration) {
        for (var i = 0; i < this.responsiveElements.length; i++) {
            var responsiveElement = this.responsiveElements[i];
            if (typeof ratios[responsiveElement.group] === 'undefined') {
                console.log('error with ' + responsiveElement.group);
            }
            responsiveElement.resize(this.responsiveDimensions, ratios[responsiveElement.group], timeline, duration);
        }
    };

    NextendSmartSliderResponsive.prototype.getDeviceMode = function () {
        return NextendSmartSliderResponsive._DeviceMode[this.deviceMode];
    };

    NextendSmartSliderResponsive.prototype.getDeviceModeOrientation = function () {
        return NextendSmartSliderResponsive._DeviceMode[this.deviceMode] + NextendSmartSliderResponsive._DeviceOrientation[this.orientation];
    };

    NextendSmartSliderResponsive.prototype.onResize = function () {
        if (this.slider.mainAnimation.getState() == 'ended') {
            this.doResize();
        } else if (!this.delayedResizeAdded) {
            this.delayedResizeAdded = true;
            this.sliderElement.on('mainAnimationComplete.responsive', $.proxy(this._doDelayedResize, this));
        }
    };

    NextendSmartSliderResponsive.prototype._doDelayedResize = function () {
        this.doResize();
        this.delayedResizeAdded = false;
    };


    NextendSmartSliderResponsive.prototype.doNormalizedResize = function () {
        if (this.normalizeTimeout) {
            clearTimeout(this.normalizeTimeout);
        }

        this.normalizeTimeout = setTimeout($.proxy(this.doResize, this), 10);
    };

    NextendSmartSliderResponsive.prototype._getOrientation = function () {
        if (this.orientationMode == NextendSmartSliderResponsive.OrientationMode.SCREEN) {
            if (window.innerHeight <= window.innerWidth) {
                return NextendSmartSliderResponsive.DeviceOrientation.LANDSCAPE;
            } else {
                return NextendSmartSliderResponsive.DeviceOrientation.PORTRAIT;
            }
        } else if (this.orientationMode == NextendSmartSliderResponsive.OrientationMode.ADMIN_PORTRAIT) {
            return NextendSmartSliderResponsive.DeviceOrientation.PORTRAIT;
        } else if (this.orientationMode == NextendSmartSliderResponsive.OrientationMode.ADMIN_LANDSCAPE) {
            return NextendSmartSliderResponsive.DeviceOrientation.LANDSCAPE;
        }
    };

    NextendSmartSliderResponsive.prototype._getDevice = function () {
        switch (this.parameters.basedOn) {
            case 'combined':
                return this._getDeviceDevice(this._getDeviceScreenWidth());
            case 'device':
                return this._getDeviceDevice(NextendSmartSliderResponsive.DeviceMode.DESKTOP);
            case 'screen':
                return this._getDeviceScreenWidth();
        }
    };

    NextendSmartSliderResponsive.prototype._getDeviceScreenWidth = function () {
        var viewportWidth = window.innerWidth;
        if (this.orientation == NextendSmartSliderResponsive.DeviceOrientation.PORTRAIT) {
            if (viewportWidth < this.parameters.mobilePortraitScreenWidth) {
                return NextendSmartSliderResponsive.DeviceMode.MOBILE;
            } else if (viewportWidth < this.parameters.tabletPortraitScreenWidth) {
                return NextendSmartSliderResponsive.DeviceMode.TABLET;
            }
        } else {
            if (viewportWidth < this.parameters.mobileLandscapeScreenWidth) {
                return NextendSmartSliderResponsive.DeviceMode.MOBILE;
            } else if (viewportWidth < this.parameters.tabletLandscapeScreenWidth) {
                return NextendSmartSliderResponsive.DeviceMode.TABLET;
            }
        }
        return NextendSmartSliderResponsive.DeviceMode.DESKTOP;
    };

    NextendSmartSliderResponsive.prototype._getDeviceAndOrientationByScreenWidth = function () {
        var viewportWidth = window.innerWidth;
        if (viewportWidth < this.parameters.mobilePortraitScreenWidth) {
            return [NextendSmartSliderResponsive.DeviceMode.MOBILE, NextendSmartSliderResponsive.DeviceOrientation.PORTRAIT];
        } else if (viewportWidth < this.parameters.mobileLandscapeScreenWidth) {
            return [NextendSmartSliderResponsive.DeviceMode.MOBILE, NextendSmartSliderResponsive.DeviceOrientation.LANDSCAPE];
        } else if (viewportWidth < this.parameters.tabletPortraitScreenWidth) {
            return [NextendSmartSliderResponsive.DeviceMode.TABLET, NextendSmartSliderResponsive.DeviceOrientation.PORTRAIT];
        } else if (viewportWidth < this.parameters.tabletLandscapeScreenWidth) {
            return [NextendSmartSliderResponsive.DeviceMode.TABLET, NextendSmartSliderResponsive.DeviceOrientation.LANDSCAPE];
        } else if (viewportWidth < this.parameters.desktopPortraitScreenWidth) {
            return [NextendSmartSliderResponsive.DeviceMode.DESKTOP, NextendSmartSliderResponsive.DeviceOrientation.PORTRAIT];
        }
        return [NextendSmartSliderResponsive.DeviceMode.DESKTOP, NextendSmartSliderResponsive.DeviceOrientation.LANDSCAPE];
    };

    NextendSmartSliderResponsive.prototype._getDeviceDevice = function (device) {
        if (isMobile === true) {
            return NextendSmartSliderResponsive.DeviceMode.MOBILE;
        } else if (isTablet && device != NextendSmartSliderResponsive.DeviceMode.MOBILE) {
            return NextendSmartSliderResponsive.DeviceMode.TABLET;
        }
        return device;
    };

    NextendSmartSliderResponsive.prototype._getDeviceZoom = function (ratio) {
        var orientation;
        if (this.orientationMode == NextendSmartSliderResponsive.OrientationMode.ADMIN_PORTRAIT) {
            orientation = NextendSmartSliderResponsive.DeviceOrientation.PORTRAIT;
        } else if (this.orientationMode == NextendSmartSliderResponsive.OrientationMode.ADMIN_LANDSCAPE) {
            orientation = NextendSmartSliderResponsive.DeviceOrientation.LANDSCAPE;
        }
        var targetMode = NextendSmartSliderResponsive.DeviceMode.DESKTOP;
        if (ratio <= this.parameters.ratioToDevice[NextendSmartSliderResponsive._DeviceOrientation[orientation]].mobile) {
            targetMode = NextendSmartSliderResponsive.DeviceMode.MOBILE;
        } else if (ratio <= this.parameters.ratioToDevice[NextendSmartSliderResponsive._DeviceOrientation[orientation]].tablet) {
            targetMode = NextendSmartSliderResponsive.DeviceMode.TABLET;
        }
        return targetMode;
    };

    NextendSmartSliderResponsive.prototype.reTriggerSliderDeviceOrientation = function () {
        var normalized = this._normalizeMode(NextendSmartSliderResponsive._DeviceMode[this.deviceMode], NextendSmartSliderResponsive._DeviceOrientation[this.orientation]);
        this.sliderElement.trigger('SliderDeviceOrientation', {
            lastDevice: normalized[0],
            lastOrientation: normalized[1],
            device: normalized[0],
            orientation: normalized[1]
        });
    };

    NextendSmartSliderResponsive.prototype.doResize = function (fixedMode, timeline, nextSlideIndex, duration) {

        // required to force recalculate if the thumbnails widget get hidden.
        this.refreshMargin();

        if (!this.slider.isAdmin) {
            if (this.parameters.forceFull) {
                $('body').css('overflow-x', 'hidden');
                var outerEl = this.containerElement.parent();
                this.containerElement.css('marginLeft', -outerEl.offset().left - parseInt(outerEl.css('paddingLeft')) - parseInt(outerEl.css('borderLeftWidth'))).width(document.body.clientWidth || document.documentElement.clientWidth);
            }
        }
        var ratio = this.containerElementPadding.width() / this.getOuterWidth();


        var hasOrientationOrDeviceChange = false,
            lastOrientation = this.orientation,
            lastDevice = this.deviceMode,
            targetOrientation = null,
            targetMode = null;

        if (this.orientationMode == NextendSmartSliderResponsive.OrientationMode.SCREEN_WIDTH_ONLY) {
            var deviceOrientation = this._getDeviceAndOrientationByScreenWidth();
            targetMode = deviceOrientation[0]
            targetOrientation = deviceOrientation[1];
        } else {
            targetOrientation = this._getOrientation()
        }

        if (this.orientation != targetOrientation) {
            this.orientation = targetOrientation;
            hasOrientationOrDeviceChange = true;
            n2c.log('Event: SliderOrientation', {
                lastOrientation: NextendSmartSliderResponsive._DeviceOrientation[lastOrientation],
                orientation: NextendSmartSliderResponsive._DeviceOrientation[targetOrientation]
            });
            this.sliderElement.trigger('SliderOrientation', {
                lastOrientation: NextendSmartSliderResponsive._DeviceOrientation[lastOrientation],
                orientation: NextendSmartSliderResponsive._DeviceOrientation[targetOrientation]
            });
        }

        if (!fixedMode) {
            if (this.orientationMode != NextendSmartSliderResponsive.OrientationMode.SCREEN_WIDTH_ONLY) {
                targetMode = this._getDevice(ratio);
            }

            if (this.deviceMode != targetMode) {
                this.deviceMode = targetMode;
                this.sliderElement.removeClass('n2-ss-' + NextendSmartSliderResponsive._DeviceMode[lastDevice])
                    .addClass('n2-ss-' + NextendSmartSliderResponsive._DeviceMode[targetMode]);
                n2c.log('Event: SliderDevice', {
                    lastDevice: NextendSmartSliderResponsive._DeviceMode[lastDevice],
                    device: NextendSmartSliderResponsive._DeviceMode[targetMode]
                });
                this.sliderElement.trigger('SliderDevice', {
                    lastDevice: NextendSmartSliderResponsive._DeviceMode[lastDevice],
                    device: NextendSmartSliderResponsive._DeviceMode[targetMode]
                });
                hasOrientationOrDeviceChange = true;
            }
        }

        if (!this.slider.isAdmin) {
            if (this.parameters.type == 'fullpage') {
                this.parameters.maximumHeightRatio[this.getDeviceModeOrientation()] = this.parameters.minimumHeightRatio = ((document.documentElement.clientHeight || document.body.clientHeight) - this.getVerticalOffsetHeight()) / this.responsiveDimensions.startHeight;
            }
        }

        if (hasOrientationOrDeviceChange) {
            var lastNormalized = this._normalizeMode(NextendSmartSliderResponsive._DeviceMode[lastDevice], NextendSmartSliderResponsive._DeviceOrientation[lastOrientation]),
                normalized = this._normalizeMode(NextendSmartSliderResponsive._DeviceMode[this.deviceMode], NextendSmartSliderResponsive._DeviceOrientation[this.orientation]);

            if (lastNormalized[0] != normalized[0] || lastNormalized[1] != normalized[1]) {
                this.normalizedMode = normalized[0] + normalized[1];
                n2c.log('Event: SliderDeviceOrientation', {
                    lastDevice: lastNormalized[0],
                    lastOrientation: lastNormalized[1],
                    device: normalized[0],
                    orientation: normalized[1]
                });
                this.sliderElement.trigger('SliderDeviceOrientation', {
                    lastDevice: lastNormalized[0],
                    lastOrientation: lastNormalized[1],
                    device: normalized[0],
                    orientation: normalized[1]
                });
            }
        }
        /*
         if (this.parameters.type == 'adaptive') {
         this._doResize(this.parameters.sliderWidthToDevice[this.normalizedMode] / this.parameters.sliderWidthToDevice.desktopPortrait);
         } else {
         */
        var zeroRatio = this.parameters.sliderWidthToDevice[this.normalizedMode] / this.parameters.sliderWidthToDevice.desktopPortrait;
        if (!this.parameters.downscale && ratio < zeroRatio) {
            ratio = zeroRatio;
        } else if (!this.parameters.upscale && ratio > zeroRatio) {
            ratio = zeroRatio;
        }
        this._doResize(ratio, timeline, nextSlideIndex, duration);
        //}
    };

    NextendSmartSliderResponsive.prototype._normalizeMode = function (device, orientation) {
        return this.parameters.normalizedDeviceModes[device + orientation];
    };

    NextendSmartSliderResponsive.prototype.getNormalizedModeString = function () {
        var normalized = this._normalizeMode(NextendSmartSliderResponsive._DeviceMode[this.deviceMode], NextendSmartSliderResponsive._DeviceOrientation[this.orientation]);
        return normalized.join('');
    };

    NextendSmartSliderResponsive.prototype.getModeString = function () {
        return NextendSmartSliderResponsive._DeviceMode[this.deviceMode] + NextendSmartSliderResponsive._DeviceOrientation[this.orientation];
    };

    NextendSmartSliderResponsive.prototype.isEnabled = function (device, orientation) {
        return this.parameters.deviceModes[device + orientation];
    };

    NextendSmartSliderResponsive.prototype._doResize = function (ratio, timeline, nextSlideIndex, duration) {
        var ratios = {
            ratio: ratio,
            w: ratio,
            h: ratio,
            slideW: ratio,
            slideH: ratio,
            fontRatio: 1
        };

        this._buildRatios(ratios, this.slider.parameters.dynamicHeight, nextSlideIndex);
        /*
         if (this.fixedEditRatio && this.slider.isAdmin) {
         ratios.w = ratios.slideW;
         ratios.h = ratios.slideH;
         }
         */
        ratios.fontRatio = ratios.slideW;


        var isChanged = false;
        for (var k in ratios) {
            if (ratios[k] != this.lastRatios[k]) {
                isChanged = true;
                break;
            }
        }

        if (isChanged) {
            this.resizeResponsiveElements(ratios, timeline, duration);
            this.lastRatios = ratios;

            if (timeline) {
                this.sliderElement.trigger('SliderAnimatedResize', [ratios, timeline, duration]);
                timeline.eventCallback("onComplete", function () {
                    this.triggerResize(ratios, timeline);
                }, [], this);
            } else {
                this.triggerResize(ratios, timeline);
            }
        }
    };

    NextendSmartSliderResponsive.prototype.triggerResize = function (ratios, timeline) {
        nextend.fontsDeferred.done($.proxy(function () {
            this.triggerResize = this._triggerResize;
            this._triggerResize(ratios, timeline);
        }, this));
    };

    NextendSmartSliderResponsive.prototype._triggerResize = function (ratios, timeline) {
        n2c.log('Event: SliderResize', ratios);
        this.sliderElement.trigger('SliderResize', [ratios, this, timeline]);
    };

    NextendSmartSliderResponsive.prototype._buildRatios = function (ratios, dynamicHeight, nextSlideIndex) {
        if (this.parameters.minimumHeightRatio > 0) {
            ratios.h = Math.max(ratios.h, this.parameters.minimumHeightRatio);
        }

        var deviceModeOrientation = this.getDeviceModeOrientation();

        if (this.parameters.maximumHeightRatio[deviceModeOrientation] > 0) {
            ratios.h = Math.min(ratios.h, this.parameters.maximumHeightRatio[deviceModeOrientation]);
        }

        if (this.parameters.maximumSlideWidthRatio[deviceModeOrientation] > 0 && ratios.slideW > this.parameters.maximumSlideWidthRatio[deviceModeOrientation]) {
            ratios.slideW = this.parameters.maximumSlideWidthRatio[deviceModeOrientation];
        }

        ratios.slideW = ratios.slideH = Math.min(ratios.slideW, ratios.slideH);

        var verticalRatioModifier = this.parameters.verticalRatioModifiers[deviceModeOrientation];
        ratios.slideH *= verticalRatioModifier;
        if (this.parameters.type == 'fullpage') {
            if (ratios.slideH > ratios.h) {
                ratios.slideW /= ratios.slideH / ratios.h;
            }
            ratios.slideH = Math.min(ratios.slideH, ratios.h);

            if (this.slider.isAdmin) {
                ratios.w = ratios.slideW;
                ratios.h = ratios.slideH;
            }
        } else {
            ratios.h *= verticalRatioModifier;
            ratios.slideH = Math.min(ratios.slideH, ratios.h);
            //if (this.parameters.type == 'showcase') {
            // Constrain slide ratio in showcase type
            ratios.slideW = ratios.slideH / verticalRatioModifier;
            //}
        }

        var slideIndex = this.slider.currentSlideIndex;
        if (typeof nextSlideIndex !== 'undefined') {
            slideIndex = nextSlideIndex;
        }

        if (dynamicHeight) {
            var backgroundRatio = this.slider.backgroundImages.backgroundImages[slideIndex].responsiveElement.relativeRatio;
            if (backgroundRatio != -1) {
                ratios.slideH *= backgroundRatio;
                ratios.h *= backgroundRatio;
            }
        }

        this.sliderElement.triggerHandler('responsiveBuildRatios', [ratios]);
    };

    NextendSmartSliderResponsive.prototype.setOrientation = function (newOrientation) {
        if (newOrientation == 'portrait') {
            this.orientationMode = NextendSmartSliderResponsive.OrientationMode.ADMIN_PORTRAIT;
        } else if (newOrientation == 'landscape') {
            this.orientationMode = NextendSmartSliderResponsive.OrientationMode.ADMIN_LANDSCAPE;
        }
    };

    NextendSmartSliderResponsive.prototype.setMode = function (newMode) {
        var orientation;
        if (this.orientationMode == NextendSmartSliderResponsive.OrientationMode.ADMIN_PORTRAIT) {
            orientation = NextendSmartSliderResponsive.DeviceOrientation.PORTRAIT;
        } else if (this.orientationMode == NextendSmartSliderResponsive.OrientationMode.ADMIN_LANDSCAPE) {
            orientation = NextendSmartSliderResponsive.DeviceOrientation.LANDSCAPE;
        }
        var width = this.parameters.sliderWidthToDevice[newMode + NextendSmartSliderResponsive._DeviceOrientation[orientation]];
        width = nextend.smallestZoom + (((this.parameters.sliderWidthToDevice['desktopPortrait'] - nextend.smallestZoom)) / 50) * Math.floor((width - nextend.smallestZoom) / (((this.parameters.sliderWidthToDevice['desktopPortrait'] - nextend.smallestZoom)) / 50));
        this.setSize(width);
        if (this.containerElement.width() > width) {
            // We have to find a proper value for the zoom slider - backend only
            width = this.parameters.sliderWidthToDevice[newMode + NextendSmartSliderResponsive._DeviceOrientation[orientation]] - (this.parameters.sliderWidthToDevice['desktopPortrait'] - nextend.smallestZoom) / 50;
            this.setSize(width);
        }
    };

    NextendSmartSliderResponsive.prototype.setSize = function (targetWidth) {
        this.containerElement.width(targetWidth);

        this.doResize();
    };

    /**
     * Required for maximum slide width calculation
     * @returns {null}
     */
    NextendSmartSliderResponsive.prototype.getCanvas = function () {
        return null;
    };

    NextendSmartSliderResponsive.prototype.getVerticalOffsetHeight = function () {
        var h = 0;
        for (var i = 0; i < this.verticalOffsetSelectors.length; i++) {
            h += this.verticalOffsetSelectors.eq(i).outerHeight();
        }
        return h;
    };

    NextendSmartSliderResponsive.prototype.addMargin = function (side, widget) {
        this.widgetMargins[side].push(widget);
        if (widget.isVisible()) {
            this._addMarginSize(side, widget.getSize());
            this.enabledWidgetMargins.push(widget);
        }
        this.doNormalizedResize();
    };

    NextendSmartSliderResponsive.prototype.addStaticMargin = function (side, widget) {
        if (!this.widgetStaticMargins) {
            this.widgetStaticMargins = {
                Top: [],
                Right: [],
                Bottom: [],
                Left: []
            };
        }
        this.widgetStaticMargins[side].push(widget);
        this.doNormalizedResize();
    };

    NextendSmartSliderResponsive.prototype.refreshMargin = function () {
        for (var side in this.widgetMargins) {
            var widgets = this.widgetMargins[side];
            for (var i = widgets.length - 1; i >= 0; i--) {
                var widget = widgets[i];
                if (widget.isVisible()) {
                    if ($.inArray(widget, this.enabledWidgetMargins) == -1) {
                        this._addMarginSize(side, widget.getSize());
                        this.enabledWidgetMargins.push(widget);
                    }
                } else {
                    var index = $.inArray(widget, this.enabledWidgetMargins);
                    if (index != -1) {
                        this._addMarginSize(side, -widget.getSize());
                        this.enabledWidgetMargins.splice(index, 1);
                    }
                }
            }
        }
        if (this.widgetStaticMargins) {
            var staticSizes = {
                paddingTop: 0,
                paddingRight: 0,
                paddingBottom: 0,
                paddingLeft: 0
            };
            for (var side in this.widgetStaticMargins) {
                var widgets = this.widgetStaticMargins[side];
                for (var i = widgets.length - 1; i >= 0; i--) {
                    var widget = widgets[i];
                    if (widget.isVisible()) {
                        staticSizes['padding' + side] += widget.getSize();
                    }
                }
            }
            for (var k in staticSizes) {
                this.containerElementPadding.css(staticSizes);
            }
            this.staticSizes = staticSizes;
        }
    };

    NextendSmartSliderResponsive.prototype._addMarginSize = function (side, size) {
        var axis = null;
        switch (side) {
            case 'Top':
            case 'Bottom':
                axis = this._sliderVertical;
                break;
            default:
                axis = this._sliderHorizontal;
        }
        axis.data['margin' + side] += size;
        this.responsiveDimensions['startSliderMargin' + side] += size;
    };

    scope.NextendSmartSliderResponsive = NextendSmartSliderResponsive;
})(n2, window);