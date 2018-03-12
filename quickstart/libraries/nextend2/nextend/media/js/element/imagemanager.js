;
(function ($, scope) {

    function NextendElementImageManager(id, parameters) {
        this.element = $('#' + id);
        $('#' + id + '_manage').on('click', $.proxy(this.show, this));

        this.parameters = parameters;

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementImageManager.prototype = Object.create(NextendElement.prototype);
    NextendElementImageManager.prototype.constructor = NextendElementImageManager;


    NextendElementImageManager.prototype.show = function (e) {
        e.preventDefault();
        nextend.imageManager.show(this.element.val(), $.proxy(this.save, this));
    };

    NextendElementImageManager.prototype.save = function () {

    };

    scope.NextendElementImageManager = NextendElementImageManager;

})(n2, window);