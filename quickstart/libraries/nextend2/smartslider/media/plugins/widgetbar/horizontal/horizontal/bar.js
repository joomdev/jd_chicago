(function ($, scope, undefined) {
    "use strict";
    function NextendSmartSliderWidgetBarHorizontal(id, bars, parameters) {
        this.slider = window[id];

        this.slider.started($.proxy(this.start, this, id, bars, parameters));
    };

    NextendSmartSliderWidgetBarHorizontal.prototype.start = function (id, bars, parameters) {
        if (this.slider.sliderElement.data('bar')) {
            return false;
        }
        this.slider.sliderElement.data('bar', this);

        this.offset = 0;
        this.tween = null;

        this.originalBars = this.bars = bars;
        this.bar = this.slider.sliderElement.find('.nextend-bar');
        this.innerBar = this.bar.find('> div');

        this.slider.sliderElement.on('slideCountChanged', $.proxy(this.onSlideCountChanged, this));

        if (parameters.animate) {
            this.slider.sliderElement.on('mainAnimationStart', $.proxy(this.onSliderSwitchToAnimateStart, this));
        } else {
            this.slider.sliderElement.on('sliderSwitchTo', $.proxy(this.onSliderSwitchTo, this));
        }

        if (parameters.overlay == 0) {
            var side = false;
            switch (parameters.area) {
                case 1:
                    side = 'Top';
                    break;
                case 12:
                    side = 'Bottom';
                    break;
            }
            if (side) {
                this.offset = parseFloat(this.bar.data('offset'));
                this.slider.responsive.addStaticMargin(side, this);
            }
        }
    };

    NextendSmartSliderWidgetBarHorizontal.prototype.onSliderSwitchTo = function (e, targetSlideIndex) {
        this.innerBar.html(this.bars[targetSlideIndex]);
    };

    NextendSmartSliderWidgetBarHorizontal.prototype.onSliderSwitchToAnimateStart = function () {
        var deferred = $.Deferred();
        this.slider.sliderElement.on('mainAnimationComplete.n2Bar', $.proxy(this.onSliderSwitchToAnimateEnd, this, deferred));
        if (this.tween) {
            this.tween.pause();
        }
        NextendTween.to(this.innerBar, 0.3, {
            opacity: 0,
            onComplete: function () {
                deferred.resolve();
            }
        }).play();
    };

    NextendSmartSliderWidgetBarHorizontal.prototype.onSliderSwitchToAnimateEnd = function (deferred, e, animation, currentSlideIndex, targetSlideIndex) {
        this.slider.sliderElement.off('.n2Bar');
        deferred.done($.proxy(function () {
            var innerBar = this.innerBar.clone();
            this.innerBar.remove();
            this.innerBar = innerBar.css('opacity', 0)
                .html(this.bars[targetSlideIndex])
                .appendTo(this.bar);

            this.tween = NextendTween.to(this.innerBar, 0.3, {
                opacity: 1
            }).play();
        }, this));
    };

    NextendSmartSliderWidgetBarHorizontal.prototype.isVisible = function () {
        return this.bar.is(':visible');
    };

    NextendSmartSliderWidgetBarHorizontal.prototype.getSize = function () {
        return this.bar.height() + this.offset;
    };

    NextendSmartSliderWidgetBarHorizontal.prototype.onSlideCountChanged = function (e, newCount, slidesInGroup) {
        this.bars = [];
        for (var i = 0; i < this.originalBars.length; i++) {
            if (i % slidesInGroup == 0) {
                this.bars.push(this.originalBars[i]);
            }
        }
    };

    scope.NextendSmartSliderWidgetBarHorizontal = NextendSmartSliderWidgetBarHorizontal;
})(n2, window);