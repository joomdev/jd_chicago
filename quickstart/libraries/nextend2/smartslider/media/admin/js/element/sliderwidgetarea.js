;
(function ($, scope) {

    function NextendElementSliderWidgetArea(id) {
        this.element = $('#' + id);

        this.area = $('#' + id + '_area');

        this.areas = this.area.find('.n2-area');

        this.areas.on('click', $.proxy(this.chooseArea, this));

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementSliderWidgetArea.prototype = Object.create(NextendElement.prototype);
    NextendElementSliderWidgetArea.prototype.constructor = NextendElementSliderWidgetArea;


    NextendElementSliderWidgetArea.prototype.chooseArea = function (e) {
        var value = parseInt($(e.target).data('area'));

        this.element.val(value);
        this.setSelected(value);

        this.triggerOutsideChange();
    };

    NextendElementSliderWidgetArea.prototype.insideChange = function (value) {
        value = parseInt(value);
        this.element.val(value);
        this.setSelected(value);

        this.triggerInsideChange();
    };

    NextendElementSliderWidgetArea.prototype.setSelected = function (index) {
        this.areas.removeClass('n2-active');
        this.areas.eq(index - 1).addClass('n2-active');
    };

    scope.NextendElementSliderWidgetArea = NextendElementSliderWidgetArea;

})(n2, window);
