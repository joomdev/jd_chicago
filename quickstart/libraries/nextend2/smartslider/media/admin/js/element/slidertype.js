;
(function ($, scope) {

    function NextendElementSliderType(id) {
        this.element = $('#' + id);

        this.setAttribute();

        this.element.on('nextendChange', $.proxy(this.setAttribute, this));
    };

    NextendElementSliderType.prototype.setAttribute = function () {

        $('#n2-admin').attr('data-slider-type', this.element.val());
    };

    scope.NextendElementSliderType = NextendElementSliderType;

})(n2, window);
