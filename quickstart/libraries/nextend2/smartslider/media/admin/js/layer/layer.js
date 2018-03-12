(function (smartSlider, $, scope, undefined) {

    var highlighted = false,
        timeout = null;
    window.nextendPreventClick = false;

    var UNDEFINED,
        rAFShim = (function () {
            var timeLast = 0;

            return window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || function (callback) {
                    var timeCurrent = (new Date()).getTime(),
                        timeDelta;

                    /* Dynamically set delay on a per-tick basis to match 60fps. */
                    /* Technique by Erik Moller. MIT license: https://gist.github.com/paulirish/1579671 */
                    timeDelta = Math.max(0, 16 - (timeCurrent - timeLast));
                    timeLast = timeCurrent + timeDelta;

                    return setTimeout(function () {
                        callback(timeCurrent + timeDelta);
                    }, timeDelta);
                };
        })(),
        resizeCollection = {
            raf: false,
            ratios: null,
            isThrottled: false,
            layers: []
        },
        requestRender = function () {
            if (resizeCollection.raf === false) {
                resizeCollection.raf = true;
                rAFShim(function () {
                    for (var i = 0; i < resizeCollection.layers.length; i++) {
                        resizeCollection.layers[i].doTheResize(resizeCollection.ratios, true, resizeCollection.isThrottled);
                    }
                    resizeCollection = {
                        raf: false,
                        ratios: null,
                        isThrottled: false,
                        layers: []
                    };
                });
            }
        };

    function Layer(layerEditor, layer, itemEditor, properties) {
        //this.resize = NextendDeBounce(this.resize, 200);
        //this.triggerLayerResized = NextendThrottle(this.triggerLayerResized, 30);
        this._triggerLayerResizedThrottled = NextendThrottle(this._triggerLayerResized, 30);
        //this.doThrottledTheResize = NextendThrottle(this.doTheResize, 16.6666);
        this.markSmallLayer = NextendDeBounce(this.markSmallLayer, 500);
        this.doThrottledTheResize = this.doTheResize;
        this.eye = false;
        this.lock = false;
        this.parent = false;
        this.parentIsVisible = true;
        this.$ = $(this);

        this.layerEditor = layerEditor;

        if (!layer) {
            layer = $('<div class="n2-ss-layer" style="z-index: ' + layerEditor.zIndexList.length + ';"></div>')
                .appendTo(layerEditor.layerContainerElement);
            this.property = $.extend({
                id: null,
                parentid: null,
                parentalign: 'center',
                parentvalign: 'middle',
                name: 'New layer',
                nameSynced: 1,
                crop: 'visible',
                inneralign: 'left',
                parallax: 0,
                align: 'center',
                valign: 'middle',
                fontsize: 100,
                adaptivefont: 0,
                desktopPortrait: 1,
                desktopLandscape: 1,
                tabletPortrait: 1,
                tabletLandscape: 1,
                mobilePortrait: 1,
                mobileLandscape: 1,
                left: 0,
                top: 0,
                responsiveposition: 1,
                width: 'auto',
                height: 'auto',
                responsivesize: 1,
                mouseenter: UNDEFINED,
                click: UNDEFINED,
                mouseleave: UNDEFINED,
                play: UNDEFINED,
                pause: UNDEFINED,
                stop: UNDEFINED
            }, properties);
        } else {
            this.property = {
                id: layer.attr('id'),
                parentid: layer.data('parentid'),
                parentalign: layer.data('desktopportraitparentalign'),
                parentvalign: layer.data('desktopportraitparentvalign'),
                name: layer.data('name') + '',
                nameSynced: layer.data('namesynced'),
                crop: layer.data('crop'),
                inneralign: layer.data('inneralign'),
                parallax: layer.data('parallax'),
                align: layer.data('desktopportraitalign'),
                valign: layer.data('desktopportraitvalign'),
                fontsize: layer.data('desktopportraitfontsize'),
                adaptivefont: layer.data('adaptivefont'),
                desktopPortrait: parseFloat(layer.data('desktopportrait')),
                desktopLandscape: parseFloat(layer.data('desktoplandscape')),
                tabletPortrait: parseFloat(layer.data('tabletportrait')),
                tabletLandscape: parseFloat(layer.data('tabletlandscape')),
                mobilePortrait: parseFloat(layer.data('mobileportrait')),
                mobileLandscape: parseFloat(layer.data('mobilelandscape')),
                left: parseInt(layer.data('desktopportraitleft')),
                top: parseInt(layer.data('desktopportraittop')),
                responsiveposition: parseInt(layer.data('responsiveposition')),
                responsivesize: parseInt(layer.data('responsivesize')),
                mouseenter: layer.data('mouseenter'),
                click: layer.data('click'),
                mouseleave: layer.data('mouseleave'),
                play: layer.data('play'),
                pause: layer.data('pause'),
                stop: layer.data('stop')
            };

            var width = layer.data('desktopportraitwidth');
            if (this.isDimensionPropertyAccepted(width)) {
                this.property.width = width;
            } else {
                this.property.width = parseInt(width);
            }

            var height = layer.data('desktopportraitheight');
            if (this.isDimensionPropertyAccepted(height)) {
                this.property.height = height;
            } else {
                this.property.height = parseInt(height);
            }
        }

        if (!this.property.id) {
            this.property.id = null;
        }

        this.subscribeParentCallbacks = {};
        if (this.property.parentid) {
            this.subscribeParent();
        } else {
            this.property.parentid = null;
        }

        if (!this.property.parentalign) {
            this.property.parentalign = 'center';
        }

        if (!this.property.parentvalign) {
            this.property.parentvalign = 'middle';
        }

        if (typeof this.property.nameSynced === 'undefined') {
            this.property.nameSynced = 1;
        }

        if (typeof this.property.responsiveposition === 'undefined') {
            this.property.responsiveposition = 1;
        }

        if (typeof this.property.responsivesize === 'undefined') {
            this.property.responsivesize = 1;
        }

        if (!this.property.inneralign) {
            this.property.inneralign = 'left';
        }

        if (!this.property.crop) {
            this.property.crop = 'visible';
        }

        if (!this.property.parallax) {
            this.property.parallax = 0;
        }

        if (typeof this.property.fontsize == 'undefined') {
            this.property.fontsize = 100;
        }

        if (typeof this.property.adaptivefont == 'undefined') {
            this.property.adaptivefont = 0;
        }

        if (!this.property.align) {
            this.property.align = 'left';
        }

        if (!this.property.valign) {
            this.property.valign = 'top';
        }
        layer.attr('data-align', this.property.align);
        layer.attr('data-valign', this.property.valign);

        this.layer = layer.data('layerObject', this);
        this.layer.css('visibility', 'hidden');

        this.zIndex = parseInt(this.layer.css('zIndex'));
        if (isNaN(this.zIndex)) {
            this.zIndex = 0;
        }

        var eye = layer.data('eye'),
            lock = layer.data('lock');
        if (eye !== null && typeof eye != 'undefined') {
            this.eye = !!eye;
        }
        if (lock !== null && typeof lock != 'undefined') {
            this.lock = !!lock;
        }
        this.deviceProperty = {
            desktopPortrait: {
                left: this.property.left,
                top: this.property.top,
                width: this.property.width,
                height: this.property.height,
                align: this.property.align,
                valign: this.property.valign,
                parentalign: this.property.parentalign,
                parentvalign: this.property.parentvalign,
                fontsize: this.property.fontsize
            },
            desktopLandscape: {
                left: layer.data('desktoplandscapeleft'),
                top: layer.data('desktoplandscapetop'),
                width: layer.data('desktoplandscapewidth'),
                height: layer.data('desktoplandscapeheight'),
                align: layer.data('desktoplandscapealign'),
                valign: layer.data('desktoplandscapevalign'),
                parentalign: layer.data('desktoplandscapeparentalign'),
                parentvalign: layer.data('desktoplandscapeparentvalign'),
                fontsize: layer.data('desktoplandscapefontsize')
            },
            tabletPortrait: {
                left: layer.data('tabletportraitleft'),
                top: layer.data('tabletportraittop'),
                width: layer.data('tabletportraitwidth'),
                height: layer.data('tabletportraitheight'),
                align: layer.data('tabletportraitalign'),
                valign: layer.data('tabletportraitvalign'),
                parentalign: layer.data('tabletportraitparentalign'),
                parentvalign: layer.data('tabletportraitparentvalign'),
                fontsize: layer.data('tabletportraitfontsize')
            },
            tabletLandscape: {
                left: layer.data('tabletlandscapeleft'),
                top: layer.data('tabletlandscapetop'),
                width: layer.data('tabletlandscapewidth'),
                height: layer.data('tabletlandscapeheight'),
                align: layer.data('tabletlandscapealign'),
                valign: layer.data('tabletlandscapevalign'),
                parentalign: layer.data('tabletlandscapeparentalign'),
                parentvalign: layer.data('tabletlandscapeparentvalign'),
                fontsize: layer.data('tabletlandscapefontsize')
            },
            mobilePortrait: {
                left: layer.data('mobileportraitleft'),
                top: layer.data('mobileportraittop'),
                width: layer.data('mobileportraitwidth'),
                height: layer.data('mobileportraitheight'),
                align: layer.data('mobileportraitalign'),
                valign: layer.data('mobileportraitvalign'),
                parentalign: layer.data('mobileportraitparentalign'),
                parentvalign: layer.data('mobileportraitparentvalign'),
                fontsize: layer.data('mobileportraitfontsize')
            },
            mobileLandscape: {
                left: layer.data('mobilelandscapeleft'),
                top: layer.data('mobilelandscapetop'),
                width: layer.data('mobilelandscapewidth'),
                height: layer.data('mobilelandscapeheight'),
                align: layer.data('mobilelandscapealign'),
                valign: layer.data('mobilelandscapevalign'),
                parentalign: layer.data('mobilelandscapeparentalign'),
                parentvalign: layer.data('mobilelandscapeparentvalign'),
                fontsize: layer.data('mobilelandscapefontsize')
            }
        };


        this.layersItemsElement = layerEditor.layersItemsElement;
        this.layersItemsUlElement = this.layersItemsElement.find('> ul');

        this.createRow();

        this.itemEditor = itemEditor;

        this.initItems();

        this.___makeLayerAlign();
        this.___makeLayerResizeable();
        this.___makeLayerDraggable();
        this.___makeLayerQuickHandle();

        layerEditor.layerList.push(this);
        //this.index = layerEditor.layerList.push(this) - 1;

        /**
         * This is a fix for the editor load. The layers might not in the z-index order on the load,
         * so we have to "mess up" the array and let the algorithm to fix it.
         */
        if (typeof layerEditor.zIndexList[this.zIndex] === 'undefined') {
            layerEditor.zIndexList[this.zIndex] = this;
        } else {
            layerEditor.zIndexList.splice(this.zIndex, 0, this);
        }

        this._lock();

        this.animation = new NextendSmartSliderLayerAnimations(this);


        this.layerEditor.$.trigger('layerCreated', this);
        $(window).triggerHandler('layerCreated');

        this.animation.load();

        this.layer.on({
            mousedown: $.proxy(this.activate, this),
            dblclick: $.proxy(this.fit, this)
        });

        this.markSmallLayer();

        setTimeout($.proxy(function () {
            this._resize(true);
            this._eye();
        }, this), 300);
    };

    Layer.prototype.getIndex = function () {
        return this.layerEditor.layerList.indexOf(this);
    };

    Layer.prototype.getParent = function () {
        return $('#' + this.getProperty(false, 'parentid')).data('layerObject');
    };

    Layer.prototype.requestID = function () {
        var id = this.getProperty(false, 'id');
        if (!id) {
            id = $.fn.uid();
            this.setProperty('id', id, 'layer');
        }
        return id;
    };

    Layer.prototype.createRow = function () {
        var dblClickInterval = 300,
            timeout = null,
            unlink = $('<a class="n2-ss-parent-unlink" href="#" onclick="return false;"><i class="n2-i n2-i-layerunlink n2-i-grey-opacity"></i></a>').on('click', $.proxy(this.unlink, this)),
            remove = $('<a href="#" onclick="return false;"><i class="n2-i n2-i-delete n2-i-grey-opacity"></i></a>').on('click', $.proxy(this.delete, this)),
            duplicate = $('<a href="#" onclick="return false;"><i class="n2-i n2-i-duplicate n2-i-grey-opacity"></i></a>').on('click', $.proxy(this.duplicate, this, true, false));

        this.soloElement = $('<a href="#" onclick="return false;"><i class="n2-i n2-i-bulb n2-i-grey-opacity"></i></a>').css('opacity', 0.3).on('click', $.proxy(this.switchSolo, this));
        this.eyeElement = $('<a href="#" onclick="return false;"><i class="n2-i n2-i-eye n2-i-grey-opacity"></i></a>').on('click', $.proxy(this.switchEye, this));
        this.lockElement = $('<a href="#" onclick="return false;"><i class="n2-i n2-i-lock n2-i-grey-opacity"></i></a>').on('click', $.proxy(this.switchLock, this));

        this.layerRow = $('<li class="n2-ss-layer-row"></li>')
            .on({
                mouseenter: $.proxy(function () {
                    this.layer.addClass('n2-highlight');
                }, this),
                mouseleave: $.proxy(function (e) {
                    this.layer.removeClass('n2-highlight');
                }, this)
            })
            .appendTo(this.layersItemsUlElement);
        this.layerTitleSpan = $('<span class="n2-ucf">' + this.property.name + '</span>')
            .on({
                mouseup: $.proxy(function (e) {
                    if (timeout) {
                        clearTimeout(timeout);
                        timeout = null;
                        this.editName();
                    } else {
                        timeout = setTimeout($.proxy(function () {
                            this.activate();
                            timeout = null;
                        }, this), dblClickInterval);
                    }
                }, this)
            });

        this.layerTitle = $('<div class="n2-ss-layer-title"></div>')
            .append(this.layerTitleSpan)
            .append($('<div class="n2-actions"></div>').append(unlink).append(duplicate).append(remove))
            .append($('<div class="n2-actions-left"></div>').append(this.eyeElement).append(this.soloElement).append(this.lockElement))
            .appendTo(this.layerRow)
            .on({
                mouseup: $.proxy(function (e) {
                    if (e.target.tagName === 'DIV') {
                        this.activate();
                    }
                }, this)
            });

        this.editorVisibilityChange();
    };

    Layer.prototype.editorVisibilityChange = function () {
        switch (this.layersItemsUlElement.children().length) {
            case 0:
                $('body').removeClass('n2-has-layers');
                break;
            case 1:
                $('body').addClass('n2-has-layers');
                break;
        }
    };

    Layer.prototype.setZIndex = function (targetIndex) {
        this.zIndex = targetIndex;
        this.layer.css('zIndex', targetIndex);
        this.layersItemsUlElement.append(this.layerRow);
        this.$.trigger('layerIndexed', targetIndex);
    };

    /**
     *
     * @param item {optional}
     */
    Layer.prototype.activate = function (e) {
        if (document.activeElement) {
            document.activeElement.blur();
        }
        if (this.items.length == 0) {
            console.error('The layer do not have item on it!');
        } else {
            this.items[0].activate();
        }

        // Set the layer active if it is not active currently
        var currentIndex = this.getIndex();
        if (this.layerEditor.activeLayerIndex !== currentIndex) {
            this.layerRow.addClass('n2-active');
            this.layer.triggerHandler('n2-ss-activate');
            this.layerEditor.changeActiveLayer(currentIndex);
            nextend.activeLayer = this.layer;

            var scroll = this.layersItemsUlElement.parent(),
                scrollTop = scroll.scrollTop(),
                top = this.layerRow.get(0).offsetTop;
            if (top < scrollTop || top > scrollTop + scroll.height() - this.layerRow.height()) {
                scroll.scrollTop(top);
            }

            if (timeout) {
                highlighted.removeClass('n2-highlight2');
                clearTimeout(timeout);
                timeout = null;
            }
            highlighted = this.layer.addClass('n2-highlight2');
            timeout = setTimeout(function () {
                highlighted.removeClass('n2-highlight2');
                highlighted = null;
                timeout = null;
            }, 500);
        }
    };

    Layer.prototype.deActivate = function () {
        this.animation.deActivate();
        this.layerRow.removeClass('n2-active');
        this.layer.triggerHandler('n2-ss-deactivate');
    };

    Layer.prototype.fit = function () {
        var layer = this.layer.get(0);

        var slideSize = this.layerEditor.slideSize,
            position = this.layer.position();

        if (layer.scrollWidth > 0 && layer.scrollHeight > 0) {
            var resized = false;
            for (var i = 0; i < this.items.length; i++) {
                resized = this.items[i].parser.fitLayer(this.items[i]);
                if (resized) {
                    break;
                }
            }
            if (!resized) {
                this.setProperty('width', 'auto', 'layer');
                this.setProperty('height', 'auto', 'layer');

                var layerWidth = this.layer.width();
                if (Math.abs(this.layerEditor.layerContainerElement.width() - this.layer.position().left - layerWidth) < 2) {
                    this.setProperty('width', layerWidth, 'layer');
                }
            }
        }
    };

    Layer.prototype.switchToAnimation = function () {
        smartSlider.sidebarManager.switchTab(1);
    };

    Layer.prototype.hide = function (targetMode) {
        this.store(false, (targetMode ? targetMode : this.getMode()), 0, true);
    };

    Layer.prototype.show = function (targetMode) {
        this.store(false, (targetMode ? targetMode : this.getMode()), 1, true);
    };

    Layer.prototype.switchSolo = function () {
        this.layerEditor.setSolo(this);
    };

    Layer.prototype.markSolo = function () {
        this.soloElement.css('opacity', 1);
        this.layer.addClass('n2-ss-layer-solo');
    };

    Layer.prototype.unmarkSolo = function () {
        this.soloElement.css('opacity', 0.3);
        this.layer.removeClass('n2-ss-layer-solo');
    };

    Layer.prototype.switchEye = function () {
        this.eye = !this.eye;
        this._eye();
    };

    Layer.prototype._eye = function () {
        if (this.eye) {
            this.eyeElement.css('opacity', 0.3);
            this.layer.css('visibility', 'hidden');
        } else {
            this.eyeElement.css('opacity', 1);
            this.layer.css('visibility', '');
        }
    };

    Layer.prototype._hide = function () {
        this.layer.css('display', 'none');
    };

    Layer.prototype._show = function () {
        if (parseInt(this.property[this.layerEditor.getMode()])) {
            this.layer.css('display', 'block');
        }
    };

    Layer.prototype.switchLock = function () {
        this.lock = !this.lock;
        this._lock();
    };

    Layer.prototype._lock = function () {
        if (this.lock) {
            this.lockElement.css('opacity', 1);
            this.layer.nextenddraggable("disable");
            this.layer.nextendResizable("disable");
            this.layer.addClass('n2-ss-layer-locked');
        } else {
            this.lockElement.css('opacity', 0.3);
            this.layer.nextenddraggable("enable");
            this.layer.nextendResizable("enable");
            this.layer.removeClass('n2-ss-layer-locked');

        }
    };

    Layer.prototype.duplicate = function (needActivate, newParentId) {
        var layer = this.getHTML(true, false);

        var id = layer.attr('id');
        if (id) {
            id = $.fn.uid();
            layer.attr('id', id);
        }

        if (newParentId) {
            layer.attr('data-parentid', newParentId);
        }

        var newLayer = this.layerEditor.addLayer(layer, true);

        this.layer.triggerHandler('LayerDuplicated', id);

        this.layerRow.trigger('mouseleave');

        if (needActivate) {
            newLayer.activate();
        }
    };

    Layer.prototype.delete = function () {

        this.deActivate();

        for (var i = 0; i < this.items.length; i++) {
            this.items[i].delete();
        }

        this.layerEditor.zIndexList.splice(this.zIndex, 1);

        var parentId = this.getProperty(false, 'parentid');
        if (parentId) {
            this.unSubscribeParent(true);
        }
        // If delete happen meanwhile layer dragged or resized, we have to cancel that.
        this.layer.trigger('mouseup');
        this.layer.triggerHandler('LayerDeleted');
        this.layer.remove();
        this.layerRow.remove();
        this.layerEditor.layerDeleted(this.getIndex());

        this.editorVisibilityChange();

        this.$.trigger('layerDeleted');

        delete this.layerEditor;
        delete this.layer;
        delete this.itemEditor;
        delete this.animation;
        delete this.items;
    };

    Layer.prototype.getHTML = function (itemsIncluded, base64) {
        var layer = $('<div class="n2-ss-layer"></div>')
            .attr('style', this.getStyleText());

        for (var k in this.property) {
            if (k != 'width' && k != 'height' && k != 'left' && k != 'top') {
                layer.attr('data-' + k.toLowerCase(), this.property[k]);
            }
        }

        for (var k in this.deviceProperty) {
            for (var k2 in this.deviceProperty[k]) {
                layer.attr('data-' + k.toLowerCase() + k2, this.deviceProperty[k][k2]);
            }
        }

        layer.css({
            position: 'absolute',
            zIndex: this.zIndex + 1
        });

        for (var k in this.deviceProperty['desktop']) {
            layer.css(k, this.deviceProperty['desktop'][k] + 'px');
        }

        if (itemsIncluded) {
            for (var i = 0; i < this.items.length; i++) {
                layer.append(this.items[i].getHTML(base64));
            }
        }
        var id = this.getProperty(false, 'id');
        if (id && id != '') {
            layer.attr('id', id);
        }

        layer.attr('data-eye', this.eye);
        layer.attr('data-lock', this.lock);


        layer.attr('data-animations', this.animation.getAnimationsCode());

        return layer;
    };

    Layer.prototype.getData = function (itemsIncluded) {
        var layer = {
            zIndex: (this.zIndex + 1),
            eye: this.eye,
            lock: this.lock,
            animations: this.animation.getData()
        };
        for (var k in this.property) {
            switch (k) {
                case 'width':
                case 'height':
                case 'left':
                case 'top':
                case 'align':
                case 'valign':
                case 'parentalign':
                case 'parentvalign':
                case 'fontsize':
                    break;
                default:
                    layer[k.toLowerCase()] = this.property[k];
            }
        }

        // store the device based properties
        for (var device in this.deviceProperty) {
            for (var property in this.deviceProperty[device]) {
                var value = this.deviceProperty[device][property];
                if (typeof value === 'undefined') {
                    continue;
                }
                if (!(property == 'width' && this.isDimensionPropertyAccepted(value)) && !(property == 'height' && this.isDimensionPropertyAccepted(value)) && property != 'align' && property != 'valign' && property != 'parentalign' && property != 'parentvalign') {
                    value = parseFloat(value);
                }
                layer[device.toLowerCase() + property] = value;
            }
        }

        // Set the default styles for the layer
        /*var defaultProperties = this.deviceProperty['desktopPortrait'];
         layer.style += 'left:' + parseFloat(defaultProperties.left) + 'px;';
         layer.style += 'top:' + parseFloat(defaultProperties.top) + 'px;';
         if (this.isDimensionPropertyAccepted(defaultProperties.width)) {
         layer.style += 'width:' + defaultProperties.width + ';';
         } else {
         layer.style += 'width:' + parseFloat(defaultProperties.width) + 'px;';
         }
         if (this.isDimensionPropertyAccepted(defaultProperties.height)) {
         layer.style += 'height:' + defaultProperties.height + ';';
         } else {
         layer.style += 'height:' + parseFloat(defaultProperties.height) + 'px;';
         }*/

        if (itemsIncluded) {
            layer.items = [];
            for (var i = 0; i < this.items.length; i++) {
                layer.items.push(this.items[i].getData());
            }
        }
        return layer;
    };

    Layer.prototype.initItems = function () {
        this.items = [];
        var items = this.layer.find('.n2-ss-item');
        for (var i = 0; i < items.length; i++) {
            this.addItem(items.eq(i), false);
        }
    };

    Layer.prototype.addItem = function (item, place) {
        if (place) {
            item.appendTo(this.layer);
        }
        new NextendSmartSliderItem(item, this, this.itemEditor);
    };

    Layer.prototype.editName = function () {
        var input = new NextendSmartSliderAdminInlineField();

        input.$input.on({
            valueChanged: $.proxy(function (e, newName) {
                this.rename(newName, true);
                this.layerTitleSpan.css('display', 'inline');
            }, this),
            cancel: $.proxy(function () {
                this.layerTitleSpan.css('display', 'inline');
            }, this)
        });

        this.layerTitleSpan.css('display', 'none');
        input.injectNode(this.layerTitle, this.property.name);

    };

    Layer.prototype.rename = function (newName, force) {

        if (this.property.nameSynced || force) {

            if (force) {
                this.property.nameSynced = 0;
            }

            if (newName == '') {
                if (force) {
                    this.property.nameSynced = 1;
                    if (this.items.length) {
                        this.items[0].reRender();
                        return false;
                    }
                }
                newName = 'Layer #' + (this.layerEditor.layerList.length + 1);
            }
            newName = newName.substr(0, 35);
            if (this.property.name != newName) {
                this.property.name = newName;
                this.layerTitleSpan.html(newName);

                this.$.trigger('layerRenamed', newName);
            }
        }
    };

    Layer.prototype.markSmallLayer = function () {
        if (this.layer) {
            var w = this.layer.width(),
                h = this.layer.height();
            if (w < 50 || h < 50) {
                this.layer.addClass('n2-ss-layer-small');
            } else {
                this.layer.removeClass('n2-ss-layer-small');
            }
        }
    };

    // from: manager or other
    Layer.prototype.setProperty = function (name, value, from) {
        switch (name) {
            case 'responsiveposition':
            case 'responsivesize':
                value = parseInt(value);
            case 'id':
            case 'parentid':
            case 'inneralign':
            case 'crop':
            case 'parallax':
            case 'adaptivefont':
            case 'mouseenter':
            case 'click':
            case 'mouseleave':
            case 'play':
            case 'pause':
            case 'stop':
                this.store(false, name, value, true);
                break;
            case 'parentalign':
            case 'parentvalign':
            case 'align':
            case 'valign':
            case 'fontsize':
                this.store(true, name, value, true);
                break;
            case 'width':
                var ratioSizeH = this.layerEditor.getResponsiveRatio('h')
                if (!parseInt(this.getProperty(false, 'responsivesize'))) {
                    ratioSizeH = 1;
                }

                var v = value;
                if (!this.isDimensionPropertyAccepted(value)) {
                    v = ~~value;
                    if (v != value) {
                        this.$.trigger('propertyChanged', [name, v]);
                    }
                }
                this.storeWithModifier(name, v, ratioSizeH, true);
                this._resize(false);
                break;
            case 'height':
                var ratioSizeV = this.layerEditor.getResponsiveRatio('v')
                if (!parseInt(this.getProperty(false, 'responsivesize'))) {
                    ratioSizeV = 1;
                }

                var v = value;
                if (!this.isDimensionPropertyAccepted(value)) {
                    v = ~~value;
                    if (v != value) {
                        this.$.trigger('propertyChanged', [name, v]);
                    }
                }

                this.storeWithModifier(name, v, ratioSizeV, true);
                this._resize(false);
                break;
            case 'left':
                var ratioPositionH = this.layerEditor.getResponsiveRatio('h')
                if (!parseInt(this.getProperty(false, 'responsiveposition'))) {
                    ratioPositionH = 1;
                }

                var v = ~~value;
                if (v != value) {
                    this.$.trigger('propertyChanged', [name, v]);
                }

                this.storeWithModifier(name, v, ratioPositionH, true);
                break;
            case 'top':
                var ratioPositionV = this.layerEditor.getResponsiveRatio('v')
                if (!parseInt(this.getProperty(false, 'responsiveposition'))) {
                    ratioPositionV = 1;
                }

                var v = ~~value;
                if (v != value) {
                    this.$.trigger('propertyChanged', [name, v]);
                }

                this.storeWithModifier(name, v, ratioPositionV, true);
                break;
            case 'showFieldDesktopPortrait':
                this.store(false, 'desktopPortrait', parseInt(value), true);
                break;
            case 'showFieldDesktopLandscape':
                this.store(false, 'desktopLandscape', parseInt(value), true);
                break;
            case 'showFieldTabletPortrait':
                this.store(false, 'tabletPortrait', parseInt(value), true);
                break;
            case 'showFieldTabletLandscape':
                this.store(false, 'tabletLandscape', parseInt(value), true);
                break;
            case 'showFieldMobilePortrait':
                this.store(false, 'mobilePortrait', parseInt(value), true);
                break;
            case 'showFieldMobileLandscape':
                this.store(false, 'mobileLandscape', parseInt(value), true);
                break;
        }

        if (from != 'manager') {
            // jelezzuk a sidebarnak, hogy valamely property megvaltozott
            this.$.trigger('propertyChanged', [name, value]);
        }
    };

    Layer.prototype.getProperty = function (deviceBased, name) {

        if (deviceBased) {
            var properties = this.deviceProperty[this.getMode()],
                fallbackProperties = this.deviceProperty['desktopPortrait'];
            if (typeof properties[name] !== 'undefined') {
                return properties[name];
            } else if (typeof fallbackProperties[name] !== 'undefined') {
                return fallbackProperties[name];
            }
        }
        return this.property[name];
    };

    Layer.prototype.store = function (deviceBased, name, value, needRender) {
        this.property[name] = value;
        if (deviceBased) {
            var mode = this.getMode();
            this.deviceProperty[mode][name] = value;
        }

        if (needRender) {
            this.render(name, value);
        }

        if (name == 'width' || name == 'height') {
            this.markSmallLayer();
        }
        return;

        var lastLocalValue = this.property[name],
            lastValue = lastLocalValue;

        if (!isReset && this.property[name] != value) {
            this.property[name] = value;
            if (deviceBased) {
                lastValue = this.getProperty(deviceBased, name);
                this.deviceProperty[this.getMode()][name] = value;
            }
        } else if (deviceBased) {
            lastValue = this.getProperty(deviceBased, name);
            //this.property[name] = value;
        }
        /*if (lastLocalValue != value) {
         this.$.trigger('propertyChanged', [name, value]);
         }*/
        // The resize usually sets px for left/top/width/height values for the original percents. So we have to force those values back.
        if (needRender) {
            this.render(name, value);
        }

        if (name == 'width' || name == 'height') {
            this.markSmallLayer();
        }
    };

    Layer.prototype.storeWithModifier = function (name, value, modifier, needRender) {
        this.property[name] = value;
        var mode = this.getMode();
        this.deviceProperty[mode][name] = value;

        if (needRender) {
            this.renderWithModifier(name, value, modifier);
        }

        if (name == 'width' || name == 'height') {
            this.markSmallLayer();
        }
        return;


        var lastLocalValue = this.property[name];

        if (!isReset && this.property[name] != value) {
            this.property[name] = value;

            //this.$.trigger('propertyChanged', [name, value]);

            this.deviceProperty[this.getMode()][name] = value;
        }
        /*
         if (lastLocalValue != value) {
         this.$.trigger('propertyChanged', [name, value]);
         }
         */
        // The resize usually sets px for left/top/width/height values for the original percents. So we have to force those values back.
        if (needRender) {
            this.renderWithModifier(name, value, modifier);
        }

        this.markSmallLayer();
    };

    Layer.prototype.render = function (name, value) {
        this['_sync' + name](value);
    };

    Layer.prototype.renderWithModifier = function (name, value, modifier) {
        if ((name == 'width' || name == 'height') && this.isDimensionPropertyAccepted(value)) {
            this['_sync' + name](value);
        } else {
            this['_sync' + name](Math.round(value * modifier));
        }
    };

    Layer.prototype._syncid = function (value) {
        if (!value || value == '') {
            this.layer.removeAttr('id');
        } else {
            this.layer.attr('id', value);
        }
    };

    Layer.prototype.subscribeParent = function () {
        var that = this;
        this.subscribeParentCallbacks = {
            LayerResized: function () {
                that.resizeParent.apply(that, arguments);
            },
            LayerParent: function () {
                that.layer.addClass('n2-ss-layer-parent');
                that.layer.triggerHandler('LayerParent');
            },
            LayerUnParent: function () {
                that.layer.removeClass('n2-ss-layer-parent');
                that.layer.triggerHandler('LayerUnParent');
            },
            LayerDeleted: function () {
                that.setProperty('parentid', '', 'layer');
            },
            LayerDuplicated: function (e, newParentId) {
                that.duplicate(false, newParentId);
            },
            LayerShowChange: function (e, mode, value) {
                if (that.getMode() == mode) {
                    that.parentIsVisible = value;
                }
            },
            'n2-ss-activate': function () {
                that.layerRow.addClass('n2-parent-active');
            },
            'n2-ss-deactivate': function () {
                that.layerRow.removeClass('n2-parent-active');
            }
        };
        this.parent = n2('#' + this.property.parentid).on(this.subscribeParentCallbacks);
    };

    Layer.prototype.unSubscribeParent = function (isDelete) {
        this.layerRow.removeClass('n2-parent-active');
        if (this.parent) {
            this.parent.off(this.subscribeParentCallbacks);
        }
        this.parent = false;
        this.subscribeParentCallbacks = {};
        if (!isDelete) {
            var position = this.layer.position();
            this.setPosition(position.left, position.top);
        }
    };

    Layer.prototype.unlink = function (e) {
        e.preventDefault();
        this.setProperty('parentid', '', 'layer');
    };

    Layer.prototype.parentPicked = function (parentObject, parentAlign, parentValign, align, valign) {
        this.setProperty('parentid', '', 'layer');

        this.setProperty('align', align, 'layer');
        this.setProperty('valign', valign, 'layer');
        this.setProperty('parentalign', parentAlign, 'layer');
        this.setProperty('parentvalign', parentValign, 'layer');

        this.setProperty('parentid', parentObject.requestID(), 'layer');
    };

    Layer.prototype._syncparentid = function (value) {
        if (!value || value == '') {
            this.layer.removeAttr('data-parentid');
            this.unSubscribeParent(false);
        } else {
            if ($('#' + value).length == 0) {
                this.setProperty('parentid', '', 'layer');
            } else {
                this.layer.attr('data-parentid', value);
                this.subscribeParent();
                this.setPosition(this.layer.position().left, this.layer.position().top);
            }
        }
    };

    Layer.prototype._syncparentalign = function (value) {
        this.layer.data('parentalign', value);
        var parent = this.getParent();
        if (parent) {
            parent._resize(false);
        }
    };

    Layer.prototype._syncparentvalign = function (value) {
        this.layer.data('parentvalign', value);
        var parent = this.getParent();
        if (parent) {
            parent._resize(false);
        }
    };

    Layer.prototype._syncinneralign = function (value) {
        this.layer.css('text-align', value);
    };

    Layer.prototype._synccrop = function (value) {
        if (value == 'auto') {
            value = 'hidden';
        }

        var mask = this.layer.find('> .n2-ss-layer-mask');
        if (value == 'mask') {
            value = 'hidden';
            if (!mask.length) {
                mask = $("<div class='n2-ss-layer-mask'></div>").appendTo(this.layer);
                for (var i = 0; i < this.items.length; i++) {
                    mask.append(this.items[i].item);
                }
            }
        } else {
            if (mask.length) {
                for (var i = 0; i < this.items.length; i++) {
                    this.layer.append(this.items[i].item);
                    mask.remove();
                }
            }
        }
        this.layer.css('overflow', value);
    };

    Layer.prototype._syncparallax = function (value) {

    };

    Layer.prototype._syncalign = function (value, lastValue) {
        if (lastValue !== 'undefined' && value != lastValue) {
            this.setPosition(this.layer.position().left, this.layer.position().top);
        }
        this.layer.attr('data-align', value);
    };

    Layer.prototype._syncvalign = function (value, lastValue) {
        if (lastValue !== 'undefined' && value != lastValue) {
            this.setPosition(this.layer.position().left, this.layer.position().top);
        }
        this.layer.attr('data-valign', value);
    };

    Layer.prototype._syncfontsize = function (value) {
        this.adjustFontSize(this.getProperty(false, 'adaptivefont'), value, true);
    };

    Layer.prototype._syncadaptivefont = function (value) {
        this.adjustFontSize(value, this.getProperty(true, 'fontsize'), true);
    };

    Layer.prototype.adjustFontSize = function (isAdaptive, fontSize, shouldUpdatePosition) {
        fontSize = parseInt(fontSize);
        if (parseInt(isAdaptive)) {
            this.layer.css('font-size', (nextend.smartSlider.frontend.sliderElement.data('fontsize') * fontSize / 100) + 'px');
        } else if (fontSize != 100) {
            this.layer.css('font-size', fontSize + '%');
        } else {
            this.layer.css('font-size', '');
        }
        if (shouldUpdatePosition) {
            this.update();
        }
    };

    Layer.prototype._syncleft = function (value) {
        if (!this.parent || !this.parentIsVisible) {
            switch (this.getProperty(true, 'align')) {
                case 'right':
                    this.layer.css({
                        left: 'auto',
                        right: -value + 'px'
                    });
                    break;
                case 'center':
                    this.layer.css({
                        left: (this.layer.parent().width() / 2 + value - this.layer.width() / 2) + 'px',
                        right: 'auto'
                    });
                    break;
                default:
                    this.layer.css({
                        left: value + 'px',
                        right: 'auto'
                    });
            }
        } else {
            var position = this.parent.position(),
                align = this.getProperty(true, 'align'),
                parentAlign = this.getProperty(true, 'parentalign'),
                left = 0;
            switch (parentAlign) {
                case 'right':
                    left = position.left + this.parent.width();
                    break;
                case 'center':
                    left = position.left + this.parent.width() / 2;
                    break;
                default:
                    left = position.left;
            }

            switch (align) {
                case 'right':
                    this.layer.css({
                        left: 'auto',
                        right: (this.layer.parent().width() - left - value) + 'px'
                    });
                    break;
                case 'center':
                    this.layer.css({
                        left: (left + value - this.layer.width() / 2) + 'px',
                        right: 'auto'
                    });
                    break;
                default:
                    this.layer.css({
                        left: (left + value) + 'px',
                        right: 'auto'
                    });
            }

        }

        this.triggerLayerResized();
    };

    Layer.prototype._synctop = function (value) {
        if (!this.parent || !this.parentIsVisible) {
            switch (this.getProperty(true, 'valign')) {
                case 'bottom':
                    this.layer.css({
                        top: 'auto',
                        bottom: -value + 'px'
                    });
                    break;
                case 'middle':
                    this.layer.css({
                        top: (this.layer.parent().height() / 2 + value - this.layer.height() / 2) + 'px',
                        bottom: 'auto'
                    });
                    break;
                default:
                    this.layer.css({
                        top: value + 'px',
                        bottom: 'auto'
                    });
            }
        } else {
            var position = this.parent.position(),
                valign = this.getProperty(true, 'valign'),
                parentVAlign = this.getProperty(true, 'parentvalign'),
                top = 0;
            switch (parentVAlign) {
                case 'bottom':
                    top = position.top + this.parent.height();
                    break;
                case 'middle':
                    top = position.top + this.parent.height() / 2;
                    break;
                default:
                    top = position.top;
            }

            switch (valign) {
                case 'bottom':
                    this.layer.css({
                        top: 'auto',
                        bottom: (this.layer.parent().height() - top - value) + 'px'
                    });
                    break;
                case 'middle':
                    this.layer.css({
                        top: (top + value - this.layer.height() / 2) + 'px',
                        bottom: 'auto'
                    });
                    break;
                default:
                    this.layer.css({
                        top: (top + value) + 'px',
                        bottom: 'auto'
                    });
            }
        }

        this.triggerLayerResized();
    };

    Layer.prototype._syncresponsiveposition = function (value) {
        this._resize(false);
    };

    Layer.prototype._syncwidth = function (value) {
        this.layer.css('width', value + (this.isDimensionPropertyAccepted(value) ? '' : 'px'));
    };

    Layer.prototype._syncheight = function (value) {
        this.layer.css('height', value + (this.isDimensionPropertyAccepted(value) ? '' : 'px'));
    };

    Layer.prototype._syncresponsivesize = function (value) {
        this._resize(false);
    };

    Layer.prototype._syncdesktopPortrait = function (value) {
        this.__syncShowOnDevice('desktopPortrait', value);
    };

    Layer.prototype._syncdesktopLandscape = function (value) {
        this.__syncShowOnDevice('desktopLandscape', value);
    };

    Layer.prototype._synctabletPortrait = function (value) {
        this.__syncShowOnDevice('tabletPortrait', value);
    };

    Layer.prototype._synctabletLandscape = function (value) {
        this.__syncShowOnDevice('tabletLandscape', value);
    };

    Layer.prototype._syncmobilePortrait = function (value) {
        this.__syncShowOnDevice('mobilePortrait', value);
    };

    Layer.prototype._syncmobileLandscape = function (value) {
        this.__syncShowOnDevice('mobileLandscape', value);
    };

    Layer.prototype.__syncShowOnDevice = function (mode, value) {
        if (this.getMode() == mode) {
            var value = parseInt(value);
            if (value) {
                this._show();
            } else {
                this._hide();
            }
            this.layer.triggerHandler('LayerShowChange', [mode, value]);
            this.triggerLayerResized();
        }
    };

    Layer.prototype._syncmouseenter =
        Layer.prototype._syncclick =
            Layer.prototype._syncmouseleave =
                Layer.prototype._syncplay =
                    Layer.prototype._syncpause =
                        Layer.prototype._syncstop = function () {
                        };

    Layer.prototype.___makeLayerAlign = function () {
        this.alignMarker = $('<div class="n2-ss-layer-align-marker" />').appendTo(this.layer);
    };

    //<editor-fold desc="Makes layer resizable">

    /**
     * Add resize handles to the specified layer
     * @param {jQuery} layer
     * @private
     */
    Layer.prototype.___makeLayerResizeable = function () {
        this.layer.nextendResizable({
            handles: 'n, e, s, w, ne, se, sw, nw',
            _containment: this.layerEditor.layerContainerElement,
            start: $.proxy(this.____makeLayerResizeableStart, this),
            resize: $.proxy(this.____makeLayerResizeableResize, this),
            stop: $.proxy(this.____makeLayerResizeableStop, this),
            smartguides: $.proxy(function () {
                this.layer.triggerHandler('LayerParent');
                return this.layerEditor.getSnap();
            }, this),
            tolerance: 5
        })
            .on({
                mousedown: $.proxy(function (e) {
                    if (!this.lock) {
                        this.layerEditor.positionDisplay
                            .css({
                                left: e.pageX + 10,
                                top: e.pageY + 10
                            })
                            .html('W: ' + parseInt(this.layer.width()) + 'px<br />H: ' + parseInt(this.layer.height()) + 'px')
                            .addClass('n2-active');
                    }
                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                }, this),
                mouseup: $.proxy(function (e) {
                    this.layerEditor.positionDisplay.removeClass('n2-active');
                }, this)
            });
    };

    Layer.prototype.____makeLayerResizeableStart = function (event, ui) {
        $('#n2-admin').addClass('n2-ss-resize-layer');
        this.____makeLayerResizeableResize(event, ui);
        this.layerEditor.positionDisplay.addClass('n2-active');
    };

    Layer.prototype.____makeLayerResizeableResize = function (e, ui) {
        this.layerEditor.positionDisplay
            .css({
                left: e.pageX + 10,
                top: e.pageY + 10
            })
            .html('W: ' + ui.size.width + 'px<br />H: ' + ui.size.height + 'px');
        this.triggerLayerResized();
    };

    Layer.prototype.____makeLayerResizeableStop = function (event, ui) {
        window.nextendPreventClick = true;
        setTimeout(function () {
            window.nextendPreventClick = false;
        }, 50);
        $('#n2-admin').removeClass('n2-ss-resize-layer');

        var isAutoWidth = false;
        if (ui.originalSize.width == ui.size.width) {
            var currentValue = this.getProperty(true, 'width');
            if (this.isDimensionPropertyAccepted(currentValue)) {
                isAutoWidth = true;
                this['_syncwidth'](currentValue);
            }
        }

        var isAutoHeight = false;
        if (ui.originalSize.height == ui.size.height) {
            var currentValue = this.getProperty(true, 'height');
            if (this.isDimensionPropertyAccepted(currentValue)) {
                isAutoHeight = true;
                this['_syncheight'](currentValue);
            }
        }
        this.setPosition(ui.position.left, ui.position.top);


        var ratioSizeH = this.layerEditor.getResponsiveRatio('h'),
            ratioSizeV = this.layerEditor.getResponsiveRatio('v');

        if (!parseInt(this.getProperty(false, 'responsivesize'))) {
            ratioSizeH = ratioSizeV = 1;
        }

        if (!isAutoWidth) {
            var value = Math.round(ui.size.width * (1 / ratioSizeH));
            this.storeWithModifier('width', value, ratioSizeH, false);
            this.$.trigger('propertyChanged', ['width', value]);
        }
        if (!isAutoHeight) {
            var value = Math.round(ui.size.height * (1 / ratioSizeV));
            this.storeWithModifier('height', value, ratioSizeV, false);
            this.$.trigger('propertyChanged', ['height', value]);
        }
        this.triggerLayerResized();

        this.layer.triggerHandler('LayerUnParent');

        this.layerEditor.positionDisplay.removeClass('n2-active');
    };
    //</editor-fold>

    //<editor-fold desc="Makes layer draggable">

    /**
     * Add draggable handles to the specified layer
     * @param layer
     * @private
     */
    Layer.prototype.___makeLayerDraggable = function () {

        this.layer.nextenddraggable({
            _containment: this.layerEditor.layerContainerElement,
            start: $.proxy(this.____makeLayerDraggableStart, this),
            drag: $.proxy(this.____makeLayerDraggableDrag, this),
            stop: $.proxy(this.____makeLayerDraggableStop, this),
            smartguides: $.proxy(function () {
                this.layer.triggerHandler('LayerParent');
                return this.layerEditor.getSnap();
            }, this),
            tolerance: 5
        });
    };

    Layer.prototype.____makeLayerDraggableStart = function (event, ui) {
        $('#n2-admin').addClass('n2-ss-move-layer');
        this.____makeLayerDraggableDrag(event, ui);
        this.layerEditor.positionDisplay.addClass('n2-active');

        var currentValue = this.getProperty(true, 'width');
        if (this.isDimensionPropertyAccepted(currentValue)) {
            this.layer.width(this.layer.width() + 0.5); // Center positioned element can wrap the last word to a new line if this fix not added
        }

        var currentValue = this.getProperty(true, 'height');
        if (this.isDimensionPropertyAccepted(currentValue)) {
            this['_syncheight'](currentValue);
        }
    };

    Layer.prototype.____makeLayerDraggableDrag = function (e, ui) {
        this.layerEditor.positionDisplay
            .css({
                left: e.pageX + 10,
                top: e.pageY + 10
            })
            .html('L: ' + parseInt(ui.position.left | 0) + 'px<br />T: ' + parseInt(ui.position.top | 0) + 'px');
        this.triggerLayerResized();
    };

    Layer.prototype.____makeLayerDraggableStop = function (event, ui) {
        window.nextendPreventClick = true;
        setTimeout(function () {
            window.nextendPreventClick = false;
        }, 50);
        $('#n2-admin').removeClass('n2-ss-move-layer');

        this.setPosition(ui.position.left, ui.position.top);

        var currentValue = this.getProperty(true, 'width');
        if (this.isDimensionPropertyAccepted(currentValue)) {
            this['_syncwidth'](currentValue);
        }

        var currentValue = this.getProperty(true, 'height');
        if (this.isDimensionPropertyAccepted(currentValue)) {
            this['_syncheight'](currentValue);
        }

        this.triggerLayerResized();

        this.layer.triggerHandler('LayerUnParent');
        this.layerEditor.positionDisplay.removeClass('n2-active');
    };

    Layer.prototype.moveX = function (x) {
        this.setDeviceBasedAlign();
        this.setProperty('left', this.getProperty(true, 'left') + x, 'layer');
        this.triggerLayerResized();
    };

    Layer.prototype.moveY = function (y) {
        this.setDeviceBasedAlign();
        this.setProperty('top', this.getProperty(true, 'top') + y, 'layer');
        this.triggerLayerResized();
    };

    Layer.prototype.setPosition = function (left, top) {

        var ratioH = this.layerEditor.getResponsiveRatio('h'),
            ratioV = this.layerEditor.getResponsiveRatio('v');

        if (!parseInt(this.getProperty(false, 'responsiveposition'))) {
            ratioH = ratioV = 1;
        }

        this.setDeviceBasedAlign();

        var parent = this.parent,
            p = {
                left: 0,
                leftMultiplier: 1,
                top: 0,
                topMultiplier: 1
            };
        if (!parent || !parent.is(':visible')) {
            parent = this.layer.parent();


            switch (this.getProperty(true, 'align')) {
                case 'center':
                    p.left += parent.width() / 2;
                    break;
                case 'right':
                    p.left += parent.width();
                    break;
            }

            switch (this.getProperty(true, 'valign')) {
                case 'middle':
                    p.top += parent.height() / 2;
                    break;
                case 'bottom':
                    p.top += parent.height();
                    break;
            }
        } else {
            var position = parent.position();
            switch (this.getProperty(true, 'parentalign')) {
                case 'right':
                    p.left = position.left + parent.width();
                    break;
                case 'center':
                    p.left = position.left + parent.width() / 2;
                    break;
                default:
                    p.left = position.left;
            }
            switch (this.getProperty(true, 'parentvalign')) {
                case 'bottom':
                    p.top = position.top + parent.height();
                    break;
                case 'middle':
                    p.top = position.top + parent.height() / 2;
                    break;
                default:
                    p.top = position.top;
            }
        }


        var left, needRender = false;
        switch (this.getProperty(true, 'align')) {
            case 'left':
                left = -Math.round((p.left - left) * (1 / ratioH));
                break;
            case 'center':
                left = -Math.round((p.left - left - this.layer.width() / 2) * (1 / ratioH))
                break;
            case 'right':
                left = -Math.round((p.left - left - this.layer.width()) * (1 / ratioH));
                needRender = true;
                break;
        }
        this.storeWithModifier('left', left, ratioH, needRender);
        this.$.trigger('propertyChanged', ['left', left]);

        var top, needRender = false;
        switch (this.getProperty(true, 'valign')) {
            case 'top':
                top = -Math.round((p.top - top) * (1 / ratioV));
                break;
            case 'middle':
                top = -Math.round((p.top - top - this.layer.height() / 2) * (1 / ratioV));
                break;
            case 'bottom':
                top = -Math.round((p.top - top - this.layer.height()) * (1 / ratioV));
                needRender = true;
                break;
        }
        this.storeWithModifier('top', top, ratioV, needRender);
        this.$.trigger('propertyChanged', ['top', top]);
    }

    Layer.prototype.setDeviceBasedAlign = function () {
        var mode = this.getMode();
        if (typeof this.deviceProperty[mode]['align'] == 'undefined') {
            this.setProperty('align', this.getProperty(true, 'align'), 'layer');
        }
        if (typeof this.deviceProperty[mode]['valign'] == 'undefined') {
            this.setProperty('valign', this.getProperty(true, 'valign'), 'layer');
        }
    };
    //</editor-fold

    Layer.prototype.snap = function () {
        this.layer.nextendResizable("option", "smartguides", $.proxy(function () {
            this.layer.triggerHandler('LayerParent');
            return this.layerEditor.getSnap();
        }, this));
        this.layer.nextenddraggable("option", "smartguides", $.proxy(function () {
            this.layer.triggerHandler('LayerParent');
            return this.layerEditor.getSnap();
        }, this));
    };

    //<editor-fold desc="Makes a layer deletable">

    Layer.prototype.___makeLayerQuickHandle = function () {
        var quick = $('<div class="n2-ss-layer-quick-handle" style="z-index: 92;"><i class="n2-i n2-it n2-i-more"></i></div>')
            .on('mousedown', $.proxy(function (e) {
                e.stopPropagation();
                this.activate();
                var handleOffset = $(e.currentTarget).offset();

                var container = $('<div class="n2-ss-layer-quick-panel"></div>').css(handleOffset)
                    .on('click mouseleave', function () {
                        container.remove();
                    })
                    .appendTo('body');
                $('<div class="n2-ss-layer-quick-panel-option"><i class="n2-i n2-it n2-i-duplicate"></i></div>')
                    .on('click', $.proxy(this.duplicate, this, true, false))
                    .appendTo(container);
                $('<div class="n2-ss-layer-quick-panel-option n2-ss-layer-quick-panel-option-center"><i class="n2-i n2-it n2-i-more"></i></div>').appendTo(container);
                $('<div class="n2-ss-layer-quick-panel-option"><i class="n2-i n2-it n2-i-delete"></i></div>')
                    .on('click', $.proxy(this.delete, this))
                    .appendTo(container);
            }, this))
            .appendTo(this.layer);
    };
    //</editor-fold>

    Layer.prototype.changeEditorMode = function (mode) {
        var value = parseInt(this.property[mode]);
        if (value) {
            this._show();
        } else {
            this._hide();
        }

        this.layer.triggerHandler('LayerShowChange', [mode, value]);

        this._renderModeProperties(false);
    };

    Layer.prototype.resetMode = function (mode, currentMode) {
        if (mode != 'desktopPortrait') {
            var undefined;
            for (var k in this.property) {
                this.deviceProperty[mode][k] = undefined;
            }
            if (mode == currentMode) {
                this._renderModeProperties(true);
            }
        }
    };

    Layer.prototype._renderModeProperties = function (isReset) {

        for (var k in this.property) {
            this.property[k] = this.getProperty(true, k);
            this.$.trigger('propertyChanged', [k, this.property[k]]);
        }

        var fontSize = this.getProperty(true, 'fontsize');
        this.adjustFontSize(this.getProperty(false, 'adaptivefont'), fontSize, false);

        this.layer.attr('data-align', this.property.align);
        this.layer.attr('data-valign', this.property.valign);
        if (isReset) {
            this._resize(true);
        }

    };

    Layer.prototype.copyMode = function (from, to) {
        if (from != to) {
            this.deviceProperty[to] = $.extend({}, this.deviceProperty[to], this.deviceProperty[from]);
        }
    };

    Layer.prototype.getMode = function () {
        return this.layerEditor.getMode();
    };

    Layer.prototype._resize = function (isForced) {
        this.resize({
            slideW: this.layerEditor.getResponsiveRatio('h'),
            slideH: this.layerEditor.getResponsiveRatio('v')
        }, isForced);
    };

    Layer.prototype.doLinearResize = function (ratios) {
        this.doThrottledTheResize(ratios, true);
    };

    Layer.prototype.resize = function (ratios, isForced) {

        if (!this.parent || isForced) {
            //this.doThrottledTheResize(ratios, false);
            this.addToResizeCollection(this, ratios, false);
        }
    };

    Layer.prototype.doTheResize = function (ratios, isLinear, isThrottled) {
        var ratioPositionH = ratios.slideW,
            ratioSizeH = ratioPositionH,
            ratioPositionV = ratios.slideH,
            ratioSizeV = ratioPositionV;

        if (!parseInt(this.getProperty(false, 'responsivesize'))) {
            ratioSizeH = ratioSizeV = 1;
        }

        //var width = this.getProperty(true, 'width');
        //this.storeWithModifier('width', this.isDimensionPropertyAccepted(width) ? width : Math.round(width), ratioSizeH, true);
        //var height = this.getProperty(true, 'height');
        //this.storeWithModifier('height', this.isDimensionPropertyAccepted(height) ? height : Math.round(height), ratioSizeV, true);
        this.renderWithModifier('width', this.getProperty(true, 'width'), ratioSizeH);
        this.renderWithModifier('height', this.getProperty(true, 'height'), ratioSizeV);

        if (!parseInt(this.getProperty(false, 'responsiveposition'))) {
            ratioPositionH = ratioPositionV = 1;
        }
        //this.storeWithModifier('left', Math.round(this.getProperty(true, 'left')), ratioPositionH, true);
        //this.storeWithModifier('top', Math.round(this.getProperty(true, 'top')), ratioPositionV, true);
        this.renderWithModifier('left', this.getProperty(true, 'left'), ratioPositionH);
        this.renderWithModifier('top', this.getProperty(true, 'top'), ratioPositionV);
        if (!isLinear) {
            this.triggerLayerResized(isThrottled, ratios);
        }
    };

    Layer.prototype.resizeParent = function (e, ratios, isThrottled) {
        //this.doThrottledTheResize(ratios, false, isThrottled);
        this.addToResizeCollection(this, ratios, isThrottled);
    };

    Layer.prototype.addToResizeCollection = function (layer, ratios, isThrottled) {
        resizeCollection.ratios = ratios;
        resizeCollection.isThrottled = isThrottled;
        for (var i = 0; i < resizeCollection.layers.length; i++) {
            if (resizeCollection.layers[i] == this) {
                resizeCollection.layers.splice(i, 1);
                break;
            }
        }
        resizeCollection.layers.push(layer);

        requestRender();
        this.triggerLayerResized(isThrottled, ratios);
    };

    Layer.prototype.update = function () {
        var parent = this.parent;

        if (this.getProperty(true, 'align') == 'center') {
            var left = 0;
            if (parent) {
                left = parent.position().left + parent.width() / 2;
            } else {
                left = this.layer.parent().width() / 2;
            }
            var ratio = this.layerEditor.getResponsiveRatio('h');
            if (!parseInt(this.getProperty(false, 'responsiveposition'))) {
                ratio = 1;
            }
            this.layer.css('left', (left - this.layer.width() / 2 + this.getProperty(true, 'left') * ratio));
        }

        if (this.getProperty(true, 'valign') == 'middle') {
            var top = 0;
            if (parent) {
                top = parent.position().top + parent.height() / 2;
            } else {
                top = this.layer.parent().height() / 2;
            }
            var ratio = this.layerEditor.getResponsiveRatio('v');
            if (!parseInt(this.getProperty(false, 'responsiveposition'))) {
                ratio = 1;
            }
            this.layer.css('top', (top - this.layer.height() / 2 + this.getProperty(true, 'top') * ratio));
        }
        this.triggerLayerResized();
    };

    Layer.prototype.triggerLayerResized = function (isThrottled, ratios) {
        if (isThrottled) {
            this._triggerLayerResized(isThrottled, ratios);
        } else {
            this._triggerLayerResizedThrottled(true, ratios);
        }
    };

    Layer.prototype._triggerLayerResized = function (isThrottled, ratios) {

        this.layer.triggerHandler('LayerResized', [ratios || {
            slideW: this.layerEditor.getResponsiveRatio('h'),
            slideH: this.layerEditor.getResponsiveRatio('v')
        }, isThrottled || false]);
    };

    Layer.prototype.getStyleText = function () {
        var style = '';
        var crop = this.property.crop;
        if (crop == 'auto') {
            crop = 'hidden';
        }
        style += 'overflow:' + crop + ';';
        style += 'text-align:' + this.property.inneralign + ';';
        return style;
    };

    Layer.prototype.isDimensionPropertyAccepted = function (value) {
        if ((value + '').match(/[0-9]+%/) || value == 'auto') {
            return true;
        }
        return false;
    };

    scope.NextendSmartSliderLayer = Layer;


})(nextend.smartSlider, n2, window);