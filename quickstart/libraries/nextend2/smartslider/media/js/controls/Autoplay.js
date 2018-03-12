(function ($, scope, undefined) {
    function NextendSmartSliderControlAutoplay(slider, parameters) {
        this._inited = false;
        this._active = false;
        this._disabled = false;
        this._currentCount = 0;
        this._progressEnabled = false;
        this.timeline = null;

        this.deferredsMediaPlaying = null;
        this.deferredMouseLeave = null;
        this.deferredMouseEnter = null;
        this.mainAnimationDeferred = true;
        this.autoplayDeferred = null;

        this.slider = slider;

        this.parameters = $.extend({
            enabled: 0,
            start: 1,
            duration: 8000,
            autoplayToSlide: 0,
            pause: {
                mouse: 'enter',
                click: true,
                mediaStarted: true
            },
            resume: {
                click: 0,
                mouse: 0,
                mediaEnded: true
            }
        }, parameters);

        if (this.parameters.enabled) {

            this.parameters.duration /= 1000;

            slider.controls.autoplay = this;

            this.deferredsExtraPlaying = {};

            this.slider.visible($.proxy(this.onReady, this));

        } else {
            this.disable();
        }

        slider.controls.autoplay = this;
    };

    NextendSmartSliderControlAutoplay.prototype.onReady = function () {
        this.autoplayDeferred = $.Deferred();

        var obj = {
            _progress: 0
        };
        this.timeline = NextendTween.to(obj, this.getSlideDuration(this.slider.currentSlideIndex), {
            _progress: 1,
            paused: true,
            onComplete: $.proxy(this.next, this)
        });

        if (this._progressEnabled) {
            this.enableProgress();
        }


        var sliderElement = this.slider.sliderElement;

        if (this.parameters.start) {
            this.continueAutoplay();
        }

        sliderElement.on('mainAnimationStart.autoplay', $.proxy(this.onMainAnimationStart, this));

        if (this.parameters.pause.mouse != '0') {
            switch (this.parameters.pause.mouse) {
                case 'enter':
                    sliderElement.on('mouseenter.autoplay', $.proxy(this.pauseAutoplayMouseEnter, this));
                    sliderElement.on('mouseleave.autoplay', $.proxy(this.pauseAutoplayMouseEnterEnded, this));
                    break;
                case 'leave':
                    sliderElement.on('mouseleave.autoplay', $.proxy(this.pauseAutoplayMouseLeave, this));
                    sliderElement.on('mouseenter.autoplay', $.proxy(this.pauseAutoplayMouseLeaveEnded, this));
                    break;
            }
        }
        if (this.parameters.pause.click && !this.parameters.resume.click) {
            sliderElement.on('universalclick.autoplay', $.proxy(this.pauseAutoplayUniversal, this));
        } else if (!this.parameters.pause.click && this.parameters.resume.click) {
            sliderElement.on('universalclick.autoplay', $.proxy(this.continueAutoplay, this));
        } else {
            sliderElement.on('universalclick.autoplay', $.proxy(function (e) {
                if (this._active) {
                    this.pauseAutoplayUniversal(e);
                } else {
                    this.continueAutoplay(e);
                }
            }, this));
        }
        if (this.parameters.pause.mediaStarted) {
            this.deferredsMediaPlaying = {};
            sliderElement.on('mediaStarted.autoplay', $.proxy(this.pauseAutoplayMediaPlaying, this));
            sliderElement.on('mediaEnded.autoplay', $.proxy(this.pauseAutoplayMediaPlayingEnded, this));
        }

        if (this.parameters.resume.mouse != '0') {
            switch (this.parameters.resume.mouse) {
                case 'enter':
                    sliderElement.on('mouseenter.autoplay', $.proxy(this.continueAutoplay, this));
                    break;
                case 'leave':
                    sliderElement.on('mouseleave.autoplay', $.proxy(this.continueAutoplay, this));
                    break;
            }
        }

        if (this.parameters.resume.mediaEnded) {
            sliderElement.on('mediaEnded.autoplay', $.proxy(this.continueAutoplay, this));
        }
        sliderElement.on('autoplayExtraWait.autoplay', $.proxy(this.pauseAutoplayExtraPlaying, this));
        sliderElement.on('autoplayExtraContinue.autoplay', $.proxy(this.pauseAutoplayExtraPlayingEnded, this));

    };

    NextendSmartSliderControlAutoplay.prototype.enableProgress = function () {
        if (this.timeline) {
            this.timeline.eventCallback('onUpdate', $.proxy(this.onUpdate, this));
        }
        this._progressEnabled = true;
    };


    NextendSmartSliderControlAutoplay.prototype.onMainAnimationStart = function (e, animation, previousSlideIndex, currentSlideIndex, isSystem) {
        this.mainAnimationDeferred = $.Deferred();
        this.deActivate(0);
    };

    NextendSmartSliderControlAutoplay.prototype.onMainAnimationComplete = function (e, animation, previousSlideIndex, currentSlideIndex) {
        this.timeline.duration(this.getSlideDuration(currentSlideIndex));

        this.mainAnimationDeferred.resolve();

        this.continueAutoplay();
    };

    NextendSmartSliderControlAutoplay.prototype.getSlideDuration = function (index) {
        var slide = this.slider.realSlides.eq(this.slider.getRealIndex(index)).data('slide'),
            duration = slide.minimumSlideDuration;

        if (duration < 0.3 && duration < this.parameters.duration) {
            duration = this.parameters.duration;
        }
        return duration;
    };

    NextendSmartSliderControlAutoplay.prototype.continueAutoplay = function (e) {
        if (this.autoplayDeferred.state() == 'pending') {
            this.autoplayDeferred.reject();
        }
        var deferreds = [];
        for (var k in this.deferredsExtraPlaying) {
            deferreds.push(this.deferredsExtraPlaying[k]);
        }
        for (var k in this.deferredsMediaPlaying) {
            deferreds.push(this.deferredsMediaPlaying[k]);
        }
        deferreds.push(this.deferredMouseEnter);
        deferreds.push(this.mainAnimationDeferred);

        this.autoplayDeferred = $.Deferred();
        this.autoplayDeferred.done($.proxy(this._continueAutoplay, this));

        $.when.apply($, deferreds).done($.proxy(function () {
            if (this.autoplayDeferred.state() == 'pending') {
                this.autoplayDeferred.resolve();
            }
        }, this));
    };

    NextendSmartSliderControlAutoplay.prototype._continueAutoplay = function () {
        if (!this._active && !this._disabled) {
            this._active = true;
            if (!this._inited) {
                this.slider.sliderElement.on('mainAnimationComplete.autoplay', $.proxy(this.onMainAnimationComplete, this));
                this._inited = true;
            }
            n2c.log('Event: autoplayStarted');
            this.slider.sliderElement.triggerHandler('autoplayStarted');

            if (this.timeline.progress() == 1) {
                this.timeline.pause(0, false);
            }

            this.startTimeout(null);
        }
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayUniversal = function () {
        this.autoplayDeferred.reject();
        this.deActivate(null);
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayMouseEnter = function () {
        this.autoplayDeferred.reject();
        this.deferredMouseEnter = $.Deferred();
        this.deActivate(null);
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayMouseEnterEnded = function () {
        if (this.deferredMouseEnter) {
            this.deferredMouseEnter.resolve();
        }
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayMouseLeave = function () {
        this.autoplayDeferred.reject();
        this.deferredMouseLeave = $.Deferred();
        this.deActivate(null);
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayMouseLeaveEnded = function () {
        if (this.deferredMouseLeave) {
            this.deferredMouseLeave.resolve();
        }
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayMediaPlaying = function (e, obj) {
        if (typeof this.deferredsMediaPlaying[obj] !== 'undefined') {
            this.autoplayDeferred.reject();
        }
        this.deferredsMediaPlaying[obj] = $.Deferred();
        this.deActivate(null);
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayMediaPlayingEnded = function (e, obj) {
        if (typeof this.deferredsMediaPlaying[obj] !== 'undefined') {
            this.autoplayDeferred.reject();
            this.deferredsMediaPlaying[obj].resolve();
            delete this.deferredsMediaPlaying[obj];
        }
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayExtraPlaying = function (e, obj) {
        if (typeof this.deferredsExtraPlaying[obj] !== 'undefined') {
            this.autoplayDeferred.reject();
        }
        this.deferredsExtraPlaying[obj] = $.Deferred();
        this.deActivate(null);
    };

    NextendSmartSliderControlAutoplay.prototype.pauseAutoplayExtraPlayingEnded = function (e, obj) {
        if (typeof this.deferredsExtraPlaying[obj] !== 'undefined') {
            this.autoplayDeferred.reject();
            this.deferredsExtraPlaying[obj].resolve();
            delete this.deferredsExtraPlaying[obj];
        }
        this.continueAutoplay();
    };

    NextendSmartSliderControlAutoplay.prototype.deActivate = function (seekTo) {
        if (this._active) {
            this._active = false;
            if (seekTo !== 0) {
                n2c.log('Event: autoplayPaused');
                this.slider.sliderElement.triggerHandler('autoplayPaused');
            }
        }

        if (this.timeline) {
            this.timeline.pause(seekTo, false);
        }
    };

    NextendSmartSliderControlAutoplay.prototype.disable = function () {
        this.deActivate(0);
        this.slider.sliderElement.triggerHandler('autoplayPaused');
        this.slider.sliderElement.triggerHandler('autoplayDisabled');
        this.slider.sliderElement.off('.autoplay');
        n2c.log('Autoplay: disable');
        this._disabled = true;
    };

    NextendSmartSliderControlAutoplay.prototype.startTimeout = function (time) {
        if (this._active && !this._disabled) {
            this.timeline.play(time);
        }
    };

    NextendSmartSliderControlAutoplay.prototype.next = function () {
        this.timeline.pause();
        this._currentCount++;
        /**
         * We have reached the maximum slides in the autoplay so disable it completely
         */
        if (this.parameters.autoplayToSlide > 0 && this._currentCount >= this.parameters.autoplayToSlide) {
            n2c.log('Autoplay: auto play to slide value reached');
            this.disable();
        }

        this.slider.nextCarousel(true);
    };

    NextendSmartSliderControlAutoplay.prototype.onUpdate = function () {
        this.slider.sliderElement.triggerHandler('autoplay', this.timeline.progress());
    };

    scope.NextendSmartSliderControlAutoplay = NextendSmartSliderControlAutoplay;
})(n2, window);