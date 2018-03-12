(function ($, scope, undefined) {

    function NextendSmartSliderFrontendBackgroundAnimation(slider, parameters, backgroundAnimations) {
        this._currentBackgroundAnimation = false;
        NextendSmartSliderMainAnimationSimple.prototype.constructor.call(this, slider, parameters);

        this.bgAnimationElement = this.sliderElement.find('.n2-ss-background-animation');

        this.backgroundAnimations = $.extend({
            global: 0,
            speed: 'normal',
            slides: []
        }, backgroundAnimations);

        this.backgroundImages = slider.backgroundImages.getBackgroundImages();

        /**
         * Hack to force browser to better image rendering {@link http://stackoverflow.com/a/14308227/305604}
         * Prevents a Firefox glitch
         */
        slider.backgroundImages.hack();
    };

    NextendSmartSliderFrontendBackgroundAnimation.prototype = Object.create(NextendSmartSliderMainAnimationSimple.prototype);
    NextendSmartSliderFrontendBackgroundAnimation.prototype.constructor = NextendSmartSliderFrontendBackgroundAnimation;

    /**
     * @returns [{NextendSmartSliderBackgroundAnimationAbstract}, {string}]
     */
    NextendSmartSliderFrontendBackgroundAnimation.prototype.getBackgroundAnimation = function (i) {
        var animations = this.backgroundAnimations.global,
            speed = this.backgroundAnimations.speed;
        if (typeof this.backgroundAnimations.slides[i] != 'undefined' && this.backgroundAnimations.slides[i]) {
            var animation = this.backgroundAnimations.slides[i];
            animations = animation.animation;
            speed = animation.speed;
        }
        if (!animations) {
            return false;
        }
        return [animations[Math.floor(Math.random() * animations.length)], speed];
    },

    /**
     * Initialize the current background animation
     * @param currentSlideIndex
     * @param currentSlide
     * @param nextSlideIndex
     * @param nextSlide
     * @param reversed
     * @private
     */
        NextendSmartSliderFrontendBackgroundAnimation.prototype._initAnimation = function (currentSlideIndex, currentSlide, nextSlideIndex, nextSlide, reversed) {
            this._currentBackgroundAnimation = false;
            var currentImage = this.backgroundImages[currentSlideIndex],
                nextImage = this.backgroundImages[nextSlideIndex];

            if (currentImage && nextImage) {
                var backgroundAnimation = this.getBackgroundAnimation(nextSlideIndex);

                if (backgroundAnimation !== false) {
                    var durationMultiplier = 1;
                    switch (backgroundAnimation[1]) {
                        case 'superSlow':
                            durationMultiplier = 3;
                            break;
                        case 'slow':
                            durationMultiplier = 1.5;
                            break;
                        case 'fast':
                            durationMultiplier = 0.75;
                            break;
                        case 'superFast':
                            durationMultiplier = 0.5;
                            break;
                    }
                    this._currentBackgroundAnimation = new window['NextendSmartSliderBackgroundAnimation' + backgroundAnimation[0].type](this, currentImage.element, nextImage.element, backgroundAnimation[0], durationMultiplier, reversed);

                    NextendSmartSliderMainAnimationSimple.prototype._initAnimation.apply(this, arguments);

                    this._currentBackgroundAnimation.postSetup();

                    this.timeline.set($('<div />'), {
                        opacity: 1, onComplete: $.proxy(function () {
                            if (this._currentBackgroundAnimation) {
                                this._currentBackgroundAnimation.ended();
                                this._currentBackgroundAnimation = false;
                            }
                        }, this)
                    });

                    return;
                }
            }

            NextendSmartSliderMainAnimationSimple.prototype._initAnimation.apply(this, arguments);
        };

    /**
     * Remove the background animation when the current animation finish
     * @param previousSlideIndex
     * @param currentSlideIndex
     */
    NextendSmartSliderFrontendBackgroundAnimation.prototype.onChangeToComplete = function (previousSlideIndex, currentSlideIndex) {
        if (this._currentBackgroundAnimation) {
            this._currentBackgroundAnimation.ended();
            this._currentBackgroundAnimation = false;
        }
        NextendSmartSliderMainAnimationSimple.prototype.onChangeToComplete.apply(this, arguments);
    };

    NextendSmartSliderFrontendBackgroundAnimation.prototype.getExtraDelay = function () {
        if (this._currentBackgroundAnimation) {
            return this._currentBackgroundAnimation.getExtraDelay();
        }
        return 0;
    };

    NextendSmartSliderFrontendBackgroundAnimation.prototype.hasBackgroundAnimation = function () {
        return this._currentBackgroundAnimation;
    };

    scope.NextendSmartSliderFrontendBackgroundAnimation = NextendSmartSliderFrontendBackgroundAnimation;

})(n2, window);