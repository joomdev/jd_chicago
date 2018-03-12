(function ($, scope, undefined) {

    function ItemParserButton() {
        NextendSmartSliderItemParser.apply(this, arguments);
    };

    ItemParserButton.prototype = Object.create(NextendSmartSliderItemParser.prototype);
    ItemParserButton.prototype.constructor = ItemParserButton;

    ItemParserButton.prototype.added = function () {
        this.needFill = ['content', 'url'];
        this.addedFont('link', 'font');
        this.addedStyle('button', 'style');

        nextend.smartSlider.generator.registerField($('#item_buttoncontent'));
        nextend.smartSlider.generator.registerField($('#linkitem_buttonlink_0'));
    };

    ItemParserButton.prototype.getName = function (data) {
        return data.content;
    };

    ItemParserButton.prototype.parseAll = function (data) {
        var link = data.link.split('|*|');
        data.url = link[0];
        data.target = link[1];
        delete data.link;

        if (data.fullwidth | 0) {
            data.display = 'block;';
        } else {
            data.display = 'inline-block;';
        }

        data.extrastyle = data.nowrap | 0 ? 'white-space: nowrap;' : '';

        NextendSmartSliderItemParser.prototype.parseAll.apply(this, arguments);
    };

    scope.NextendSmartSliderItemParser_button = ItemParserButton;
})(n2, window);