(function ($, scope, undefined) {

    function ItemParserImage() {
        NextendSmartSliderItemParser.apply(this, arguments);
    };

    ItemParserImage.prototype = Object.create(NextendSmartSliderItemParser.prototype);
    ItemParserImage.prototype.constructor = ItemParserImage;

    ItemParserImage.prototype.getDefault = function () {
        return {
            size: '100%|*|auto',
            link: '#|*|_self',
            style: ''
        }
    };

    ItemParserImage.prototype.added = function () {
        this.needFill = ['image', 'url'];

        this.addedStyle('box', 'style');

        nextend.smartSlider.generator.registerField($('#item_imageimage'));
        nextend.smartSlider.generator.registerField($('#item_imagealt'));
        nextend.smartSlider.generator.registerField($('#item_imagetitle'));
        nextend.smartSlider.generator.registerField($('#linkitem_imagelink_0'));
    };

    ItemParserImage.prototype.getName = function (data) {
        return data.image.split('/').pop();
    };

    ItemParserImage.prototype.parseAll = function (data, item) {
        var size = data.size.split('|*|');
        data.width = size[0];
        data.height = size[1];
        delete data.size;

        var link = data.link.split('|*|');
        data.url = link[0];
        data.target = link[1];
        delete data.link;

        NextendSmartSliderItemParser.prototype.parseAll.apply(this, arguments);

        if (item && item.values.image == '$system$/images/placeholder/image.png' && data.image != item.values.image) {
            data.image = nextend.imageHelper.fixed(data.image);
            this.resizeLayerToImage(item, data.image);
        } else {
            data.image = nextend.imageHelper.fixed(data.image);
        }

    };

    ItemParserImage.prototype.fitLayer = function (item) {
        this.resizeLayerToImage(item, nextend.imageHelper.fixed(item.values.image));
        return true;
    };

    ItemParserImage.prototype.render = function (node, data) {
        if (data['url'] == '#') {
            node.html(node.children('a').html());
        }
        return node;
    };

    scope.NextendSmartSliderItemParser_image = ItemParserImage;
})(n2, window);