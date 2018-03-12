(function ($, scope, undefined) {
    "use strict";
    function NextendSmartSliderControlKeyboard(slider, direction, parameters) {

        this.slider = slider;

        this.parameters = $.extend({}, parameters);

        if (direction == 'vertical') {
            this.parseEvent = NextendSmartSliderControlKeyboard.prototype.parseEventVertical;
        } else {
            this.parseEvent = NextendSmartSliderControlKeyboard.prototype.parseEventHorizontal;
        }

        $(document).on('keydown', $.proxy(this.onKeyDown, this));

        slider.controls.keyboard = this;
    };

    NextendSmartSliderControlKeyboard.prototype.onKeyDown = function (e) {

        if (e.target.tagName.match(/BODY|DIV|IMG/)) {
            e = e || window.event;
            if (this.parseEvent.call(this, e)) {
                e.preventDefault();
            }
        }
    };

    NextendSmartSliderControlKeyboard.prototype.parseEventHorizontal = function (e) {
        switch (e.keyCode) {
            case 39: // right arrow
                this.slider.next();
                return true;
            case 37: // left arrow
                this.slider.previous();
                return true;
            default:
                return false;
        }
    };

    NextendSmartSliderControlKeyboard.prototype.parseEventVertical = function (e) {
        switch (e.keyCode) {
            case 40: // down arrow
                this.slider.next();
                return true;
            case 38: // up arrow
                this.slider.previous();
                return true;
            default:
                return false;
        }
    };
    scope.NextendSmartSliderControlKeyboard = NextendSmartSliderControlKeyboard;
})(n2, window);