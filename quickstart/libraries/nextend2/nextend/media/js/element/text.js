(function ($, scope) {

    function NextendElementText(id) {
        this.element = $('#' + id).on({
            focus: $.proxy(this.focus, this),
            blur: $.proxy(this.blur, this),
            change: $.proxy(this.change, this)
        });

        this.tagName = this.element.prop('tagName');

        this.parent = this.element.parent();

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementText.prototype = Object.create(NextendElement.prototype);
    NextendElementText.prototype.constructor = NextendElementText;


    NextendElementText.prototype.focus = function () {
        this.parent.addClass('focus');

        if (this.tagName != 'TEXTAREA') {
            this.element.on('keypress.n2-text', $.proxy(function (e) {
                if (e.which == 13) {
                    this.element.off('keypress.n2-text');
                    this.element.trigger('blur');
                }
            }, this));
        }
    };

    NextendElementText.prototype.blur = function () {
        this.parent.removeClass('focus');
    };

    NextendElementText.prototype.change = function () {

        this.triggerOutsideChange();
    };

    NextendElementText.prototype.insideChange = function (value) {
        this.element.val(value);

        this.triggerInsideChange();
    };

    scope.NextendElementText = NextendElementText;

})(n2, window);