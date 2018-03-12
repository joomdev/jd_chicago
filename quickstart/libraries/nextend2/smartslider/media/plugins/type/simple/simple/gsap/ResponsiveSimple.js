(function ($, scope, undefined) {

    function NextendSmartSliderResponsiveSimple() {
        NextendSmartSliderResponsive.prototype.constructor.apply(this, arguments);
    };

    NextendSmartSliderResponsiveSimple.prototype = Object.create(NextendSmartSliderResponsive.prototype);
    NextendSmartSliderResponsiveSimple.prototype.constructor = NextendSmartSliderResponsiveSimple;

    NextendSmartSliderResponsiveSimple.prototype.addResponsiveElements = function () {
        this.helperElements = {};

        this._sliderHorizontal = this.addResponsiveElement(this.sliderElement, ['width', 'marginLeft', 'marginRight'], 'w', 'slider');
        this.addResponsiveElement(this.sliderElement.find('.n2-ss-slider-1'), ['width', 'paddingLeft', 'paddingRight', 'borderLeftWidth', 'borderRightWidth'], 'w');

        this._sliderVertical = this.addResponsiveElement(this.sliderElement, ['height', 'marginTop', 'marginBottom'], 'h', 'slider');
        this.addResponsiveElement(this.sliderElement, ['fontSize'], 'fontRatio', 'slider');
        this.addResponsiveElement(this.sliderElement.find('.n2-ss-slider-1'), ['height', 'paddingTop', 'paddingBottom', 'borderTopWidth', 'borderBottomWidth'], 'h');

        this.helperElements.canvas = this.addResponsiveElement(this.sliderElement.find('.n2-ss-slide'), ['width'], 'w', 'slideouter');

        this.addResponsiveElement(this.sliderElement.find('.n2-ss-slide'), ['height'], 'h', 'slideouter');

        this.addResponsiveElement(this.sliderElement.find('.n2-ss-layers-container'), ['width'], 'slideW', 'slide');
        this.addResponsiveElement(this.sliderElement.find('.n2-ss-layers-container'), ['height'], 'slideH', 'slide').setCentered();

        var parallax = this.slider.parameters.mainanimation.parallax;
        var backgroundImages = this.slider.backgroundImages.getBackgroundImages();
        for (var i = 0; i < backgroundImages.length; i++) {
            if (parallax != 1) {
                this.addResponsiveElement(backgroundImages[i].element, ['width'], 'w');
                this.addResponsiveElement(backgroundImages[i].element, ['height'], 'h');
            }

            this.addResponsiveElementBackgroundImageAsSingle(backgroundImages[i].image, backgroundImages[i], []);
        }


        var video = this.sliderElement.find('.n2-ss-slider-background-video');
        if (video.length) {
            if (video[0].videoWidth > 0) {
                this.videoPlayerReady(video);
            } else {
                video[0].addEventListener('error', $.proxy(this.videoPlayerError, this, video), true);
                video[0].addEventListener('canplay', $.proxy(this.videoPlayerReady, this, video));
            }
        }
    };

    NextendSmartSliderResponsiveSimple.prototype.getCanvas = function () {
        return this.helperElements.canvas;
    };

    NextendSmartSliderResponsiveSimple.prototype.videoPlayerError = function (video) {
        video.remove();
    };

    NextendSmartSliderResponsiveSimple.prototype.videoPlayerReady = function (video) {
        video.data('ratio', video[0].videoWidth / video[0].videoHeight);
        video.addClass('n2-active');

        this.slider.ready($.proxy(function () {
            this.slider.sliderElement.on('SliderResize', $.proxy(this.resizeVideo, this, video));
            this.resizeVideo(video);
        }, this));
    };

    NextendSmartSliderResponsiveSimple.prototype.resizeVideo = function (video) {

        var mode = video.data('mode'),
            ratio = video.data('ratio'),
            slideOuter = this.slider.dimensions.slideouter || this.slider.dimensions.slide,
            slideOuterRatio = slideOuter.width / slideOuter.height;

        if (mode == 'fill') {
            if (slideOuterRatio > ratio) {
                video.css({
                    width: '100%',
                    height: 'auto'
                });
            } else {
                video.css({
                    width: 'auto',
                    height: '100%'
                });
            }
        } else if (mode == 'fit') {
            if (slideOuterRatio < ratio) {
                video.css({
                    width: '100%',
                    height: 'auto'
                });
            } else {
                video.css({
                    width: 'auto',
                    height: '100%'
                });
            }
        }
    };

    scope.NextendSmartSliderResponsiveSimple = NextendSmartSliderResponsiveSimple;

})(n2, window);