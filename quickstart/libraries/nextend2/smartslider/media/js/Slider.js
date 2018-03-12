(function ($, scope, undefined) {
    function NextendSmartSlider() {
        this.sliders = {};
        this.readys = {};

        this._resetCounters = [];
    }

    NextendSmartSlider.prototype.makeReady = function (id, slider) {
        this.sliders[id] = slider;
        if (typeof this.readys[id] !== 'undefined') {
            for (var i = 0; i < this.readys[id].length; i++) {
                this.readys[id][i].call(slider, slider, slider.sliderElement);
            }
        }
    };

    NextendSmartSlider.prototype.ready = function (id, callback) {
        if (typeof this.sliders[id] !== 'undefined') {
            callback.call(this.sliders[id], this.sliders[id], this.sliders[id].sliderElement);
        } else {
            if (typeof this.readys[id] == 'undefined') {
                this.readys[id] = [];
            }
            this.readys[id].push(callback);
        }
    };

    NextendSmartSlider.prototype.trigger = function (el, event) {
        var $el = n2(el),
            split = event.split(','),
            slide = $el.closest('.n2-ss-slide,.n2-ss-static-slide');

        if (split.length > 1) {
            if ($.inArray(el, this._resetCounters) == -1) {
                this._resetCounters.push(el);

                slide.on('layerAnimationSetStart.resetCounter', function () {
                    $el.data('eventCounter', 0);
                });
            }
            var counter = $el.data('eventCounter') || 0
            event = split[counter];
            counter++;
            if (counter > split.length - 1) {
                counter = 0;
            }
            $el.data('eventCounter', counter);
        }
        slide.triggerHandler(event);
    };

    NextendSmartSlider.prototype.applyAction = function (el, action) {
        var ss = n2(el).closest('.n2-ss-slider').data('ss');
        ss[action].apply(ss, Array.prototype.slice.call(arguments, 2));
    };

    window.n2ss = new NextendSmartSlider();
})(n2, window);