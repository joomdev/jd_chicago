;
(function ($, scope) {

    function NextendElementMirror(id) {
        this.element = $('#' + id).on('nextendChange', $.proxy(this.onChange, this));
        this.tr = this.element.closest('tr').nextAll();
        this.onChange();
    }

    NextendElementMirror.prototype.onChange = function () {
        var value = parseInt(this.element.val());

        if (value) {
            this.tr.css('display', 'none');
        } else {
            this.tr.css('display', '');
        }

    };

    scope.NextendElementMirror = NextendElementMirror;

})(n2, window);
