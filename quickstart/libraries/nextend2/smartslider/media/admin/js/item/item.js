(function (smartSlider, $, scope, undefined) {

    function Item(item, layer, itemEditor, createPosition) {
        this.item = item;
        this.layer = layer;
        this.itemEditor = itemEditor;

        this.type = this.item.data('item');
        this.values = this.item.data('itemvalues');

        if (typeof this.values !== 'object') {
            this.values = $.parseJSON(this.values);
        }

        if (scope['NextendSmartSliderItemParser_' + this.type] !== undefined) {
            this.parser = new scope['NextendSmartSliderItemParser_' + this.type](this);
        } else {
            this.parser = new scope['NextendSmartSliderItemParser'](this);
        }
        this.item.data('item', this);

        if (typeof createPosition !== 'undefined') {
            if (this.layer.items.length == 0 || this.layer.items.length <= createPosition) {
                this.item.appendTo(this.layer.layer);
            } else {
                this.layer.items[createPosition].item.before(this.item);
            }
        }

        if (typeof createPosition === 'undefined' || this.layer.items.length == 0 || this.layer.items.length <= createPosition) {
            this.layer.items.push(this);
        } else {
            this.layer.items.splice(createPosition, 0, this);
        }

        if (this.item.children().length === 0) {
            this.reRender();
        }


        $('<div/>')
            .addClass('ui-helper ui-item-overlay')
            .css('zIndex', 89)
            .appendTo(this.item);

        $(window).trigger('ItemCreated');
    };

    Item.prototype.changeValue = function (property, value) {
        if (this == this.itemEditor.activeItem) {
            $('#item_' + this.type + property).data('field')
                .insideChange(value);
        } else {
            this.values[property] = value;
        }
    };

    Item.prototype.activate = function (e, force) {
        this.itemEditor.setActiveItem(this, force);
    };

    Item.prototype.deActivate = function () {
    };

    Item.prototype.render = function (html, data, originalData) {
        this.layer.layer.triggerHandler('itemRender');
        this.item.html(this.parser.render(html, data));

        // These will be available on the backend render
        this.values = originalData;

        $('<div/>')
            .addClass('ui-helper ui-item-overlay')
            .css('zIndex', 89)
            .appendTo(this.item);

        var layerName = this.parser.getName(data);
        if (layerName === false) {
            layerName = this.type;
        } else {
            layerName = layerName.replace(/[<> ]/gi, '');
        }
        this.layer.rename(layerName, false);

        this.layer.update();
    };

    Item.prototype.reRender = function (newData) {

        var data = {},
            itemEditor = this.itemEditor,
            form = itemEditor.getItemType(this.type),
            html = form.template;

        for (var name in this.values) {
            data[name] = this.values[name];
            //$.extend(data, this.parser.parse(name, data[name]));
        }

        data = $.extend({}, this.parser.getDefault(), data, newData);

        var originalData = $.extend({}, data);

        this.parser.parseAll(data, this);
        this.values = originalData;

        for (var k in data) {
            var reg = new RegExp('\\{' + k + '\\}', 'g');
            html = html.replace(reg, data[k]);
        }

        this.render($(html), data, this.values);
    };

    Item.prototype.duplicate = function () {
        this.layer.addItem(this.getHTML(), true);
    };

    Item.prototype.delete = function () {
        this.item.trigger('mouseleave');
        this.item.remove();

        if (this.itemEditor.activeItem == this) {
            this.itemEditor.activeItem = null;
        }

        delete this.itemEditor;
        delete this.layer;
    };

    Item.prototype.getHTML = function (base64) {
        var item = '';
        if (base64) {

            item = '[' + this.type + ' values="' + Base64.encode(JSON.stringify(this.values)) + '"]';
        } else {
            item = $('<div class="n2-ss-item n2-ss-item-' + this.type + '"></div>')
                .attr('data-item', this.type)
                .attr('data-itemvalues', JSON.stringify(this.values));
        }
        return item;
    };

    Item.prototype.getData = function () {
        return {
            type: this.type,
            values: this.values
        };
    };

    scope.NextendSmartSliderItem = Item;
})(nextend.smartSlider, n2, window);