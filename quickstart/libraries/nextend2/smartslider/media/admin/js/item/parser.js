(function ($, scope, undefined) {

    function ItemParser(item) {
        this.pre = 'div#' + nextend.smartSlider.frontend.sliderElement.attr('id') + ' ';

        this.item = item;

        this.fonts = [];
        this.styles = [];

        this.needFill = [];
        this.added();
    }

    ItemParser.prototype.getDefault = function () {
        return {};
    };

    ItemParser.prototype.added = function () {

    };

    ItemParser.prototype.addedFont = function (mode, name) {
        this.fonts.push({
            mode: mode,
            name: name
        });
        $.when(nextend.fontManager.addVisualUsage(mode, this.item.values[name], this.pre))
            .done($.proxy(function (existsFont) {
                if (!existsFont) {
                    this.item.changeValue(name, '');
                }
            }, this));
    };

    ItemParser.prototype.addedStyle = function (mode, name) {
        this.styles.push({
            mode: mode,
            name: name
        });
        $.when(nextend.styleManager.addVisualUsage(mode, this.item.values[name], this.pre))
            .done($.proxy(function (existsStyle) {
                if (!existsStyle) {
                    this.item.changeValue(name, '');
                }
            }, this));

    };

    ItemParser.prototype.parseAll = function (data, item) {

        for (var i = 0; i < this.fonts.length; i++) {
            data[this.fonts[i].name + 'class'] = nextend.fontManager.getClass(data[this.fonts[i].name], this.fonts[i].mode) + ' ';
        }

        for (var i = 0; i < this.styles.length; i++) {
            data[this.styles[i].name + 'class'] = nextend.styleManager.getClass(data[this.styles[i].name], this.styles[i].mode) + ' ';
        }
        for (var i = 0; i < this.needFill.length; i++) {
            data[this.needFill[i]] = nextend.smartSlider.generator.fill(data[this.needFill[i]]);
        }
    };

    ItemParser.prototype.render = function (node, data) {
        return node;
    };

    ItemParser.prototype.getName = function (data) {
        return false;
    };

    ItemParser.prototype.resizeLayerToImage = function (item, image) {
        $("<img/>")
            .attr("src", image)
            .load(function () {
                var slideSize = item.layer.layerEditor.slideSize;
                var maxWidth = slideSize.width,
                    maxHeight = slideSize.height;

                if (this.width > 0 && this.height > 0) {
                    maxWidth = parseInt(Math.min(this.width, maxWidth));
                    maxHeight = parseInt(Math.min(this.height, maxHeight));
                    if (slideSize.width / slideSize.height <= maxWidth / maxHeight) {
                        item.layer.setProperty('width', maxWidth);
                        item.layer.setProperty('height', this.height * maxWidth / this.width);
                    } else {
                        var width = Math.min(this.width * slideSize.height / this.height, maxWidth);
                        item.layer.setProperty('width', width);
                        item.layer.setProperty('height', this.height * width / this.width);
                    }
                }
            });
    };

    ItemParser.prototype.fitLayer = function (item) {
        return false;
    };

    scope.NextendSmartSliderItemParser = ItemParser;

})(n2, window);