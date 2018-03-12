(function ($, scope, undefined) {
    "use strict";
    var pointer = window.navigator.pointerEnabled || window.navigator.msPointerEnabled;

    function NextendSmartSliderControlTouch(slider, direction, parameters) {
        this.currentAnimation = null;
        this.slider = slider;

        this._animation = slider.mainAnimation;

        this.parameters = $.extend({
            fallbackToMouseEvents: true
        }, parameters);

        this.swipeElement = this.slider.sliderElement.find('> div').eq(0);

        if (direction == 'vertical') {
            this.setVertical();
        } else if (direction == 'horizontal') {
            this.setHorizontal();
        }

        this.swipeElement.addClass('unselectable').swipe({
            axis: this._direction.axis,
            threshold: 10,
            preventDefaultEvents: false,
            triggerOnTouchLeave: true,
            fallbackToMouseEvents: this.parameters.fallbackToMouseEvents,
            swipeStatus: $.proxy(this.onSwipeStatus, this),
            tap: $.proxy(this.onTap, this)
        }).on('dragstart', function (e) {
            e.preventDefault();
        });

        if (!this.parameters.fallbackToMouseEvents) {
            this.swipeElement.on('click', $.proxy(this.onTap, this));
        }

        if (this.parameters.fallbackToMouseEvents) {
            this.swipeElement.addClass('n2-grab');
        }

        slider.controls.touch = this;
    };

    NextendSmartSliderControlTouch.prototype.setHorizontal = function () {

        this._property = 'width';

        this._direction = {
            left: 'next',
            right: 'previous',
            up: null,
            down: null,
            axis: 'horizontal'
        };

        if (pointer) {
            this.swipeElement.css('-ms-touch-action', 'pan-y');
            this.swipeElement.css('touch-action', 'pan-y');
        }
    };

    NextendSmartSliderControlTouch.prototype.setVertical = function () {

        this._property = 'height';

        this._direction = {
            left: null,
            right: null,
            up: 'next',
            down: 'previous',
            axis: 'vertical'
        };

        if (pointer) {
            this.swipeElement.css('-ms-touch-action', 'pan-x');
            this.swipeElement.css('touch-action', 'pan-x');
        }
    };

    NextendSmartSliderControlTouch.prototype.onSwipeStatus = function (event, phase, direction, distance, duration, fingers) {
        if (distance > 10 && direction != null && this._direction[direction] !== null) {
            event.preventDefault();
            if (this.currentAnimation === null) {
                if (this._animation.state != 'ended') {
                    // skip the event as the current animation is still playing
                    return;
                }
                this.swipeElement.addClass('n2-grabbing');

                // Force the main animation into touch mode horizontal/vertical
                this._animation.setTouch(this._direction.axis);

                this.currentAnimation = {
                    direction: direction,
                    percent: 0
                };
                this.slider[this._direction[direction]](false);

            }
            if (this.currentAnimation.percent < 1 && this.currentAnimation.direction == direction) {
                var percent = distance / this.slider.dimensions.slider[this._property];
                if (percent <= 1) {
                    this.currentAnimation.percent = percent;
                    this._animation.timeline.progress(percent);
                }
            }
        }

        /**
         * The direction can be different for the last "action", so this block can't be in the previous if statement
         */
        if (this.currentAnimation !== null && (phase == "end" || phase == "cancel")) {
            var progress = this._animation.timeline.progress();
            if (progress != 1) {
                var totalDuration = this._animation.timeline.totalDuration(),
                    modifiedDuration = Math.max(totalDuration / 3, Math.min(totalDuration, duration / progress / 1000));
                if (modifiedDuration != totalDuration) {
                    this._animation.timeline.totalDuration(modifiedDuration);
                }
                this._animation.timeline.play();
            }
            this.swipeElement.removeClass('n2-grabbing');

            // Switch back the animation into the original mode when our touch is ended
            this._animation.setTouch(false);
            this.currentAnimation = null;
        }
    };

    NextendSmartSliderControlTouch.prototype.onTap = function (e) {
        if ((e.type != 'mouseup' || e.which == 1) && e.type != 'mouseout') {
            $(e.target).trigger('n2click');
        }
    };

    scope.NextendSmartSliderControlTouch = NextendSmartSliderControlTouch;

})(n2, window);