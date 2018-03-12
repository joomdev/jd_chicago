(function ($, scope, undefined) {
    "use strict";

    function NextendSmartSliderWidgetShadow(id, parameters) {
        this.slider = window[id];

        this.slider.started($.proxy(this.start, this, id, parameters));
    };


    NextendSmartSliderWidgetShadow.prototype.start = function (id, parameters) {
        this.shadow = this.slider.sliderElement.find('.nextend-shadow');
        this.slider.responsive.addStaticMargin('Bottom', this);
    };

    NextendSmartSliderWidgetShadow.prototype.isVisible = function () {
        return this.shadow.is(':visible');
    };

    NextendSmartSliderWidgetShadow.prototype.getSize = function () {
        return this.shadow.height();
    };

    scope.NextendSmartSliderWidgetShadow = NextendSmartSliderWidgetShadow;
})(n2, window);