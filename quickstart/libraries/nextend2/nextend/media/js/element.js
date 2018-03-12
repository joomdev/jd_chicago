;
(function ($, scope) {

    function NextendElement() {
        this.element.data('field', this);
    };

    NextendElement.prototype.triggerOutsideChange = function () {
        this.element.triggerHandler('outsideChange', this);
        this.element.triggerHandler('nextendChange', this);
    };

    NextendElement.prototype.triggerInsideChange = function () {
        this.element.triggerHandler('insideChange', this);
        this.element.triggerHandler('nextendChange', this);
    };

    scope.NextendElement = NextendElement;

})(n2, window);
