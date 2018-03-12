(function ($, scope) {

    function NextendElementNumber(id, min, max) {
        this.min = min;
        this.max = max;

        this.element = $('#' + id).on({
            focus: $.proxy(this.focus, this),
            blur: $.proxy(this.blur, this),
            change: $.proxy(this.change, this)
        });
        this.parent = this.element.parent();

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementNumber.prototype = Object.create(NextendElement.prototype);
    NextendElementNumber.prototype.constructor = NextendElementNumber;


    NextendElementNumber.prototype.focus = function () {
        this.parent.addClass('focus');

        this.element.on('keypress.n2-text', $.proxy(function (e) {
            if (e.which == 13) {
                this.element.off('keypress.n2-text');
                this.element.trigger('blur');
            }
        }, this));
    };

    NextendElementNumber.prototype.blur = function () {
        this.parent.removeClass('focus');
    };

    NextendElementNumber.prototype.change = function () {
        var validated = this.validate(this.element.val());
        if (validated === true) {
            this.triggerOutsideChange();
        } else {
            this.element.val(validated).trigger('change');
        }
    };

    NextendElementNumber.prototype.insideChange = function (value) {
        var validated = this.validate(value);
        if (validated === true) {
            this.element.val(value);
        } else {
            this.element.val(validated);
        }

        this.triggerInsideChange();
    };

    NextendElementNumber.prototype.validate = function (value) {
        var validatedValue = parseFloat(value);
        if (isNaN(validatedValue)) {
            validatedValue = 0;
        }
        validatedValue = Math.max(this.min, Math.min(this.max, validatedValue));
        if (validatedValue != value) {
            return validatedValue;
        }
        return true;
    };

    scope.NextendElementNumber = NextendElementNumber;
})(n2, window);