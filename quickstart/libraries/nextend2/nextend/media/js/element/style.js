;
(function ($, scope) {

    function NextendElementStyle(id, parameters) {
        this.element = $('#' + id);

        this.parameters = parameters;

        this.defaultSetId = parameters.set;

        this.element.parent()
            .on('click', $.proxy(this.show, this));

        this.element.siblings('.n2-form-element-clear')
            .on('click', $.proxy(this.clear, this));

        this.name = this.element.siblings('input');

        nextend.styleManager.$.on('visualDelete', $.proxy(this.styleDeleted, this));

        this.updateName(this.element.val());
        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementStyle.prototype = Object.create(NextendElement.prototype);
    NextendElementStyle.prototype.constructor = NextendElementStyle;


    NextendElementStyle.prototype.show = function (e) {
        e.preventDefault();
        if (this.parameters.font != '') {
            nextend.styleManager.setConnectedFont(this.parameters.font);
        }
        if (this.parameters.font2 != '') {
            nextend.styleManager.setConnectedFont2(this.parameters.font2);
        }
        if (this.parameters.style2 != '') {
            nextend.styleManager.setConnectedStyle(this.parameters.style2);
        }
        if (this.defaultSetId) {
            nextend.styleManager.changeSetById(this.defaultSetId);
        }
        nextend.styleManager.show(this.element.val(), $.proxy(this.save, this), {
            previewMode: this.parameters.previewmode,
            previewHTML: this.parameters.preview
        });
    };

    NextendElementStyle.prototype.clear = function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.val('');
    };

    NextendElementStyle.prototype.save = function (e, value) {

        nextend.styleManager.addVisualUsage(this.parameters.previewmode, value, window.nextend.pre);

        this.val(value);
    };

    NextendElementStyle.prototype.val = function (value) {
        this.element.val(value);
        this.updateName(value);
        this.triggerOutsideChange();
    };

    NextendElementStyle.prototype.insideChange = function (value) {
        this.element.val(value);

        this.updateName(value);

        this.triggerInsideChange();
    };

    NextendElementStyle.prototype.updateName = function (value) {
        $.when(nextend.styleManager.getVisual(value))
            .done($.proxy(function (style) {
                this.name.val(style.name);
            }, this));
    };
    NextendElementStyle.prototype.styleDeleted = function (e, id) {
        if (id == this.element.val()) {
            this.insideChange('');
        }
    };
    NextendElementStyle.prototype.renderStyle = function () {
        var style = this.element.val();
        nextend.styleManager.addVisualUsage(this.parameters.previewmode, style, '');
        return nextend.styleManager.getClass(style, this.parameters.previewmode);
    };

    scope.NextendElementStyle = NextendElementStyle;

})(n2, window);