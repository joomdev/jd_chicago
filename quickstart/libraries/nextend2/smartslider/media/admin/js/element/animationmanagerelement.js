;
(function ($, scope) {

    function NextendElementAnimationManager(id, managerIdentifier) {
        this.element = $('#' + id);
        this.managerIdentifier = managerIdentifier;

        this.element.parent()
            .on('click', $.proxy(this.show, this));

        this.element.siblings('.n2-form-element-clear')
            .on('click', $.proxy(this.clear, this));

        this.name = this.element.siblings('input');

        this.updateName(this.element.val());

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementAnimationManager.prototype = Object.create(NextendElement.prototype);
    NextendElementAnimationManager.prototype.constructor = NextendElementAnimationManager;


    NextendElementAnimationManager.prototype.show = function (e) {
        e.preventDefault();
        nextend[this.managerIdentifier].show(this.element.val(), $.proxy(this.save, this));
    };

    NextendElementAnimationManager.prototype.clear = function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.val('');
    };

    NextendElementAnimationManager.prototype.save = function (e, value) {
        this.val(value);
    };

    NextendElementAnimationManager.prototype.val = function (value) {
        this.element.val(value);
        this.updateName(value);
        this.triggerOutsideChange();
    };

    NextendElementAnimationManager.prototype.insideChange = function (value) {
        this.element.val(value);

        this.updateName(value);

        this.triggerInsideChange();
    };

    NextendElementAnimationManager.prototype.updateName = function (value) {
        if (value == '') {
            value = n2_('Disabled');
        } else if (value.split('||').length > 1) {
            value = n2_('Multiple animations')
        } else {
            value = n2_('Single animation');
        }
        this.name.val(value);
    };

    scope.NextendElementAnimationManager = NextendElementAnimationManager;

    function NextendElementPostAnimationManager() {
        NextendElementAnimationManager.prototype.constructor.apply(this, arguments);
    };


    NextendElementPostAnimationManager.prototype = Object.create(NextendElementAnimationManager.prototype);
    NextendElementPostAnimationManager.prototype.constructor = NextendElementPostAnimationManager;

    NextendElementPostAnimationManager.prototype.clear = function (e) {
        e.preventDefault();
        e.stopPropagation();
        var data = this.element.val().split('|*|');
        data[2] = '';
        this.val(data.join('|*|'));
    };
    NextendElementPostAnimationManager.prototype.updateName = function (value) {
        var data = value.split('|*|');
        value = data[2];
        if (value == '') {
            value = n2_('Disabled');
        } else if (value.split('||').length > 1) {
            value = n2_('Multiple animations');
        } else {
            value = n2_('Single animation');
        }
        this.name.val(value);
    };

    scope.NextendElementPostAnimationManager = NextendElementPostAnimationManager;

})(n2, window);