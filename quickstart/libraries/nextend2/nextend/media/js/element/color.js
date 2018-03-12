;
(function ($, scope) {
    function NextendElementColor(id, alpha) {

        this.element = $('#' + id);

        if (alpha == 1) {
            this.alpha = true;
        } else {
            this.alpha = false;
        }

        this.element.spectrum({
            showAlpha: this.alpha,
            preferredFormat: (this.alpha == 1 ? "hex8" : "hex6"),
            showInput: false,
            showButtons: false,
            move: $.proxy(this, 'onMove'),
            showSelectionPalette: true,
            showPalette: true,
            maxSelectionSize: 6,
            localStorageKey: 'color',
            palette: [
                ['000000', '55aa39', '357cbd', 'bb4a28', '8757b2', '000000CC'],
                ['81898d', '5cba3c', '4594e1', 'd85935', '9e74c2', '00000080'],
                ['ced3d5', '27ae60', '01add3', 'e79d19', 'e264af', 'FFFFFFCC'],
                ['ffffff', '2ecc71', '00c1c4', 'ecc31f', 'ec87c0', 'FFFFFF80']
            ]
        })
            .on('change', $.proxy(this, 'onChange'));

        this.text = this.element.data('field');

        NextendElement.prototype.constructor.apply(this, arguments);
    };

    NextendElementColor.prototype = Object.create(NextendElement.prototype);
    NextendElementColor.prototype.constructor = NextendElementColor;

    NextendElementColor.prototype.onMove = function () {
        this.text.element.val(this.getCurrent());
        this.text.change();
    };

    NextendElementColor.prototype.onChange = function () {
        var current = this.getCurrent(),
            value = this.element.val();
        if (current != value) {
            this.element.spectrum("set", value);

            this.triggerInsideChange();
        }
    };

    NextendElementColor.prototype.insideChange = function (value) {
        this.element.val(value);

        this.onChange();
    };

    NextendElementColor.prototype.getCurrent = function () {
        if (this.alpha) {
            return this.element.spectrum("get").toHexString8();
        }
        return this.element.spectrum("get").toHexString(true);
    };

    scope.NextendElementColor = NextendElementColor;

})(n2, window);