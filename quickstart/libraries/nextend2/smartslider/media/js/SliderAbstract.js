(function ($, scope, undefined) {

    function NextendSmartSliderAbstract(sliderElement, parameters) {
        this.startedDeferred = $.Deferred();

        var id = sliderElement.attr('id');
        if (window[id] && window[id] instanceof NextendSmartSliderAbstract) {
            return false;
        }
        // Register our object to a global variable
        window[id] = this;

        // Store them as we might need to change them back
        this.nextCarousel = this.next;
        this.previousCarousel = this.previous;

        if (sliderElement.prop('tagName') == 'SCRIPT') {
            var dependency = sliderElement.data('dependency'),
                rocketLoad = $.proxy(function () {
                    var rocketSlider = $(sliderElement.html().replace(/<_s_c_r_i_p_t/g, '<script').replace(/<_\/_s_c_r_i_p_t/g, '</script'));
                    sliderElement.replaceWith(rocketSlider);
                    this.postInit(id, $('#' + id), parameters);
                    $(window).triggerHandler('n2Rocket', [this.sliderElement]);
                }, this);
            if ($('#n2-ss-' + dependency).length) {
                n2ss.ready(dependency, $.proxy(function (slider) {
                    slider.ready(rocketLoad);
                }, this));
            } else {
                rocketLoad();
            }
        } else {
            this.postInit(id, sliderElement, parameters);
        }
    }

    NextendSmartSliderAbstract.prototype.postInit = function (id, sliderElement, parameters) {
        this.killed = false;
        this.isAdmin = false;
        this.currentSlideIndex = 0;
        this.responsive = false;
        this.layerMode = true;
        this._lastChangeTime = 0;
        n2c.log('Slider init: ', id);
        this.id = parseInt(id.replace('n2-ss-', ''));

        this.sliderElement = sliderElement.data('ss', this);
        this.readyDeferred = $.Deferred();

        this.parameters = $.extend({
            admin: false,
            playWhenVisible: 1,
            isStaticEdited: false,
            callbacks: '',
            autoplay: {},
            blockrightclick: false,
            maintainSession: 0,
            controls: {
                drag: false,
                touch: 'horizontal',
                keyboard: false,
                scroll: false,
                tilt: false
            },
            hardwareAcceleration: true,
            layerMode: {
                playOnce: 0,
                playFirstLayer: 1,
                mode: 'skippable',
                inAnimation: 'mainInEnd'
            },
            parallax: {
                enabled: 0,
                mobile: 0,
                horizontal: 'mouse',
                vertical: 'mouse',
                origin: 'enter'
            },
            load: {},
            mainanimation: {},
            randomize: {},
            responsive: {},
            lazyload: {
                enabled: 0
            },
            postBackgroundAnimations: false,
            initCallbacks: [],
            dynamicHeight: 0
        }, parameters);

        try {
            eval(this.parameters.callbacks);
        } catch (e) {
            console.error(e);
        }

        this.startVisibilityCheck();
        n2ss.makeReady(this.id, this);


        this.widgetDeferreds = [];
        this.sliderElement.on('addWidget', $.proxy(this.addWidget, this));

        this.isAdmin = !!this.parameters.admin;
        if (this.isAdmin) {
            this.changeTo = function () {
            };
        }

        this.load = new NextendSmartSliderLoad(this, this.parameters.load);

        this.findSlides();

        this.currentSlideIndex = this.__getActiveSlideIndex();

        var forceActiveSlideIndex = typeof window['ss' + this.id] !== 'undefined' ? parseInt(window['ss' + this.id]) : null;
        if (forceActiveSlideIndex !== null) {
            this.changeActiveBeforeLoad(forceActiveSlideIndex);
        }

        if (!this.isAdmin && this.parameters.maintainSession && typeof sessionStorage !== 'undefined') {
            var sessionIndex = parseInt(sessionStorage.getItem('ss-' + this.id));
            if (forceActiveSlideIndex === null && sessionIndex !== null) {
                this.changeActiveBeforeLoad(sessionIndex);
            }
            this.sliderElement.on('mainAnimationComplete', $.proxy(function (e, animation, previous, next) {
                sessionStorage.setItem('ss-' + this.id, next);
            }, this));
        }

        this.backgroundImages = new NextendSmartSliderBackgroundImages(this);

        n2c.log('First slide index: ', this.currentSlideIndex);

        for (var i = 0; i < this.parameters.initCallbacks.length; i++) {
            (new Function(this.parameters.initCallbacks[i]))(this);
        }

        this.initSlides();

        this.widgets = new NextendSmartSliderWidgets(this);

        this.sliderElement.on('universalenter', $.proxy(function () {
            this.sliderElement.addClass('n2-hover');
        }, this)).on('universalleave', $.proxy(function (e) {
            e.stopPropagation();
            this.sliderElement.removeClass('n2-hover');
        }, this));


        this.controls = {};

        if (this.layerMode) {
            this.initMainAnimationWithLayerAnimation();
        }

        if (this.parameters.blockrightclick) {
            this.sliderElement.bind("contextmenu", function (e) {
                e.preventDefault();
            });
        }

        this.initMainAnimation();
        this.initResponsiveMode();

        if (this.killed) {
            return;
        }

        this.initControls();

        this.startedDeferred.resolve(this);

        if (!this.isAdmin) {
            var event = 'click';
            if (this.parameters.controls.touch != '0' && this.parameters.controls.touch) {
                event = 'n2click';
            }
            this.sliderElement.find('[n2click]').each(function (i, el) {
                var el = $(el);
                el.on(event, function () {
                    eval(el.attr('n2click'));
                });
            });

            this.sliderElement.find('[data-click]').each(function (i, el) {
                var el = $(el).on('click', function () {
                    eval(el.data('click'));
                }).css('cursor', 'pointer');
            });

            this.sliderElement.find('[data-mouseenter]').each(function (i, el) {
                var el = $(el).on('mouseenter', function () {
                    eval(el.data('mouseenter'));
                });
            });

            this.sliderElement.find('[data-mouseleave]').each(function (i, el) {
                var el = $(el).on('mouseleave', function () {
                    eval(el.data('mouseleave'));
                });
            });

            this.sliderElement.find('[data-play]').each(function (i, el) {
                var el = $(el).on('n2play', function () {
                    eval(el.data('play'));
                });
            });

            this.sliderElement.find('[data-pause]').each(function (i, el) {
                var el = $(el).on('n2pause', function () {
                    eval(el.data('pause'));
                });
            });

            this.sliderElement.find('[data-stop]').each(function (i, el) {
                var el = $(el).on('n2stop', function () {
                    eval(el.data('stop'));
                });
            });

            var preventFocus = false;
            this.slides.find('a').on('mousedown', function (e) {
                preventFocus = true;
                setTimeout(function () {
                    preventFocus = false;
                }, 100);
            });

            this.slides.find('a').on('focus', $.proxy(function (e) {
                if (!preventFocus) {
                    var slideIndex = this.findSlideIndexByElement(e.currentTarget);
                    if (slideIndex != -1 && slideIndex != this.currentSlideIndex) {
                        this.changeTo(slideIndex, false, false);
                    }
                }
            }, this));
        }

        this.preReadyResolve();

        this.initCarousel();
    };

    NextendSmartSliderAbstract.prototype.initSlides = function () {
        if (this.layerMode) {
            if (this.isAdmin && this.type != 'showcase') {
                new NextendSmartSliderSlide(this, this.slides.eq(this.currentSlideIndex), 1);
            } else {
                for (var i = 0; i < this.slides.length; i++) {
                    new NextendSmartSliderSlide(this, this.slides.eq(i), this.currentSlideIndex == i);
                }
            }

            var staticSlide = this.findStaticSlide();
            if (staticSlide.length) {
                new NextendSmartSliderSlide(this, staticSlide, true, true);
            }
        }
    };

    NextendSmartSliderAbstract.prototype.getRealIndex = function (index) {
        return index;
    };

    NextendSmartSliderAbstract.prototype.changeActiveBeforeLoad = function (index) {
        if (index > 0 && index < this.slides.length && this.currentSlideIndex != index) {
            this.unsetActiveSlide(this.slides.eq(this.currentSlideIndex));
            this.setActiveSlide(this.slides.eq(index));
            this.currentSlideIndex = index;
            this.ready($.proxy(function () {
                this.sliderElement.trigger('sliderSwitchTo', [index, this.getRealIndex(index)]);
            }, this));
        }
    };

    NextendSmartSliderAbstract.prototype.kill = function () {
        this.killed = true;
        $('#' + this.sliderElement.attr('id') + '-placeholder').remove();
        this.sliderElement.closest('.n2-ss-align').remove();
    };

    NextendSmartSliderAbstract.prototype.findSlides = function () {

        this.realSlides = this.slides = this.sliderElement.find('.n2-ss-slide');
    };

    NextendSmartSliderAbstract.prototype.findStaticSlide = function () {
        return this.sliderElement.find('.n2-ss-static-slide');
    };

    NextendSmartSliderAbstract.prototype.addWidget = function (e, deferred) {
        this.widgetDeferreds.push(deferred);
    };

    NextendSmartSliderAbstract.prototype.started = function (fn) {
        this.startedDeferred.done($.proxy(fn, this));
    };

    NextendSmartSliderAbstract.prototype.preReadyResolve = function () {
        // Hack to allow time to widgets to register
        setTimeout($.proxy(this._preReadyResolve, this), 1);
    };

    NextendSmartSliderAbstract.prototype._preReadyResolve = function () {

        this.load.start();
        this.load.loaded($.proxy(this.readyResolve, this));
    };

    NextendSmartSliderAbstract.prototype.readyResolve = function () {
        n2c.log('Slider ready');
        $(window).scroll(); // To force other sliders to recalculate the scroll position

        this.readyDeferred.resolve();
    };

    NextendSmartSliderAbstract.prototype.ready = function (fn) {
        this.readyDeferred.done($.proxy(fn, this));
    };

    NextendSmartSliderAbstract.prototype.startVisibilityCheck = function () {
        this.visibleDeferred = $.Deferred();
        if (this.parameters.playWhenVisible) {
            this.ready($.proxy(function () {
                $(window).on('scroll.n2-ss-visible' + this.id + ' resize.n2-ss-visible' + this.id, $.proxy(this.checkIfVisible, this));
                this.checkIfVisible();
            }, this));
        } else {
            this.ready($.proxy(function () {
                this.visibleDeferred.resolve();
            }, this));
        }
    };

    NextendSmartSliderAbstract.prototype.checkIfVisible = function () {
        var TopView = $(window).scrollTop(),
            BotView = TopView + $(window).height(),
            middlePoint = this.sliderElement.offset().top + this.sliderElement.height() / 2;
        if (TopView <= middlePoint && BotView >= middlePoint) {
            $(window).off('scroll.n2-ss-visible' + this.id + ' resize.n2-ss-visible' + this.id, $.proxy(this.checkIfVisible, this));
            this.visibleDeferred.resolve();
        }
    };

    NextendSmartSliderAbstract.prototype.visible = function (fn) {
        this.visibleDeferred.done($.proxy(fn, this));
    };

    NextendSmartSliderAbstract.prototype.isPlaying = function () {
        if (this.mainAnimation.getState() != 'ended') {
            return true;
        }
        return false;
    };

    NextendSmartSliderAbstract.prototype.focus = function (isSystem) {
        var deferred = $.Deferred();
        if (typeof isSystem == 'undefined') {
            isSystem = 0;
        }
        if (this.responsive.parameters.focusUser && !isSystem || this.responsive.parameters.focusAutoplay && isSystem) {
            var top = this.sliderElement.offset().top - this.responsive.verticalOffsetSelectors.height();
            if ($(window).scrollTop() != top) {
                $("html, body").animate({scrollTop: top}, 400, $.proxy(function () {
                    deferred.resolve();
                }, this));
            } else {
                deferred.resolve();
            }
        } else {
            deferred.resolve();
        }
        return deferred;
    };

    NextendSmartSliderAbstract.prototype.initCarousel = function () {
        if (!parseInt(this.parameters.carousel)) {
            // Replace the methods
            this.next = this.nextNotCarousel;
            this.previous = this.previousNotCarousel;

            var slides = this.slides.length;
            var previousArrowOpacity = 1,
                previousArrow = this.sliderElement.find('.nextend-arrow-previous'),
                previous = function (opacity) {
                    if (opacity != previousArrowOpacity) {
                        NextendTween.to(previousArrow, 0.4, {opacity: opacity}).play();
                        previousArrowOpacity = opacity;
                    }
                };
            var nextArrowOpacity = 1,
                nextArrow = this.sliderElement.find('.nextend-arrow-next'),
                next = function (opacity) {
                    if (opacity != nextArrowOpacity) {
                        NextendTween.to(nextArrow, 0.4, {opacity: opacity}).play();
                        nextArrowOpacity = opacity;
                    }
                };

            var process = function (i) {
                if (i == 0) {
                    previous(0);
                } else {
                    previous(1);
                }
                if (i == slides - 1) {
                    next(0);
                } else {
                    next(1);
                }
            };

            process(this.__getActiveSlideIndex())

            this.sliderElement.on('sliderSwitchTo', function (e, i) {
                process(i);
            });
        }
    };

    NextendSmartSliderAbstract.prototype.next = function (isSystem, customAnimation) {
        var nextIndex = this.currentSlideIndex + 1;
        if (nextIndex >= this.slides.length) {
            nextIndex = 0;
        }
        return this.changeTo(nextIndex, false, isSystem, customAnimation);
    };

    NextendSmartSliderAbstract.prototype.previous = function (isSystem, customAnimation) {
        var nextIndex = this.currentSlideIndex - 1;
        if (nextIndex < 0) {
            nextIndex = this.slides.length - 1;
        }
        return this.changeTo(nextIndex, true, isSystem, customAnimation);
    };

    NextendSmartSliderAbstract.prototype.nextNotCarousel = function (isSystem, customAnimation) {
        var nextIndex = this.currentSlideIndex + 1;
        if (nextIndex < this.slides.length) {
            return this.changeTo(nextIndex, false, isSystem, customAnimation);
        }
        return false;
    };

    NextendSmartSliderAbstract.prototype.previousNotCarousel = function (isSystem, customAnimation) {
        var nextIndex = this.currentSlideIndex - 1;
        if (nextIndex >= 0) {
            return this.changeTo(nextIndex, true, isSystem, customAnimation);
        }
        return false;
    };

    NextendSmartSliderAbstract.prototype.directionalChangeToReal = function (nextSlideIndex) {
        this.directionalChangeTo(nextSlideIndex);
    };

    NextendSmartSliderAbstract.prototype.directionalChangeTo = function (nextSlideIndex) {
        if (nextSlideIndex > this.currentSlideIndex) {
            this.changeTo(nextSlideIndex, false);
        } else {
            this.changeTo(nextSlideIndex, true);
        }
    };

    NextendSmartSliderAbstract.prototype.changeTo = function (nextSlideIndex, reversed, isSystem, customAnimation) {
        nextSlideIndex = parseInt(nextSlideIndex);

        if (nextSlideIndex != this.currentSlideIndex) {
            n2c.log('Event: sliderSwitchTo: ', 'targetSlideIndex');
            this.sliderElement.trigger('sliderSwitchTo', [nextSlideIndex, this.getRealIndex(nextSlideIndex)]);
            var time = $.now();
            $.when(this.backgroundImages.preLoad(nextSlideIndex), this.focus(isSystem)).done($.proxy(function () {

                if (this._lastChangeTime <= time) {
                    this._lastChangeTime = time;
                    // If the current main animation haven't finished yet or the prefered next slide is the same as our current slide we have nothing to do
                    var state = this.mainAnimation.getState();
                    if (state == 'ended') {

                        if (typeof isSystem === 'undefined') {
                            isSystem = false;
                        }

                        var animation = this.mainAnimation;
                        if (typeof customAnimation !== 'undefined') {
                            animation = customAnimation;
                        }

                        this._changeTo(nextSlideIndex, reversed, isSystem, customAnimation);

                        n2c.log('Change From:', this.currentSlideIndex, ' To: ', nextSlideIndex, ' Reversed: ', reversed, ' System: ', isSystem);
                        animation.changeTo(this.currentSlideIndex, this.slides.eq(this.currentSlideIndex), nextSlideIndex, this.slides.eq(nextSlideIndex), reversed, isSystem);

                        this.currentSlideIndex = nextSlideIndex;

                    } else if (state == 'playing') {
                        this.sliderElement.off('.fastChange').one('mainAnimationComplete.fastChange', $.proxy(function () {
                            this.changeTo.call(this, nextSlideIndex, reversed, isSystem, customAnimation);
                        }, this));
                        this.mainAnimation.timeScale(this.mainAnimation.timeScale() * 2);
                    }
                }
            }, this));
            return true;
        }
        return false;
    };

    NextendSmartSliderAbstract.prototype._changeTo = function (nextSlideIndex, reversed, isSystem, customAnimation) {

    };

    NextendSmartSliderAbstract.prototype.__getActiveSlideIndex = function () {
        var index = this.slides.index(this.slides.filter('.n2-ss-slide-active'));
        if (index === -1) {
            index = 0;
        }
        return index;
    };

    NextendSmartSliderAbstract.prototype.setActiveSlide = function (slide) {
        slide.addClass('n2-ss-slide-active');
    };

    NextendSmartSliderAbstract.prototype.unsetActiveSlide = function (slide) {
        slide.removeClass('n2-ss-slide-active');
    };

    NextendSmartSliderAbstract.prototype.initMainAnimationWithLayerAnimation = function () {

        if (this.parameters.layerMode.mode == 'forced') {
            this.sliderElement.on('preChangeToPlay', $.proxy(function (e, deferred, deferredHandled, currentSlide, nextSlide) {
                deferredHandled.handled = true;
                currentSlide.on('layerAnimationCompleteOut.layers', function () {
                    currentSlide.off('layerAnimationCompleteOut.layers');
                    deferred.resolve();
                });
                this.callOnSlide(currentSlide, 'playOut');
            }, this));
        }
        this.sliderElement.on('mainAnimationStart', $.proxy(this.onMainAnimationStartSyncLayers, this, this.parameters.layerMode));

    };

    NextendSmartSliderAbstract.prototype.onMainAnimationStartSyncLayers = function (layerMode, e, animation, previousSlideIndex, currentSlideIndex) {
        var inSlide = this.slides.eq(currentSlideIndex),
            outSlide = this.slides.eq(previousSlideIndex);
        if (layerMode.inAnimation == 'mainInStart') {
            inSlide.on('mainAnimationStartIn.layers', $.proxy(function () {
                inSlide.off('mainAnimationStartIn.layers');
                this.callOnSlide(inSlide, 'playIn');
            }, this));
        } else if (layerMode.inAnimation == 'mainInEnd') {
            inSlide.on('mainAnimationCompleteIn.layers', $.proxy(function () {
                inSlide.off('mainAnimationCompleteIn.layers');
                this.callOnSlide(inSlide, 'playIn');
            }, this));
        }

        if (layerMode.mode == 'skippable') {
            outSlide.on('mainAnimationCompleteOut.layers', $.proxy(function () {
                outSlide.off('mainAnimationCompleteOut.layers');
                if (layerMode.playOnce) {
                    this.callOnSlide(outSlide, 'pause');
                } else {
                    this.callOnSlide(outSlide, 'reset');
                }
            }, this));
        }
    };

    NextendSmartSliderAbstract.prototype.callOnSlide = function (slide, functionName) {
        slide.data('slide')[functionName]();
    };

    NextendSmartSliderAbstract.prototype.findSlideIndexByElement = function (element) {
        element = $(element);
        for (var i = 0; i < this.slides.length; i++) {
            if (this.slides.eq(i).has(element).length === 1) {
                return i;
            }
        }
        return -1;
    };

    NextendSmartSliderAbstract.prototype.initMainAnimation = function () {
    };

    NextendSmartSliderAbstract.prototype.initResponsiveMode = function () {
        new scope[this.responsiveClass](this, this.parameters.responsive);
        this.dimensions = this.responsive.responsiveDimensions;
    };

    NextendSmartSliderAbstract.prototype.initControls = function () {

        if (!this.parameters.admin) {
            if (this.parameters.controls.touch != '0') {
                new NextendSmartSliderControlTouch(this, this.parameters.controls.touch, {
                    fallbackToMouseEvents: this.parameters.controls.drag
                });
            }

            if (this.parameters.controls.keyboard) {
                new NextendSmartSliderControlKeyboard(this, 'horizontal');
            }

            if (this.parameters.controls.scroll) {
                new NextendSmartSliderControlScroll(this);
            }

            if (this.parameters.controls.tilt) {
                new NextendSmartSliderControlTilt(this);
            }

            new NextendSmartSliderControlAutoplay(this, this.parameters.autoplay);

        }
    };

    NextendSmartSliderAbstract.prototype.slideToID = function (id) {
        var index = this.slides.index(this.slides.filter('[data-id="' + id + '"]'));
        return this.slide(index);
    };

    NextendSmartSliderAbstract.prototype.slide = function (index) {
        if (index >= 0 && index < this.slides.length) {
            return this.changeTo(index);
        }
        return false;
    };

    NextendSmartSliderAbstract.prototype.adminGetCurrentSlideElement = function () {

        if (this.parameters.isStaticEdited) {
            return this.findStaticSlide();
        }
        return this.slides.eq(this.currentSlideIndex);
    };

    scope.NextendSmartSliderAbstract = NextendSmartSliderAbstract;

})(n2, window);