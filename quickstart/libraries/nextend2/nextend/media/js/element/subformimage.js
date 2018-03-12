;
(function ($, scope) {

    function NextendElementSubformImage(id, options) {

        this.element = $('#' + id);

        this.options = $('#' + options).find('.n2-subform-image-option');

        this.subform = this.element.data('field');

        this.active = this.getIndex(this.options.filter('.n2-active').get(0));

        for (var i = 0; i < this.options.length; i++) {
            this.options.eq(i).on('click', $.proxy(this.selectOption, this));
        }

        NextendElement.prototype.constructor.apply(this, arguments);
    };

    NextendElementSubformImage.prototype = Object.create(NextendElement.prototype);
    NextendElementSubformImage.prototype.constructor = NextendElementSubformImage;


    NextendElementSubformImage.prototype.selectOption = function (e) {
        var index = this.getIndex(e.currentTarget);
        if (index != this.active) {

            this.options.eq(index).addClass('n2-active');
            this.options.eq(this.active).removeClass('n2-active');

            this.active = index;

            var value = this.subform.list.select.find('option').eq(index).val();
            this.subform.list.insideChange(value);
        }
    };

    NextendElementSubformImage.prototype.getIndex = function (option) {
        return $.inArray(option, this.options);
    };
    scope.NextendElementSubformImage = NextendElementSubformImage;

})(n2, window);