;
(function ($, scope) {

    function NextendElementSwitcher(id, values) {

        this.element = $('#' + id);

        this.options = this.element.parent().find('.n2-switcher-unit');

        this.active = this.options.index(this.options.filter('.n2-active'));

        this.values = values;

        for (var i = 0; i < this.options.length; i++) {
            this.options.eq(i).on('click', $.proxy(this.switch, this, i));
        }

        NextendElement.prototype.constructor.apply(this, arguments);
    };

    NextendElementSwitcher.prototype = Object.create(NextendElement.prototype);
    NextendElementSwitcher.prototype.constructor = NextendElementSwitcher;


    NextendElementSwitcher.prototype.switch = function (i, e) {
        this.element.val(this.values[i]);
        this.setSelected(i);

        this.triggerOutsideChange();
    };

    NextendElementSwitcher.prototype.insideChange = function (value) {
        var i = $.inArray(value, this.values);

        this.element.val(this.values[i]);
        this.setSelected(i);

        this.triggerInsideChange();
    };

    NextendElementSwitcher.prototype.setSelected = function (i) {
        this.options.eq(this.active).removeClass('n2-active');
        this.options.eq(i).addClass('n2-active');
        this.active = i;
    };

    scope.NextendElementSwitcher = NextendElementSwitcher;

})(n2, window);
