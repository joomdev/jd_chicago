;
(function ($, scope) {

    function NextendElementSubform(id, target, tab, originalValue) {
        this.id = id;

        this.element = $('#' + id);

        this.target = $('#' + target);

        this.tab = tab;

        this.originalValue = originalValue;

        this.form = this.element.closest('form').data('form');

        this.list = this.element.data('field');

        this.element.on('nextendChange', $.proxy(this.loadSubform, this));

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementSubform.prototype = Object.create(NextendElement.prototype);
    NextendElementSubform.prototype.constructor = NextendElementSubform;

    NextendElementSubform.prototype.loadSubform = function () {
        var value = this.element.val();
        if (value == 'disabled') {
            this.target.html('');
        } else {
            var values = [];
            if (value == this.originalValue) {
                values = this.form.values;
            }

            var data = {
                id: this.id,
                values: values,
                tab: this.tab,
                value: value
            };

            NextendAjaxHelper.ajax({
                type: "POST",
                url: NextendAjaxHelper.makeAjaxUrl(this.form.url),
                data: data,
                dataType: 'json'
            }).done($.proxy(this.load, this));
        }
    };

    NextendElementSubform.prototype.load = function (response) {
        this.target.html(response.data.html);
        eval(response.data.scripts);
    };

    scope.NextendElementSubform = NextendElementSubform;

})(n2, window);
