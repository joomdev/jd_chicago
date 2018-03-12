(function ($, scope, undefined) {

    function NextendElementFolders(id, parameters) {
        this.element = $('#' + id);

        this.field = this.element.data('field');

        this.parameters = parameters;

        this.editButton = $('#' + id + '_edit')
            .on('click', $.proxy(this.edit, this));

        this.button = $('#' + id + '_button').on('click', $.proxy(this.open, this));

        this.element.siblings('.n2-form-element-clear')
            .on('click', $.proxy(this.clear, this));

        NextendElement.prototype.constructor.apply(this, arguments);
    };

    NextendElementFolders.prototype = Object.create(NextendElement.prototype);
    NextendElementFolders.prototype.constructor = NextendElementFolders;

    NextendElementFolders.prototype.clear = function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.val('');
    };

    NextendElementFolders.prototype.val = function (value) {
        this.element.val(value);
        this.triggerOutsideChange();
    };

    NextendElementFolders.prototype.open = function (e) {
        e.preventDefault();
        nextend.imageHelper.openFoldersLightbox($.proxy(this.val, this));
    };

    scope.NextendElementFolders = NextendElementFolders;
})(n2, window);