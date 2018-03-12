(function ($, scope) {

    function NextendElementRadio(id, values) {
        this.element = $('#' + id);

        this.values = values;

        this.parent = this.element.parent();

        this.options = this.parent.find('.n2-radio-option');

        for (var i = 0; i < this.options.length; i++) {
            this.options.eq(i).on('click', $.proxy(this.click, this));
        }

        NextendElement.prototype.constructor.apply(this, arguments);
    };

    NextendElementRadio.prototype = Object.create(NextendElement.prototype);
    NextendElementRadio.prototype.constructor = NextendElementRadio;

    NextendElementRadio.prototype.click = function (e) {
        this.changeSelectedIndex(this.options.index(e.currentTarget));
    };

    NextendElementRadio.prototype.changeSelectedIndex = function (index) {
        var value = this.values[index];

        this.element.val(value);

        this.setSelected(index);

        this.triggerOutsideChange();
    };

    NextendElementRadio.prototype.insideChange = function (value, option) {
        var index = $.inArray(value, this.values);
        if (index == '-1') {
            index = this.partialSearch(value);
        }

        if (index == '-1' && typeof option !== 'undefined') {
            index = this.addOption(value, option);
        }

        if (index != '-1') {
            this.element.val(this.values[index]);
            this.setSelected(index);

            this.triggerInsideChange();
        } else {
            // It will reset the state if the preferred value not available
            this.options.eq(0).trigger('click');
        }
    };

    NextendElementRadio.prototype.setSelected = function (index) {
        this.options.removeClass('n2-active');
        this.options.eq(index).addClass('n2-active');
    };

    NextendElementRadio.prototype.partialSearch = function (text) {
        text = text.replace(/^.*[\\\/]/, '');
        for (var i = 0; i < this.values.length; i++) {
            if (this.values[i].indexOf(text) != -1) return i;
        }
        return -1;
    };

    NextendElementRadio.prototype.addOption = function (value, option) {
        var i = this.values.push(value) - 1;
        option.appendTo(this.parent)
            .on('click', $.proxy(this.click, this));
        this.options = this.options.add(option);
        return i;
    };

    NextendElementRadio.prototype.addTabOption = function (value, label) {
        var i = this.values.push(value) - 1;
        var option = $('<div class="n2-radio-option n2-h4 n2-last">' + label + '</div>')
            .insertAfter(this.options.last().removeClass('n2-last'))
            .on('click', $.proxy(this.click, this));
        this.options = this.options.add(option);
        return i;
    };
    NextendElementRadio.prototype.removeTabOption = function (value) {
        var i = $.inArray(value, this.values);
        var option = this.options.eq(i);
        this.options = this.options.not(option);
        option.remove();
        if (i == 0) {
            this.options.eq(0).addClass('n2-first');
        }
        if (i == this.options.length) {
            this.options.eq(this.options.length - 1).addClass('n2-last');
        }

        this.values.splice(i, 1);
    };

    NextendElementRadio.prototype.moveTab = function (originalIndex, targetIndex) {

    };

    scope.NextendElementRadio = NextendElementRadio;

})(n2, window);