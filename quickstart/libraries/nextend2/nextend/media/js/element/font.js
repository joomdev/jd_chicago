;
(function ($, scope) {

    function NextendElementFont(id, parameters) {
        this.element = $('#' + id);

        this.parameters = parameters;

        this.defaultSetId = parameters.set;

        this.element.parent()
            .on('click', $.proxy(this.show, this));

        this.element.siblings('.n2-form-element-clear')
            .on('click', $.proxy(this.clear, this));

        this.name = this.element.siblings('input');

        nextend.fontManager.$.on('visualDelete', $.proxy(this.fontDeleted, this));

        this.updateName(this.element.val());

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementFont.prototype = Object.create(NextendElement.prototype);
    NextendElementFont.prototype.constructor = NextendElementFont;


    NextendElementFont.prototype.show = function (e) {
        e.preventDefault();
        if (this.parameters.style != '') {
            nextend.fontManager.setConnectedStyle(this.parameters.style);
        }
        if (this.parameters.style2 != '') {
            nextend.fontManager.setConnectedStyle2(this.parameters.style2);
        }
        if (this.defaultSetId) {
            nextend.fontManager.changeSetById(this.defaultSetId);
        }
        nextend.fontManager.show(this.element.val(), $.proxy(this.save, this), {
            previewMode: this.parameters.previewmode,
            previewHTML: this.parameters.preview
        });
    };

    NextendElementFont.prototype.clear = function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.val('');
    };

    NextendElementFont.prototype.save = function (e, value) {

        nextend.fontManager.addVisualUsage(this.parameters.previewmode, value, window.nextend.pre);

        this.val(value);
    };

    NextendElementFont.prototype.val = function (value) {
        this.element.val(value);
        this.updateName(value);
        this.triggerOutsideChange();
    };

    NextendElementFont.prototype.insideChange = function (value) {
        this.element.val(value);

        this.updateName(value);

        this.triggerInsideChange();
    };

    NextendElementFont.prototype.updateName = function (value) {
        $.when(nextend.fontManager.getVisual(value))
            .done($.proxy(function (font) {
                this.name.val(font.name);
            }, this));
    };
    NextendElementFont.prototype.fontDeleted = function (e, id) {
        if (id == this.element.val()) {
            this.insideChange('');
        }
    };

    NextendElementFont.prototype.renderFont = function () {
        var font = this.element.val();
        nextend.fontManager.addVisualUsage(this.parameters.previewmode, font, '');
        return nextend.fontManager.getClass(font, this.parameters.previewmode);
    };

    scope.NextendElementFont = NextendElementFont;

})(n2, window);