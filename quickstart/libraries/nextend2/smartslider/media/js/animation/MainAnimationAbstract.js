/**
 * Abstract class for all the main animations
 * @type {NextendSmartSliderMainAnimationAbstract}
 * @abstract
 */
(function ($, scope, undefined) {
    function NextendSmartSliderMainAnimationAbstract(slider, parameters) {

        this.state = 'ended';
        this.isTouch = false;

        this.slider = slider;

        this.parameters = $.extend({
            duration: 1500,
            ease: 'easeInOutQuint'
        }, parameters);

        this.parameters.duration /= 1000;

        this.sliderElement = slider.sliderElement;

        this.timeline = new NextendTimeline({
            paused: true
        });
    };

    NextendSmartSliderMainAnimationAbstract.prototype.setTouch = function (direction) {
        this.isTouch = direction;
    };

    NextendSmartSliderMainAnimationAbstract.prototype.getState = function () {
        return this.state;
    };

    NextendSmartSliderMainAnimationAbstract.prototype.timeScale = function () {
        if (arguments.length > 0) {
            this.timeline.timeScale(arguments[0]);
            return this;
        }
        return this.timeline.timeScale();
    };

    NextendSmartSliderMainAnimationAbstract.prototype.preChangeToPlay = function (deferred, currentSlide, nextSlide) {
        var deferredHandled = {
            handled: false
        };

        this.sliderElement.trigger('preChangeToPlay', [deferred, deferredHandled, currentSlide, nextSlide]);

        if (!deferredHandled.handled) {
            deferred.resolve();
        }
    };

    NextendSmartSliderMainAnimationAbstract.prototype.changeTo = function (currentSlideIndex, currentSlide, nextSlideIndex, nextSlide, reversed, isSystem) {

        this._initAnimation(currentSlideIndex, currentSlide, nextSlideIndex, nextSlide, reversed);

        this.state = 'initAnimation';

        this.timeline.paused(true);
        this.timeline.eventCallback('onStart', this.onChangeToStart, [currentSlideIndex, nextSlideIndex, isSystem], this);
        this.timeline.eventCallback('onComplete', this.onChangeToComplete, [currentSlideIndex, nextSlideIndex, isSystem], this);

        if (this.slider.parameters.dynamicHeight) {
            var tl = new NextendTimeline();
            this.slider.responsive.doResize(false, tl, nextSlideIndex, 0.6);
            this.timeline.add(tl);
        }


        // If the animation is in touch mode, we do not need to play the timeline as the touch will set the actual progress and also play later...
        if (!this.isTouch) {
            var deferred = $.Deferred();

            deferred.done($.proxy(function () {
                this.play();
            }, this.timeline));

            this.preChangeToPlay(deferred, currentSlide, nextSlide);
        } else {
            this.slider.callOnSlide(currentSlide, 'onOutAnimationsPlayed');
        }
    };

    /**
     * @abstract
     * @param currentSlideIndex
     * @param currentSlide
     * @param nextSlideIndex
     * @param nextSlide
     * @param reversed
     * @private
     */
    NextendSmartSliderMainAnimationAbstract.prototype._initAnimation = function (currentSlideIndex, currentSlide, nextSlideIndex, nextSlide, reversed) {

    };

    NextendSmartSliderMainAnimationAbstract.prototype.onChangeToStart = function (previousSlideIndex, currentSlideIndex, isSystem) {

        this.state = 'playing';

        var parameters = [this, previousSlideIndex, currentSlideIndex, isSystem];

        n2c.log('Event: mainAnimationStart: ', parameters, '{NextendSmartSliderMainAnimationAbstract}, previousSlideIndex, currentSlideIndex, isSystem');
        this.sliderElement.trigger('mainAnimationStart', parameters);

        this.slider.slides.eq(previousSlideIndex).trigger('mainAnimationStartOut', parameters);
        this.slider.slides.eq(currentSlideIndex).trigger('mainAnimationStartIn', parameters);
    };

    NextendSmartSliderMainAnimationAbstract.prototype.onChangeToComplete = function (previousSlideIndex, currentSlideIndex, isSystem) {
        var parameters = [this, previousSlideIndex, currentSlideIndex, isSystem];

        // When the animation done, clear the timeline
        this.timeline.clear();
        this.timeline.timeScale(1);

        this.slider.slides.eq(previousSlideIndex).trigger('mainAnimationCompleteOut', parameters);
        this.slider.slides.eq(currentSlideIndex).trigger('mainAnimationCompleteIn', parameters);

        this.state = 'ended';

        n2c.log('Event: mainAnimationComplete: ', parameters, '{NextendSmartSliderMainAnimationAbstract}, previousSlideIndex, currentSlideIndex, isSystem');
        this.sliderElement.trigger('mainAnimationComplete', parameters);
    };

    NextendSmartSliderMainAnimationAbstract.prototype.getEase = function () {
        if (this.isTouch) {
            return 'linear';
        }
        return this.parameters.ease;
    };
    scope.NextendSmartSliderMainAnimationAbstract = NextendSmartSliderMainAnimationAbstract;
})(n2, window);