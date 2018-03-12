(function ($, scope, undefined) {

    function NextendSmartSliderAdminInlineField() {

        this.$input = $('<input type="text" name="name" />')
            .on({
                mouseup: function (e) {
                    e.stopPropagation();
                },
                keyup: $.proxy(function (e) {
                    if (e.keyCode == 27) {
                        this.cancel();
                    }
                }, this),
                blur: $.proxy(this.save, this)
            });

        this.$form = $('<form class="n2-inline-form"></form>')
            .append(this.$input)
            .on('submit', $.proxy(this.save, this));
    }

    NextendSmartSliderAdminInlineField.prototype.injectNode = function ($targetNode, value) {
        this.$input.val(value);
        $targetNode.append(this.$form);
        this.$input.focus();
    };

    NextendSmartSliderAdminInlineField.prototype.save = function (e) {
        e.preventDefault();
        this.$input.trigger('valueChanged', [this.$input.val()]);
        this.$input.off('blur');
        this.destroy();
    };

    NextendSmartSliderAdminInlineField.prototype.cancel = function () {
        this.$input.trigger('cancel');
        this.destroy();
    };

    NextendSmartSliderAdminInlineField.prototype.destroy = function () {
        this.$input.off('blur')
        this.$form.remove();
    };

    scope.NextendSmartSliderAdminInlineField = NextendSmartSliderAdminInlineField;

})(n2, window);