;
(function ($, scope) {

    function NextendElementOnoff(id) {
        this.element = $('#' + id);

        this.onoff = this.element.parent()
            .on('click', $.proxy(this.switch, this));

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementOnoff.prototype = Object.create(NextendElement.prototype);
    NextendElementOnoff.prototype.constructor = NextendElementOnoff;


    NextendElementOnoff.prototype.switch = function () {
        var value = parseInt(this.element.val());
        if (value) {
            value = 0;
        } else {
            value = 1;
        }
        this.element.val(value);
        this.setSelected(value);

        this.triggerOutsideChange();
    };

    NextendElementOnoff.prototype.insideChange = function (value) {
        value = parseInt(value);
        this.element.val(value);
        this.setSelected(value);

        this.triggerInsideChange();
    };

    NextendElementOnoff.prototype.setSelected = function (state) {
        if (state) {
            this.onoff.addClass('n2-onoff-on');
        } else {
            this.onoff.removeClass('n2-onoff-on');
        }
    };

    scope.NextendElementOnoff = NextendElementOnoff;

})(n2, window);
