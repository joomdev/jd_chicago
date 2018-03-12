;
(function ($, scope) {

    function NextendElementCheckbox(id, values) {
        this.separator = '||';

        this.element = $('#' + id);

        this.values = values;

        this.checkboxes = this.element.parent().find('.n2-checkbox-option');

        this.states = this.element.val().split(this.separator);

        for (var i = 0; i < this.checkboxes.length; i++) {
            if (typeof this.states[i] === 'undefined' || this.states[i] != this.values[i]) {
                this.states[i] = '';
            }

            this.checkboxes.eq(i).on('click', $.proxy(this.switchCheckbox, this, i));
        }

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementCheckbox.prototype = Object.create(NextendElement.prototype);
    NextendElementCheckbox.prototype.constructor = NextendElementCheckbox;


    NextendElementCheckbox.prototype.switchCheckbox = function (i) {
        if (this.states[i] == this.values[i]) {
            this.states[i] = '';
            this.setSelected(i, 0);
        } else {
            this.states[i] = this.values[i];
            this.setSelected(i, 1);
        }
        this.element.val(this.states.join(this.separator));

        this.triggerOutsideChange();
    };

    NextendElementCheckbox.prototype.insideChange = function (values) {

        var states = values.split(this.separator);

        for (var i = 0; i < this.checkboxes.length; i++) {
            if (typeof states[i] === 'undefined' || states[i] != this.values[i]) {
                this.states[i] = '';
                this.setSelected(i, 0);
            } else {
                this.states[i] = this.values[i];
                this.setSelected(i, 1);
            }

        }

        this.element.val(this.states.join(this.separator));

        this.triggerInsideChange();
    };

    NextendElementCheckbox.prototype.setSelected = function (i, state) {
        if (state) {
            this.checkboxes.eq(i)
                .addClass('n2-active');
        } else {
            this.checkboxes.eq(i)
                .removeClass('n2-active');
        }
    };


    scope.NextendElementCheckbox = NextendElementCheckbox;

})(n2, window);