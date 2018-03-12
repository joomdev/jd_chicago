(function ($, scope, undefined) {
    "use strict";
    function NextendSmartSliderControlScroll(slider) {

        this.preventScroll = false

        this.slider = slider;

        // handled by jquery.mousewheel.js
        slider.sliderElement.on('mousewheel', $.proxy(this.onMouseWheel, this));

        slider.controls.scroll = this;
    };

    NextendSmartSliderControlScroll.prototype.onMouseWheel = function (e) {
        if (!this.preventScroll) {
            this.preventScroll = true;
            if (e.deltaY > 0) {
                if (this.slider.previous()) {
                    // Stops the browser normal scroll
                    e.preventDefault();
                }
            } else {
                if (this.slider.next()) {
                    // Stops the browser normal scroll
                    e.preventDefault();
                }
            }
            setTimeout($.proxy(function () {
                this.preventScroll = false;
            }, this), 400);
        } else {
            e.preventDefault();
        }
    };
    scope.NextendSmartSliderControlScroll = NextendSmartSliderControlScroll;
})(n2, window);