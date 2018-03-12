"use strict";
(function ($, scope) {
    function NextendElementWidgetPosition(id) {

        this.element = $('#' + id + '-mode');
        this.container = this.element.closest('.n2-form-element-mixed');

        this.tabs = this.container.find('> .n2-mixed-group');

        this.element.on('nextendChange', $.proxy(this.onChange, this));

        this.onChange();
    };

    NextendElementWidgetPosition.prototype.onChange = function () {
        var value = this.element.val();

        if (value == 'advanced') {
            this.tabs.eq(2).css('display', '');
            this.tabs.eq(1).css('display', 'none');
        } else {
            this.tabs.eq(1).css('display', '');
            this.tabs.eq(2).css('display', 'none');
        }
    };

    scope.NextendElementWidgetPosition = NextendElementWidgetPosition;

})(n2, window);
