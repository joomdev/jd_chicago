;
(function ($, scope) {

    function NextendElementList(id, multiple) {

        this.separator = '||';

        this.element = $('#' + id).on('change', $.proxy(this.onHiddenChange, this));

        this.select = $('#' + id + '_select').on('change', $.proxy(this.onChange, this));

        this.multiple = multiple;

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementList.prototype = Object.create(NextendElement.prototype);
    NextendElementList.prototype.constructor = NextendElementList;

    NextendElementList.prototype.onHiddenChange = function () {
        var value = this.element.val();
        if (value && value != this.select.val()) {
            this.insideChange(value);
        }
    };

    NextendElementList.prototype.onChange = function () {
        var value = this.select.val();
        if (value !== null && typeof value === 'object') {
            value = value.join(this.separator);
        }
        this.element.val(value);

        this.triggerOutsideChange();
    };

    NextendElementList.prototype.insideChange = function (value) {
        if (typeof value === 'array') {
            this.select.val(value.split(this.separator));
        } else {
            this.select.val(value);
        }

        this.element.val(value);

        this.triggerInsideChange();
    };

    scope.NextendElementList = NextendElementList;

})(n2, window);
