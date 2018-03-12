;
(function ($, scope) {

    function NextendElementSkin(id, preId, skins, fixedMode) {
        this.element = $('#' + id);

        this.preId = preId;

        this.skins = skins;

        this.list = this.element.data('field');

        this.fixedMode = fixedMode;

        this.firstOption = this.list.select.find('option').eq(0);

        this.originalText = this.firstOption.text();

        this.element.on('nextendChange', $.proxy(this.onSkinSelect, this));

        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementSkin.prototype = Object.create(NextendElement.prototype);
    NextendElementSkin.prototype.constructor = NextendElementSkin;


    NextendElementSkin.prototype.onSkinSelect = function () {
        var skin = this.element.val();
        if (skin != '0') {
            skin = this.skins[skin];
            for (var k in skin) {
                if (skin.hasOwnProperty(k)) {
                    var el = $('#' + this.preId + k);
                    if (el.length) {
                        var field = el.data('field');
                        field.insideChange(skin[k]);
                    }
                }
            }

            if (!this.fixedMode) {
                this.changeFirstOptionText(n2_('Done'));
                this.list.insideChange('0');
                setTimeout($.proxy(this.changeFirstOptionText, this, this.originalText), 3000);
            }

        }
    };

    NextendElementSkin.prototype.changeFirstOptionText = function (text) {
        this.firstOption.text(text);
    };

    NextendElementSkin.prototype.insideChange = function (value) {
        this.element.val(value);
        this.list.select.val(value);
    };

    scope.NextendElementSkin = NextendElementSkin;
})(n2, window);
