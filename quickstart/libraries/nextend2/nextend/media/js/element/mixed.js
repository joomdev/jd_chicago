;
(function ($, scope) {

    function NextendElementMixed(id, elements, separator) {

        this.element = $('#' + id);

        this.elements = [];
        for (var i = 0; i < elements.length; i++) {
            this.elements.push($('#' + elements[i])
                .on('outsideChange', $.proxy(this.onFieldChange, this)));
        }

        this.separator = separator;

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementMixed.prototype = Object.create(NextendElement.prototype);
    NextendElementMixed.prototype.constructor = NextendElementMixed;


    NextendElementMixed.prototype.onFieldChange = function () {
        this.element.val(this.getValue());

        this.triggerOutsideChange();
    };

    NextendElementMixed.prototype.insideChange = function (value) {
        this.element.val(value);

        var values = value.split(this.separator);

        for (var i = 0; i < this.elements.length; i++) {
            this.elements[i].data('field').insideChange(values[i]);
        }

        this.triggerInsideChange();
    };

    NextendElementMixed.prototype.getValue = function () {
        var values = [];
        for (var i = 0; i < this.elements.length; i++) {
            values.push(this.elements[i].val());
        }

        return values.join(this.separator);
    };

    scope.NextendElementMixed = NextendElementMixed;

})(n2, window);