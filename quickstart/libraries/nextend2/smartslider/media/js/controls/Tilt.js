(function ($, scope, undefined) {
    "use strict";
    function NextendSmartSliderControlTilt(slider, parameters) {

        if (typeof window.DeviceOrientationEvent == 'undefined' || typeof window.orientation == 'undefined') {
            return "Not supported";
        }
        this.timeout = null;

        this.slider = slider;

        this.parameters = $.extend({
            duration: 2000
        }, parameters);

        this.orientationchange();

        window.addEventListener('orientationchange', $.proxy(this.orientationchange, this));

        window.addEventListener("deviceorientation", $.proxy(this.handleOrientation, this), true);

        slider.controls.tilt = this;
    };

    NextendSmartSliderControlTilt.prototype.orientationchange = function () {
        switch (window.orientation) {
            case -90:
            case 90:
                this.parseEvent = NextendSmartSliderControlTilt.prototype.parseEventHorizontalLandscape;
                break;
            default:
                this.parseEvent = NextendSmartSliderControlTilt.prototype.parseEventHorizontal;
                break;
        }
    };

    NextendSmartSliderControlTilt.prototype.clearTimeout = function () {
        this.timeout = null;
    };

    NextendSmartSliderControlTilt.prototype.handleOrientation = function (e) {
        if (this.timeout == null && this.parseEvent.call(this, e)) {
            this.timeout = setTimeout($.proxy(this.clearTimeout, this), this.parameters.duration);

            e.preventDefault();
        }
    };

    NextendSmartSliderControlTilt.prototype.parseEventHorizontal = function (e) {
        if (e.gamma > 10) { // right tilt
            this.slider.next();
            return true;
        } else if (e.gamma < -10) { // left tilt
            this.slider.previous();
            return true;
        }
        return false;
    };

    NextendSmartSliderControlTilt.prototype.parseEventHorizontalLandscape = function (e) {
        if (e.beta < -10) { // right tilt
            this.slider.next();
            return true;
        } else if (e.beta > 10) { // left tilt
            this.slider.previous();
            return true;
        }
        return false;
    };

    scope.NextendSmartSliderControlTilt = NextendSmartSliderControlTilt;

})(n2, window);