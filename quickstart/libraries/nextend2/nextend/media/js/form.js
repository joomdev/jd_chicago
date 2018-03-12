;
(function ($, scope) {
    var _registered = false;

    function registerBeforeUnload() {
        if (!_registered) {
            $(window).on('beforeunload', function (e) {
                if (nextend.askToSave) {
                    var data = {
                        changed: false
                    };
                    $(window).triggerHandler('n2-before-unload', data);

                    if (data.changed) {
                        var confirmationMessage = n2_('The changes you made will be lost if you navigate away from this page.');

                        (e || window.event).returnValue = confirmationMessage;
                        return confirmationMessage;
                    }
                }
            });
            _registered = true;
        }
    }

    function NextendForm(id, url, values) {
        this.form = $('#' + id)
            .on('saved', $.proxy(this.onSaved, this))
            .data('form', this);

        this.onSaved();

        this.url = url;

        this.values = values;

        // Special fix for Joomla 1.6, 1.7 & 2.5. Speedy save!
        if (typeof document.formvalidator !== "undefined") {
            document.formvalidator.isValid = function () {
                return true;
            };
        }

        $(window).on('n2-before-unload', $.proxy(this.onBeforeUnload, this));
        registerBeforeUnload();

        $('input, textarea').on('keyup', function (e) {
            if (e.which == 27) {
                e.target.blur();
                e.stopPropagation();
            }
        });
    };

    NextendForm.prototype.onBeforeUnload = function (e, data) {
        if (!data.changed && this.isChanged()) {
            data.changed = true;
        }
    };

    NextendForm.prototype.isChanged = function () {
        this.form.triggerHandler('checkChanged');
        if (this.serialized != this.form.serialize()) {
            return true;
        }
        return false;
    };


    NextendForm.prototype.onSaved = function () {
        this.serialized = this.form.serialize();
    };

    NextendForm.submit = function (query) {
        nextend.askToSave = false;
        setTimeout(function () {
            n2(query).submit();
        }, 300);
        return false;
    };

    scope.NextendForm = NextendForm;


})(n2, window);