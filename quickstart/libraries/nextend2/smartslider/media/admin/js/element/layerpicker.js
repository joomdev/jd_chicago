(function ($, scope) {

    var STATUS = {
            INITIALIZED: 0,
            UNDER_PICK_PARENT: 1,
            UNDER_PICK_CHILD: 2
        },
        OVERLAYS = '<div class="n2-ss-picker-overlay" data-align="left" data-valign="top" />' +
            '<div class="n2-ss-picker-overlay" data-align="center" data-valign="top" style="left:33%;top:0;" />' +
            '<div class="n2-ss-picker-overlay" data-align="right" data-valign="top" style="left:66%;top:0;width:34%;" />' +
            '<div class="n2-ss-picker-overlay" data-align="left" data-valign="middle" style="left:0;top:33%;" />' +
            '<div class="n2-ss-picker-overlay" data-align="center" data-valign="middle" style="left:33%;top:33%; " />' +
            '<div class="n2-ss-picker-overlay" data-align="right" data-valign="middle" style="left:66%;top:33%;width:34%;" />' +
            '<div class="n2-ss-picker-overlay" data-align="left" data-valign="bottom" style="left:0;top:66%;height:34%;" />' +
            '<div class="n2-ss-picker-overlay" data-align="center" data-valign="bottom" style="left:33%;top:66%;height:34%;" />' +
            '<div class="n2-ss-picker-overlay" data-align="right" data-valign="bottom" style="left:66%;top:66%;width:34%;height:34%;" />';

    function NextendElementLayerPicker(id) {
        this.status = 0;
        this.element = $('#' + id);
        this.overlays = null;

        this.aligns = this.element.parent().parent().siblings();

        this.globalPicker = $('#n2-ss-parent-linker');
        this.picker = this.element.siblings('.n2-ss-layer-picker')
            .on({
                click: $.proxy(this.click, this),
                mouseenter: $.proxy(function () {
                    var value = this.element.val();
                    if (value != '') {
                        $('#' + value).addClass('n2-highlight');
                    }
                }, this),
                mouseleave: $.proxy(function () {
                    var value = this.element.val();
                    if (value != '') {
                        $('#' + value).removeClass('n2-highlight');
                    }
                }, this)
            });


        NextendElement.prototype.constructor.apply(this, arguments);
    };


    NextendElementLayerPicker.prototype = Object.create(NextendElement.prototype);
    NextendElementLayerPicker.prototype.constructor = NextendElementLayerPicker;

    NextendElementLayerPicker.prototype.click = function (e) {
        if (this.status == STATUS.INITIALIZED) {
            $('body').on('mousedown.n2-ss-parent-linker', $.proxy(function (e) {
                var el = $(e.target),
                    parent = el.parent();
                if (!el.hasClass('n2-ss-picker-overlay') && !parent.hasClass('n2-under-pick')) {
                    this.endSelection();
                }
            }, this));
            var layers = nextend.activeLayer.parent().find('.n2-ss-layer').not(nextend.activeLayer),
                cb = function (id) {
                    layers.each(function () {
                        var layer = $(this),
                            layerObject = layer.data('layerObject');
                        if (layerObject.getProperty(false, 'parentid') == id) {
                            layers = layers.not(layer);
                            var id2 = layerObject.getProperty(false, 'id');
                            if (id2 && id2 != '') {
                                cb(id2);
                            }
                        }
                    });
                };
            var cID = nextend.activeLayer.data('layerObject').getProperty(false, 'id');
            if (cID && cID != '') {
                cb(cID);
            }

            if (layers.length > 0) {
                this.globalPicker.addClass('n2-under-pick');
                this.picker.addClass('n2-under-pick');

                layers.addClass('n2-ss-picking-on-layer');
                this.overlays = $(OVERLAYS).appendTo(layers);
                this.overlays.on('mousedown', $.proxy(function (e) {
                    var selectedOverlay = $(e.currentTarget),
                        parentAlign = selectedOverlay.data('align'),
                        parentValign = selectedOverlay.data('valign'),
                        parentObject = selectedOverlay.parent().data('layerObject');
                    this.status = STATUS.UNDER_PICK_CHILD;
                    this.overlays.remove();

                    layers.removeClass('n2-ss-picking-on-layer');
                    nextend.activeLayer.addClass('n2-ss-picking-on-layer');
                    this.overlays = $(OVERLAYS).appendTo(nextend.activeLayer);
                    this.overlays.on('mousedown', $.proxy(function (e) {
                        var selectedChildOverlay = $(e.currentTarget),
                            align = selectedChildOverlay.data('align'),
                            valign = selectedChildOverlay.data('valign');

                        nextend.activeLayer.removeClass('n2-ss-picking-on-layer');
                        nextend.activeLayer.data('layerObject').parentPicked(parentObject, parentAlign, parentValign, align, valign);

                        //this.change(parentObject.requestID());

                        e.preventDefault();
                        e.stopPropagation();
                        this.endSelection();
                    }, this));
                    e.preventDefault();
                    e.stopPropagation();
                }, this));

                NextendEsc.add($.proxy(function () {
                    this.endSelection();
                    return false;
                }, this));

                this.status = STATUS.UNDER_PICK_PARENT;
            }
        } else if (this.status == STATUS.UNDER_PICK_PARENT) {
            this.change('');
            this.endSelection();
        } else if (this.status == STATUS.UNDER_PICK_CHILD) {
            this.change('');
            this.endSelection();
        }
    };

    NextendElementLayerPicker.prototype.endSelection = function () {
        $('body').off('mousedown.n2-ss-parent-linker');
        nextend.activeLayer.parent().find('.n2-ss-layer').removeClass('n2-ss-picking-on-layer');
        this.globalPicker.removeClass('n2-under-pick');
        this.picker.removeClass('n2-under-pick');
        if (this.overlays) {
            this.overlays.remove();
        }
        this.overlays = null;
        this.status = STATUS.INITIALIZED;
        NextendEsc.pop();
    };

    NextendElementLayerPicker.prototype.change = function (value) {
        this.picker.trigger('mouseleave');
        this.element.val(value).trigger('change');
        this._setValue(value);
        this.triggerOutsideChange();
    };

    NextendElementLayerPicker.prototype.insideChange = function (value) {
        this.element.val(value);
        this._setValue(value);

        this.triggerInsideChange();
    };

    NextendElementLayerPicker.prototype._setValue = function (value) {
        if (value && value != '') {
            this.picker.addClass('n2-active');
            this.aligns.css('display', '');
        } else {
            this.picker.removeClass('n2-active');
            this.aligns.css('display', 'none');
        }
    };

    scope.NextendElementLayerPicker = NextendElementLayerPicker;

})(n2, window);