;
(function ($, scope) {

    function NextendElementEnabled(id, selector) {
        this.element = $('#' + id).on('nextendChange', $.proxy(this.onChange, this));
        this.hide = this.element.closest('tr').nextAll().add(selector);
        this.onChange();
    }

    NextendElementEnabled.prototype.onChange = function () {
        var value = parseInt(this.element.val());

        if (value) {
            this.hide.css('display', '');
        } else {
            this.hide.css('display', 'none');
        }

    };

    scope.NextendElementEnabled = NextendElementEnabled;

})(n2, window);
