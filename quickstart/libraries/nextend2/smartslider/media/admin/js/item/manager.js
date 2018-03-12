(function (smartSlider, $, scope, undefined) {

    function ItemManager(layerEditor) {
        this.suppressChange = false;

        this.layerEditor = layerEditor;

        this._initInstalledItems();

        this.form = {};
        this.activeForm = {
            form: $('<div></div>')
        };
    }

    ItemManager.prototype.setActiveItem = function (item, force) {
        if (item != this.activeItem || force) {
            var type = item.type,
                values = item.values;

            this.activeForm.form.css('display', 'none');

            this.activeForm = this.getItemType(type);

            if (this.activeItem) {
                this.activeItem.deActivate();
            }

            this.activeItem = item;

            this.suppressChange = true;

            for (var key in values) {
                var field = $('#item_' + type + key).data('field');
                if (field) {
                    field.insideChange(values[key]);
                }
            }

            this.suppressChange = false;

            this.activeForm.form.css('display', 'block');
        }
    };

    ItemManager.prototype._initInstalledItems = function () {

        $('#n2-ss-item-container .n2-ss-core-item')
            .on('click', $.proxy(function (e) {
                this.createLayerItem($(e.currentTarget).data('item'));
            }, this));
    };

    ItemManager.prototype.createLayerItem = function (type) {
        var itemData = this.getItemType(type),
            layer = this.layerEditor.createLayer($('.n2-ss-core-item-' + type).data('layerproperties'));

        var itemNode = $('<div></div>').data('item', type).data('itemvalues', $.extend(true, {}, itemData.values))
            .addClass('n2-ss-item n2-ss-item-' + type);

        var item = new scope.NextendSmartSliderItem(itemNode, layer, this, 0);
        layer.activate();

        smartSlider.sidebarManager.switchTab(0);

        return item;
    };

    /**
     * Initialize an item type and subscribe the field changes on that type.
     * We use event normalization to stop not necessary rendering.
     * @param type
     * @private
     */
    ItemManager.prototype.getItemType = function (type) {
        if (this.form[type] === undefined) {
            var form = $('#smartslider-slide-toolbox-item-type-' + type),
                formData = {
                    form: form,
                    template: form.data('itemtemplate'),
                    values: form.data('itemvalues'),
                    fields: form.find('[name^="item_' + type + '"]'),
                    fieldNameRegexp: new RegExp('item_' + type + "\\[(.*?)\\]", "")
                };
            formData.fields.on({
                nextendChange: $.proxy(this.updateCurrentItem, this),
                keydown: $.proxy(this.updateCurrentItemDeBounced, this)
            });

            this.form[type] = formData;
        }
        return this.form[type];
    };

    /**
     * This function renders the current item with the current values of the related form field.
     */
    ItemManager.prototype.updateCurrentItem = function (e) {
        if (!this.suppressChange) {
            var data = {},
                originalData = {},
                form = this.form[this.activeItem.type],
                html = form.template,
                parser = this.activeItem.parser;

            // Get the current values of the fields
            // Run through the related item filter
            // Replace the variables in the template of the item type
            form.fields.each($.proxy(function (i, field) {
                var field = $(field),
                    name = field.attr('name').match(form.fieldNameRegexp)[1];

                originalData[name] = data[name] = field.val();

            }, this));

            data = $.extend({}, parser.getDefault(), data);

            parser.parseAll(data, this.activeItem);
            for (var k in data) {
                var reg = new RegExp('\\{' + k + '\\}', 'g');
                html = html.replace(reg, data[k]);
            }

            this.activeItem.render($(html), data, originalData);
        }
    };

    ItemManager.prototype.updateCurrentItemDeBounced = NextendDeBounce(function (e) {
        this.updateCurrentItem(e);
    }, 100);

    scope.NextendSmartSliderItemManager = ItemManager;

})(nextend.smartSlider, n2, window);