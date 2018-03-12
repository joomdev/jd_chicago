(function ($, scope, undefined) {

    /**
     * NOT_INITIALIZED -> INITIALIZED -> READY_TO_START -> PLAYING -> ENDED
     *                          <-----------------------------/
     */
    var SlideStatus = {
            NOT_INITIALIZED: -1,
            INITIALIZED: 0,
            READY_TO_START: 1,
            PLAYING: 2,
            ENDED: 3
        },
        TimelineMode = {
            event: 0,
            linear: 1
        },
        LayerStatus = {
            NOT_INITIALIZED: -1,
            INITIALIZED: 1,
            PLAY_IN_DISABLED: 2,
            PLAY_IN_STARTED: 3,
            PLAY_IN_PAUSED: 4,
            PLAY_IN_ENDED: 5,
            PLAY_LOOP_STARTED: 6,
            PLAY_LOOP_PAUSED: 7,
            PLAY_LOOP_ENDED: 8,
            PLAY_OUT_STARTED: 9,
            PLAY_OUT_PAUSED: 10,
            PLAY_OUT_ENDED: 11
        },
        In = {
            NOT_INITIALIZED: -1,
            NO: 0,
            INITIALIZED: 1
        },
        Loop = {
            NOT_INITIALIZED: -1,
            NO: 0,
            INITIALIZED: 1
        },
        Out = {
            NOT_INITIALIZED: -1,
            NO: 0,
            INITIALIZED: 1
        },
        zero = {
            opacity: 1,
            x: 0,
            y: 0,
            z: 0,
            rotationX: 0,
            rotationY: 0,
            rotationZ: 0,
            scaleX: 1,
            scaleY: 1,
            scaleZ: 1,
            skewX: 0
        },
        responsiveProperties = ['left', 'top', 'width', 'height'];


    if (/(MSIE\ [0-7]\.\d+)/.test(navigator.userAgent)) {
        function getPos($element) {
            return $element.position();
        }
    } else {
        function getPos($element) {
            return {
                left: $element.prop('offsetLeft'),
                top: $element.prop('offsetTop')
            }
        }
    }

    function Slide(slider, $slideElement, isFirstSlide, isStaticSlide) {
        if (typeof isStaticSlide === 'undefined') {
            isStaticSlide = false;
        }
        this.isStaticSlide = isStaticSlide;
        this.status = SlideStatus.NOT_INITIALIZED;
        this.slider = slider;
        this.slider.isFirstSlide = true;

        this.$slideElement = $slideElement;

        $slideElement.data('slide', this);

        if (!slider.parameters.admin) {
            this.minimumSlideDuration = $slideElement.data('slide-duration');
            if (!$.isNumeric(this.minimumSlideDuration)) {
                this.minimumSlideDuration = 0;
            }
        } else {
            this.minimumSlideDuration = 0;
        }

        this.findLayers();

        if (!this.slider.parameters.admin || !$slideElement.is(this.slider.adminGetCurrentSlideElement())) {
            this.initResponsiveMode();
        }

        this.status = SlideStatus.INITIALIZED;

        this.playOnce = (!this.slider.isAdmin && this.slider.parameters.layerMode.playOnce);
    };

    Slide.prototype.isActive = function () {
        return this.$slideElement.hasClass('n2-ss-slide-active');
    };

    Slide.prototype.findLayers = function () {
        this.$layers = this.$slideElement.find('.n2-ss-layer')
            .each($.proxy(function (i, el) {
                var $el = $(el);
                for (var j = 0; j < responsiveProperties.length; j++) {
                    var property = responsiveProperties[j];
                    $el.data('desktop' + property, parseFloat(el.style[property]));
                }
                var parent = this.getLayerProperty($el, 'parentid');
                if (typeof parent !== 'undefined' && parent) {
                    parent = $('#' + parent);
                    if (parent.length > 0) {
                        $el.data('parent', parent);
                    }
                } else {
                    $el.data('parent', false);
                }
            }, this));
        this.$parallax = this.$layers.filter('[data-parallax]');
    };

    Slide.prototype.getLayerResponsiveProperty = function (layer, mode, property) {
        var value = layer.data(mode + property);
        if (typeof value != 'undefined') {
            return value;
        }
        if (mode != 'desktopportrait') {
            return layer.data('desktopportrait' + property);
        }
        return 0;
    };

    Slide.prototype.getLayerProperty = function (layer, property) {
        return layer.data(property);
    };

    Slide.prototype.initResponsiveMode = function () {
        this.slider.sliderElement.on('SliderDeviceOrientation', $.proxy(function (e, modes) {
            var mode = modes.device + modes.orientation.toLowerCase();
            this.currentMode = mode;
            this.$layers.each($.proxy(function (i, el) {
                var layer = $(el),
                    show = layer.data(mode),
                    parent = layer.data('parent');
                if ((typeof show == 'undefined' || parseInt(show))) {
                    if (this.getLayerProperty(layer, 'adaptivefont')) {
                        layer.css('font-size', (16 * this.getLayerResponsiveProperty(layer, this.currentMode, 'fontsize') / 100) + 'px');
                    } else {
                        layer.css('font-size', this.getLayerResponsiveProperty(layer, this.currentMode, 'fontsize') + '%');
                    }
                    layer.data('shows', 1);
                    layer.css('display', 'block');
                } else {
                    layer.data('shows', 0);
                    layer.css('display', 'none');
                }
            }, this));
        }, this))
            .on('SliderResize', $.proxy(function (e, ratios, responsive) {

                var dimensions = responsive.responsiveDimensions;

                this.$layers.each($.proxy(function (i, el) {
                    this.repositionLayer($(el), ratios, dimensions);
                }, this));
            }, this));
    };

    Slide.prototype.isDimensionPropertyAccepted = function (value) {
        if ((value + '').match(/[0-9]+%/) || value == 'auto') {
            return true;
        }
        return false;
    };

    Slide.prototype.repositionLayer = function (layer, ratios, dimensions) {
        var ratioPositionH = ratios.slideW,
            ratioSizeH = ratioPositionH,
            ratioPositionV = ratios.slideH,
            ratioSizeV = ratioPositionV;

        if (!parseInt(this.getLayerProperty(layer, 'responsivesize'))) {
            ratioSizeH = ratioSizeV = 1;
        }

        var width = this.getLayerResponsiveProperty(layer, this.currentMode, 'width');
        layer.css('width', this.isDimensionPropertyAccepted(width) ? width : (width * ratioSizeH) + 'px');
        var height = this.getLayerResponsiveProperty(layer, this.currentMode, 'height');
        layer.css('height', this.isDimensionPropertyAccepted(height) ? height : (height * ratioSizeV) + 'px');

        if (!parseInt(this.getLayerProperty(layer, 'responsiveposition'))) {
            ratioPositionH = ratioPositionV = 1;
        }


        var left = this.getLayerResponsiveProperty(layer, this.currentMode, 'left') * ratioPositionH,
            top = this.getLayerResponsiveProperty(layer, this.currentMode, 'top') * ratioPositionV,
            align = this.getLayerResponsiveProperty(layer, this.currentMode, 'align'),
            valign = this.getLayerResponsiveProperty(layer, this.currentMode, 'valign');


        var positionCSS = {
                left: 'auto',
                top: 'auto',
                right: 'auto',
                bottom: 'auto'
            },
            parent = this.getLayerProperty(layer, 'parent');

        if (parent && parent.data('shows')) {
            var position = getPos(parent),
                p = {left: 0, top: 0};

            switch (this.getLayerResponsiveProperty(layer, this.currentMode, 'parentalign')) {
                case 'right':
                    p.left = position.left + parent.width();
                    break;
                case 'center':
                    p.left = position.left + parent.width() / 2;
                    break;
                default:
                    p.left = position.left;
            }

            switch (align) {
                case 'right':
                    positionCSS.right = (layer.parent().width() - p.left - left) + 'px';
                    break;
                case 'center':
                    positionCSS.left = (p.left + left - layer.width() / 2) + 'px';
                    break;
                default:
                    positionCSS.left = (p.left + left) + 'px';
                    break;
            }


            switch (this.getLayerResponsiveProperty(layer, this.currentMode, 'parentvalign')) {
                case 'bottom':
                    p.top = position.top + parent.height();
                    break;
                case 'middle':
                    p.top = position.top + parent.height() / 2;
                    break;
                default:
                    p.top = position.top;
            }

            switch (valign) {
                case 'bottom':
                    positionCSS.bottom = (layer.parent().height() - p.top - top) + 'px';
                    break;
                case 'middle':
                    positionCSS.top = (p.top + top - layer.height() / 2) + 'px';
                    break;
                default:
                    positionCSS.top = (p.top + top) + 'px';
                    break;
            }


        } else {
            switch (align) {
                case 'right':
                    positionCSS.right = -left + 'px';
                    break;
                case 'center':
                    positionCSS.left = ((this.isStaticSlide ? layer.parent().width() : dimensions.slide.width) / 2 + left - layer.width() / 2) + 'px';
                    break;
                default:
                    positionCSS.left = left + 'px';
                    break;
            }

            switch (valign) {
                case 'bottom':
                    positionCSS.bottom = -top + 'px';
                    break;
                case 'middle':
                    positionCSS.top = ((this.isStaticSlide ? layer.parent().height() : dimensions.slide.height) / 2 + top - layer.height() / 2) + 'px';
                    break;
                default:
                    positionCSS.top = top + 'px';
                    break;
            }
        }
        layer.css(positionCSS);
    };

    Slide.prototype.setZero = function () {
        this.$slideElement.trigger('layerSetZero', this);
    };

    Slide.prototype.setZeroAll = function () {
        this.$slideElement.trigger('layerSetZeroAll', this);
    };

    Slide.prototype.setStart = function () {
        if (this.status == SlideStatus.INITIALIZED) {
            this.$slideElement.trigger('layerAnimationSetStart');
            this.status = SlideStatus.READY_TO_START;
        }
    };

    Slide.prototype.playIn = function () {
        if (this.status == SlideStatus.READY_TO_START) {
            this.status = SlideStatus.PLAYING;
            this.$slideElement.trigger('layerAnimationPlayIn');
        }
    };

    Slide.prototype.playOut = function () {
        if (this.status == SlideStatus.PLAYING) {
            var deferreds = [];
            this.$slideElement.triggerHandler('beforeMainSwitch', [deferreds]);

            $.when.apply($, deferreds)
                .done($.proxy(function () {
                    this.onOutAnimationsPlayed();
                }, this));
        } else {
            this.onOutAnimationsPlayed();
        }
    };

    Slide.prototype.onOutAnimationsPlayed = function () {
        if (!this.playOnce) {
            this.status = SlideStatus.INITIALIZED;
        } else {
            this.status = SlideStatus.ENDED;
        }
        this.$slideElement.trigger('layerAnimationCompleteOut');
    };

    Slide.prototype.pause = function () {
        this.$slideElement.triggerHandler('layerPause');
    };

    Slide.prototype.reset = function () {
        this.$slideElement.triggerHandler('layerReset');
        this.status = SlideStatus.INITIALIZED;
    };

    Slide.prototype.getTimeline = function () {
        return this.layers.getTimeline();
    };

    scope.NextendSmartSliderSlide = Slide;

    function SlideLayers(slide, $layers, mode, ratios) {
        this.layerAnimations = [];
        this.slide = slide;
        slide.$slideElement.off(".n2-ss-animations");
        for (var i = 0; i < $layers.length; i++) {
            var $layer = $layers.eq(i);
            this.layerAnimations.push(new SlideLayerAnimations(slide, this, $layer, $layer.find('.n2-ss-layer-mask, .n2-ss-layer-parallax').addBack().last(), mode, ratios));
        }
    };

    SlideLayers.prototype.refresh = function (ratios) {
        for (var i = 0; i < this.layerAnimations.length; i++) {
            this.layerAnimations[i].refresh(ratios);
        }
    };

    SlideLayers.prototype.getTimeline = function () {
        var timeline = new NextendTimeline({
            paused: 1
        });
        for (var i = 0; i < this.layerAnimations.length; i++) {
            var animation = this.layerAnimations[i];
            timeline.add(animation.linearTimeline, 0);
            animation.linearTimeline.paused(false);

        }
        return timeline;
    };
    scope.NextendSmartSliderSlideLayers = SlideLayers;

})(n2, window);