(function (smartSlider, $, scope, undefined) {
    var layerClass = '.n2-ss-layer',
        keys = {
            16: 0,
            38: 0,
            40: 0,
            37: 0,
            39: 0
        },
        nameToIndex = {
            left: 0,
            center: 1,
            right: 2,
            top: 0,
            middle: 1,
            bottom: 2
        },
        horizontalAlign = {
            97: 'left',
            98: 'center',
            99: 'right',
            100: 'left',
            101: 'center',
            102: 'right',
            103: 'left',
            104: 'center',
            105: 'right'
        },
        verticalAlign = {
            97: 'bottom',
            98: 'bottom',
            99: 'bottom',
            100: 'middle',
            101: 'middle',
            102: 'middle',
            103: 'top',
            104: 'top',
            105: 'top'
        };

    function AdminSlideLayerManager(layerManager, staticSlide, isUploadDisabled, uploadUrl, uploadDir) {
        this.activeLayerIndex = -1;
        this.snapToEnabled = true;
        this.staticSlide = staticSlide;

        this.layerDefault = {
            align: null,
            valign: null
        };

        this.solo = false;

        this.$ = $(this);
        smartSlider.layerManager = this;

        this.responsive = smartSlider.frontend.responsive;

        new NextendSmartSliderSidebar();

        this.layerList = [];

        this.layersItemsElement = $('#n2-ss-layers-items-list');

        this.frontendSlideLayers = layerManager;

        this.frontendSlideLayers.setZero();


        this.layerContainerElement = smartSlider.$currentSlideElement.find('.n2-ss-layers-container');
        if (!this.layerContainerElement.length) {
            this.layerContainerElement = smartSlider.$currentSlideElement;
        }

        this.layerContainerElement.parent().prepend('<div class="n2-ss-slide-border n2-ss-slide-border-left" /><div class="n2-ss-slide-border n2-ss-slide-border-top" /><div class="n2-ss-slide-border n2-ss-slide-border-right" /><div class="n2-ss-slide-border n2-ss-slide-border-bottom" />');


        this.slideSize = {
            width: this.layerContainerElement.width(),
            height: this.layerContainerElement.height()
        };

        smartSlider.frontend.sliderElement.on('SliderResize', $.proxy(this.refreshSlideSize, this));

        this.initToolbox();

        new NextendSmartSliderLayerAnimationManager(this);

        this.refreshLayers();

        smartSlider.itemEditor = this.itemEditor = new NextendSmartSliderItemManager(this);

        this.positionDisplay = $('<div class="n2 n2-ss-position-display"/>')
            .appendTo('body');

        this.zIndexList = [];

        this.layers.each($.proxy(function (i, layer) {
            new NextendSmartSliderLayer(this, $(layer), this.itemEditor);
        }, this));

        this.reIndexLayers();

        this._makeLayersOrderable();

        $('#smartslider-slide-toolbox-layer').on('mouseenter', function () {
            $('#n2-admin').addClass('smartslider-layer-highlight-active');
        }).on('mouseleave', function () {
            $('#n2-admin').removeClass('smartslider-layer-highlight-active');
        });

        this._initDeviceModeChange();

        //this.initBatch();
        this.initSnapTo();
        this.initEditorTheme();
        this.initAlign();
        this.initParentLinker();
        this.initEvents();

        var globalAdaptiveFont = $('#n2-ss-adaptive-font').on('click', $.proxy(function () {
            this.toolboxForm.adaptivefont.data('field').onoff.trigger('click');
        }, this));

        this.toolboxForm.adaptivefont.on('nextendChange', $.proxy(function () {
            if (this.toolboxForm.adaptivefont.val() == 1) {
                globalAdaptiveFont.addClass('n2-active');
            } else {
                globalAdaptiveFont.removeClass('n2-active');
            }
        }, this));


        new NextendElementNumber("n2-ss-font-size", -Number.MAX_VALUE, Number.MAX_VALUE);
        new NextendElementAutocompleteSimple("n2-ss-font-size", ["60", "80", "100", "120", "140", "160", "180"]);

        var globalFontSize = $('#n2-ss-font-size').on('outsideChange', $.proxy(function () {
            var value = parseInt(globalFontSize.val());
            this.toolboxForm.fontsize.val(value).trigger('change');
        }, this));

        this.toolboxForm.fontsize.on('nextendChange', $.proxy(function () {
            globalFontSize.data('field').insideChange(this.toolboxForm.fontsize.val());
        }, this));

        if (this.zIndexList.length > 0) {
            this.zIndexList[this.zIndexList.length - 1].activate();
        }


        $(window).on({
            keydown: $.proxy(function (e) {
                if (e.target.tagName != 'TEXTAREA' && e.target.tagName != 'INPUT' && (!smartSlider.timelineControl || !smartSlider.timelineControl.isActivated())) {
                    if (this.activeLayerIndex != -1) {
                        if (e.keyCode == 46) {
                            this.layerList[this.activeLayerIndex].delete();
                        } else if (e.keyCode == 35) {
                            this.layerList[this.activeLayerIndex].duplicate(true, false);
                            e.preventDefault();
                        } else if (e.keyCode == 16) {
                            keys[e.keyCode] = 1;
                        } else if (e.keyCode == 38) {
                            if (!keys[e.keyCode]) {
                                var fn = $.proxy(function () {
                                    this.layerList[this.activeLayerIndex].moveY(-1 * (keys[16] ? 10 : 1))
                                }, this);
                                fn();
                                keys[e.keyCode] = setInterval(fn, 100);
                            }
                            e.preventDefault();
                        } else if (e.keyCode == 40) {
                            if (!keys[e.keyCode]) {
                                var fn = $.proxy(function () {
                                    this.layerList[this.activeLayerIndex].moveY((keys[16] ? 10 : 1))
                                }, this);
                                fn();
                                keys[e.keyCode] = setInterval(fn, 100);
                            }
                            e.preventDefault();
                        } else if (e.keyCode == 37) {
                            if (!keys[e.keyCode]) {
                                var fn = $.proxy(function () {
                                    this.layerList[this.activeLayerIndex].moveX(-1 * (keys[16] ? 10 : 1))
                                }, this);
                                fn();
                                keys[e.keyCode] = setInterval(fn, 100);
                            }
                            e.preventDefault();
                        } else if (e.keyCode == 39) {
                            if (!keys[e.keyCode]) {
                                var fn = $.proxy(function () {
                                    this.layerList[this.activeLayerIndex].moveX((keys[16] ? 10 : 1))
                                }, this);
                                fn();
                                keys[e.keyCode] = setInterval(fn, 100);
                            }
                            e.preventDefault();
                        } else if (e.keyCode >= 97 && e.keyCode <= 105) {

                            var hAlign = horizontalAlign[e.keyCode],
                                vAlign = verticalAlign[e.keyCode],
                                toZero = false;
                            if (this.toolboxForm.align.val() == hAlign && this.toolboxForm.valign.val() == vAlign) {
                                toZero = true;
                            }
                            // numeric pad
                            this.horizontalAlign(hAlign, toZero);
                            this.verticalAlign(vAlign, toZero);

                        } else if (e.keyCode == 34) {
                            e.preventDefault();
                            var targetIndex = this.layerList[this.activeLayerIndex].zIndex - 1;
                            if (targetIndex < 0) {
                                targetIndex = this.zIndexList.length - 1;
                            }
                            this.zIndexList[targetIndex].activate();

                        } else if (e.keyCode == 33) {
                            e.preventDefault();
                            var targetIndex = this.layerList[this.activeLayerIndex].zIndex + 1;
                            if (targetIndex > this.zIndexList.length - 1) {
                                targetIndex = 0;
                            }
                            this.zIndexList[targetIndex].activate();

                        }
                    }
                }
            }, this),
            keyup: $.proxy(function (e) {
                if (typeof keys[e.keyCode] !== 'undefined' && keys[e.keyCode]) {
                    clearInterval(keys[e.keyCode]);
                    keys[e.keyCode] = 0;
                }
            }, this)
        });

        if (!isUploadDisabled) {
            smartSlider.frontend.sliderElement.fileupload({
                url: uploadUrl,
                pasteZone: false,
                dropZone: smartSlider.frontend.sliderElement,
                dataType: 'json',
                paramName: 'image',
                add: $.proxy(function (e, data) {
                    data.formData = {path: '/' + uploadDir};
                    data.submit();
                }, this),
                done: $.proxy(function (e, data) {
                    var response = data.result;
                    if (response.data && response.data.name) {
                        var item = this.itemEditor.createLayerItem('image');
                        item.reRender({
                            image: response.data.url
                        });
                        item.activate(null, true);
                    } else {
                        NextendAjaxHelper.notification(response);
                    }

                }, this),
                fail: $.proxy(function (e, data) {
                    NextendAjaxHelper.notification(data.jqXHR.responseJSON);
                }, this),

                start: function () {
                    NextendAjaxHelper.startLoading();
                },

                stop: function () {
                    setTimeout(function () {
                        NextendAjaxHelper.stopLoading();
                    }, 100);
                }
            });
        }
    };

    AdminSlideLayerManager.prototype.getMode = function () {
        return this.mode;
    };

    AdminSlideLayerManager.prototype._getMode = function () {
        return this.responsive.getNormalizedModeString();
    };

    AdminSlideLayerManager.prototype.getResponsiveRatio = function (axis) {
        if (axis == 'h') {
            return this.responsive.lastRatios.slideW;
        } else if (axis == 'v') {
            return this.responsive.lastRatios.slideH;
        }
        return 0;
    };

    AdminSlideLayerManager.prototype.createLayer = function (properties) {
        for (var k in this.layerDefault) {
            if (this.layerDefault[k] !== null) {
                properties[k] = this.layerDefault[k];
            }
        }
        var newLayer = new NextendSmartSliderLayer(this, false, this.itemEditor, properties);

        this.reIndexLayers();

        this._makeLayersOrderable();

        return newLayer;
    };

    AdminSlideLayerManager.prototype.addLayer = function (html, refresh) {
        var newLayer = $(html);
        this.layerContainerElement.append(newLayer);
        var layerObj = new NextendSmartSliderLayer(this, newLayer, this.itemEditor);

        if (refresh) {
            this.reIndexLayers();
            this.refreshMode();
        }
        return layerObj;
    };

    AdminSlideLayerManager.prototype.setSolo = function (layer) {
        if (this.solo) {
            this.solo.unmarkSolo();
            if (this.solo === layer) {
                this.solo = false;
                smartSlider.$currentSlideElement.removeClass('n2-ss-layer-solo-mode');
                return;
            } else {
                this.solo = false;
            }
        }

        this.solo = layer;
        layer.markSolo();
        smartSlider.$currentSlideElement.addClass('n2-ss-layer-solo-mode');
    };

    /**
     * Force the view to change to the second mode (layer)
     */
    AdminSlideLayerManager.prototype.switchToLayerTab = function () {
        smartSlider.slide._changeView(1);
    };

    //<editor-fold desc="Initialize the device mode changer">


    AdminSlideLayerManager.prototype._initDeviceModeChange = function () {
        var resetButton = $('#layerresettodesktop').on('click', $.proxy(this.__onResetToDesktopClick, this));
        this.resetToDesktopTRElement = resetButton.closest('tr');
        this.resetToDesktopGlobalElement = $('#n2-ss-reset-to-desktop').on('click', $.proxy(function () {
            if (this.resetToDesktopTRElement.css('display') == 'table-row') {
                resetButton.trigger('click');
            }
        }, this));


        var globalShowOnDevice = $('#n2-ss-show-on-device').on('click', $.proxy(function () {
            this.toolboxForm['showField' + this.mode.charAt(0).toUpperCase() + this.mode.substr(1)].data('field').onoff.trigger('click');
        }, this));

        this.globalShowOnDeviceCB = function (mode) {
            if (this.mode == mode) {
                if (this.toolboxForm['showField' + this.mode.charAt(0).toUpperCase() + this.mode.substr(1)].val() == 1) {
                    globalShowOnDevice.addClass('n2-active');
                } else {
                    globalShowOnDevice.removeClass('n2-active');
                }
            }
        };

        this.toolboxForm.showFieldDesktopPortrait.on('nextendChange', $.proxy(this.globalShowOnDeviceCB, this, 'desktopPortrait'));
        this.toolboxForm.showFieldDesktopLandscape.on('nextendChange', $.proxy(this.globalShowOnDeviceCB, this, 'desktopLandscape'));

        this.toolboxForm.showFieldTabletPortrait.on('nextendChange', $.proxy(this.globalShowOnDeviceCB, this, 'tabletPortrait'));
        this.toolboxForm.showFieldTabletLandscape.on('nextendChange', $.proxy(this.globalShowOnDeviceCB, this, 'tabletLandscape'));

        this.toolboxForm.showFieldMobilePortrait.on('nextendChange', $.proxy(this.globalShowOnDeviceCB, this, 'mobilePortrait'));
        this.toolboxForm.showFieldMobileLandscape.on('nextendChange', $.proxy(this.globalShowOnDeviceCB, this, 'mobileLandscape'));

        this.__onChangeDeviceOrientation();
        smartSlider.frontend.sliderElement.on('SliderDeviceOrientation', $.proxy(this.__onChangeDeviceOrientation, this));


        //this.__onResize();
        smartSlider.frontend.sliderElement.on('SliderResize', $.proxy(this.__onResize, this));
    };

    /**
     * Refresh the current responsive mode. Example: you are in tablet view and unpublish a layer for tablet, then you should need a refresh on the mode.
     */
    AdminSlideLayerManager.prototype.refreshMode = function () {

        this.__onChangeDeviceOrientation();

        smartSlider.frontend.responsive.reTriggerSliderDeviceOrientation();
    };

    /**
     * When the device mode changed we have to change the slider
     * @param mode
     * @private
     */
    AdminSlideLayerManager.prototype.__onChangeDeviceOrientation = function () {

        this.mode = this._getMode();
        this.globalShowOnDeviceCB(this.mode);

        this.resetToDesktopTRElement.css('display', (this.mode == 'desktopPortrait' ? 'none' : 'table-row'));
        this.resetToDesktopGlobalElement.css('display', (this.mode == 'desktopPortrait' ? 'none' : ''));
        for (var i = 0; i < this.layerList.length; i++) {
            this.layerList[i].changeEditorMode(this.mode);
        }
    };

    AdminSlideLayerManager.prototype.__onResize = function (e, ratios) {

        var sortedLayerList = this.getSortedLayers();

        for (var i = 0; i < sortedLayerList.length; i++) {
            sortedLayerList[i].doLinearResize(ratios);
        }
    };

    /**
     * Reset the custom values of the current mode on the current layer to the desktop values.
     * @private
     */
    AdminSlideLayerManager.prototype.__onResetToDesktopClick = function () {
        if (this.activeLayerIndex != -1) {
            var mode = this.getMode();
            this.layerList[this.activeLayerIndex].resetMode(mode, mode);
        }
    };

    AdminSlideLayerManager.prototype.copyOrResetMode = function (mode) {

        var currentMode = this.getMode();
        if (mode != 'desktopPortrait' && mode == currentMode) {
            for (var i = 0; i < this.layerList.length; i++) {
                this.layerList[i].resetMode(mode, currentMode);
            }
        } else if (mode != 'desktopPortrait' && currentMode == 'desktopPortrait') {
            for (var i = 0; i < this.layerList.length; i++) {
                this.layerList[i].resetMode(mode, currentMode);
            }
        } else if (mode != currentMode) {
            for (var i = 0; i < this.layerList.length; i++) {
                this.layerList[i].copyMode(currentMode, mode);
            }
        }

    };

    AdminSlideLayerManager.prototype.refreshSlideSize = function () {
        this.slideSize.width = smartSlider.frontend.dimensions.slide.width;
        this.slideSize.height = smartSlider.frontend.dimensions.slide.height;
    };

//</editor-fold>

    AdminSlideLayerManager.prototype._makeLayersOrderable = function () {
        this.layersOrderableElement = this.layersItemsElement.find(' > ul');
        this.layersOrderableElement
            .sortable({
                axis: "y",
                helper: 'clone',
                placeholder: "sortable-placeholder",
                forcePlaceholderSize: true,
                tolerance: "pointer",
                items: '.n2-ss-layer-row',
                //handle: '.n2-i-order',
                start: function (event, ui) {
                    $(ui.item).data("startindex", ui.item.index());
                },
                stop: $.proxy(function (event, ui) {
                    var startIndex = this.zIndexList.length - $(ui.item).data("startindex") - 1,
                        newIndex = this.zIndexList.length - $(ui.item).index() - 1;
                    this.zIndexList.splice(newIndex, 0, this.zIndexList.splice(startIndex, 1)[0]);
                    this.reIndexLayers();
                }, this)
            });
    };

    AdminSlideLayerManager.prototype.reIndexLayers = function () {
        this.zIndexList = this.zIndexList.filter(function (item) {
            return item != undefined
        });

        for (var i = this.zIndexList.length - 1; i >= 0; i--) {
            this.zIndexList[i].setZIndex(i);
        }
    };

    AdminSlideLayerManager.prototype.initEvents = function () {
        var parent = $('#n2-tab-events'),
            content = parent.find('> table').css('display', 'none'),
            heading = parent.find('.n2-h3'),
            headingLabel = heading.html(),
            row = $('<div class="n2-sidebar-row n2-sidebar-header-bg n2-form-dark n2-sets-header"><div class="n2-table"><div class="n2-tr"><div class="n2-td"><div class="n2-h3 n2-uc">' + headingLabel + '</div></div><div style="text-align: ' + (nextend.isRTL() ? 'left' : 'right') + ';" class="n2-td"></div></div></div></div>'),
            button = $('<a href="#" class="n2-button n2-button-medium n2-button-green n2-h5 n2-uc">' + n2_('Show') + '</a>').on('click', function (e) {
                e.preventDefault();
                if (button.hasClass('n2-button-green')) {
                    content.css('display', '');
                    button.html(n2_('Hide'));
                    button.addClass('n2-button-grey');
                    button.removeClass('n2-button-green');
                    $.jStorage.set("n2-ss-events", 1);
                } else {
                    content.css('display', 'none');
                    button.html(n2_('Show'));
                    button.addClass('n2-button-green');
                    button.removeClass('n2-button-grey');
                    $.jStorage.set("n2-ss-events", 0);
                }
            });
        if ($.jStorage.get("n2-ss-events", 0)) {
            content.css('display', '');
            button.html(n2_('Hide'));
            button.addClass('n2-button-grey');
            button.removeClass('n2-button-green');
        }
        heading.replaceWith(row);
        button.appendTo(row.find('.n2-td').eq(1));
    }

    AdminSlideLayerManager.prototype.initSnapTo = function () {

        var field = new NextendElementOnoff("n2-ss-snap");

        if (!$.jStorage.get("n2-ss-snap-to-enabled", 1)) {
            field.insideChange(0);
            this.snapToDisable();
        }

        field.element.on('outsideChange', $.proxy(this.switchSnapTo, this));
    };

    AdminSlideLayerManager.prototype.switchSnapTo = function (e) {
        e.preventDefault();
        if (this.snapToEnabled) {
            this.snapToDisable();
        } else {
            this.snapToEnable();
        }
    };

    AdminSlideLayerManager.prototype.snapToDisable = function () {
        this.snapToEnabled = false;
        this.snapToChanged(0);
    };

    AdminSlideLayerManager.prototype.snapToEnable = function () {
        this.snapToEnabled = true;
        this.snapToChanged(1);
    };
    AdminSlideLayerManager.prototype.snapToChanged = function () {
        for (var i = 0; i < this.layerList.length; i++) {
            this.layerList[i].snap();
        }
        $.jStorage.set("n2-ss-snap-to-enabled", this.snapToEnabled);
    };

    AdminSlideLayerManager.prototype.getSnap = function () {
        if (!this.snapToEnabled) {
            return false;
        }

        if (this.staticSlide) {
            return $('.n2-ss-static-slide .n2-ss-layer:not(.n2-ss-layer-locked):not(.n2-ss-layer-parent):visible');
        }
        return $('.n2-ss-slide.n2-ss-slide-active .n2-ss-layer:not(.n2-ss-layer-locked):not(.n2-ss-layer-parent):visible');
    };

    AdminSlideLayerManager.prototype.initEditorTheme = function () {
        this.themeElement = $('#n2-tab-smartslider-editor');
        this.themeButton = $('#n2-ss-theme').on('click', $.proxy(this.switchEditorTheme, this));
        if ($.jStorage.get("n2-ss-theme-dark", 0)) {
            this.themeButton.addClass('n2-active');
            this.themeElement.addClass('n2-ss-theme-dark');
        }
    };

    AdminSlideLayerManager.prototype.switchEditorTheme = function () {
        $.jStorage.set("n2-ss-theme-dark", !this.themeButton.hasClass('n2-active'));
        this.themeButton.toggleClass('n2-active');
        this.themeElement.toggleClass('n2-ss-theme-dark');
    };

    AdminSlideLayerManager.prototype.initAlign = function () {
        var hAlignButton = $('#n2-ss-horizontal-align .n2-radio-option'),
            vAlignButton = $('#n2-ss-vertical-align .n2-radio-option');

        hAlignButton.add(vAlignButton).on('click', $.proxy(function (e) {
            if (e.ctrlKey || e.metaKey) {
                var $el = $(e.currentTarget),
                    isActive = $el.hasClass('n2-sub-active'),
                    align = $el.data('align');
                switch (align) {
                    case 'left':
                    case 'center':
                    case 'right':
                        hAlignButton.removeClass('n2-sub-active');
                        if (isActive) {
                            $.jStorage.set('ss-item-horizontal-align', null);
                            this.layerDefault.align = null;
                        } else {
                            $.jStorage.set('ss-item-horizontal-align', align);
                            this.layerDefault.align = align;
                            $el.addClass('n2-sub-active');
                        }
                        break;
                    case 'top':
                    case 'middle':
                    case 'bottom':
                        vAlignButton.removeClass('n2-sub-active');
                        if (isActive) {
                            $.jStorage.set('ss-item-vertical-align', null);
                            this.layerDefault.valign = null;
                        } else {
                            $.jStorage.set('ss-item-vertical-align', align);
                            this.layerDefault.valign = align;
                            $el.addClass('n2-sub-active');
                        }
                        break;
                }
            } else if (this.activeLayerIndex != -1) {
                var align = $(e.currentTarget).data('align');
                switch (align) {
                    case 'left':
                    case 'center':
                    case 'right':
                        this.horizontalAlign(align, true);
                        break;
                    case 'top':
                    case 'middle':
                    case 'bottom':
                        this.verticalAlign(align, true);
                        break;
                }
            }
        }, this));

        this.toolboxForm.align.on('nextendChange', $.proxy(function () {
            hAlignButton.removeClass('n2-active');
            switch (this.toolboxForm.align.val()) {
                case 'left':
                    hAlignButton.eq(0).addClass('n2-active');
                    break;
                case 'center':
                    hAlignButton.eq(1).addClass('n2-active');
                    break;
                case 'right':
                    hAlignButton.eq(2).addClass('n2-active');
                    break;
            }
        }, this));
        this.toolboxForm.valign.on('nextendChange', $.proxy(function () {
            vAlignButton.removeClass('n2-active');
            switch (this.toolboxForm.valign.val()) {
                case 'top':
                    vAlignButton.eq(0).addClass('n2-active');
                    break;
                case 'middle':
                    vAlignButton.eq(1).addClass('n2-active');
                    break;
                case 'bottom':
                    vAlignButton.eq(2).addClass('n2-active');
                    break;
            }
        }, this));


        var hAlign = $.jStorage.get('ss-item-horizontal-align', null),
            vAlign = $.jStorage.get('ss-item-vertical-align', null);
        if (hAlign != null) {
            hAlignButton.eq(nameToIndex[hAlign]).addClass('n2-sub-active');
            this.layerDefault.align = hAlign;
        }
        if (vAlign != null) {
            vAlignButton.eq(nameToIndex[vAlign]).addClass('n2-sub-active');
            this.layerDefault.valign = vAlign;
        }
    };

    AdminSlideLayerManager.prototype.horizontalAlign = function (align, toZero) {
        if (this.toolboxForm.align.val() != align) {
            this.toolboxForm.align.data('field').options.eq(nameToIndex[align]).trigger('click');
        } else if (toZero) {
            this.toolboxForm.left.val(0).trigger('change');
        }
    };

    AdminSlideLayerManager.prototype.verticalAlign = function (align, toZero) {
        if (this.toolboxForm.valign.val() != align) {
            this.toolboxForm.valign.data('field').options.eq(nameToIndex[align]).trigger('click');
        } else if (toZero) {
            this.toolboxForm.top.val(0).trigger('change');
        }
    };

    AdminSlideLayerManager.prototype.initParentLinker = function () {
        var field = this.toolboxForm.parentid.data('field'),
            parentLinker = $('#n2-ss-parent-linker').on({
                click: function (e) {
                    field.click(e);
                },
                mouseenter: function (e) {
                    field.picker.trigger(e);
                },
                mouseleave: function (e) {
                    field.picker.trigger(e);
                }
            });
    };

    /**
     * Delete all layers on the slide
     */
    AdminSlideLayerManager.prototype.deleteLayers = function () {
        for (var i = this.layerList.length - 1; i >= 0; i--) {
            this.layerList[i].delete();
        }
    };

    AdminSlideLayerManager.prototype.layerDeleted = function (index) {

        this.reIndexLayers();

        var activeLayer = this.getSelectedLayer();

        this.layerList.splice(index, 1);

        if (index === this.activeLayerIndex) {
            this.activeLayerIndex = -1;
            if (this.zIndexList.length > 0) {
                this.zIndexList[this.zIndexList.length - 1].activate();
            } else {
                this.changeActiveLayer(-1);
            }
        } else if (activeLayer) {
            this.activeLayerIndex = activeLayer.getIndex();
        }
    };

    AdminSlideLayerManager.prototype.getSortedLayers = function () {
        var list = this.layerList.slice(),
            children = {};
        for (var i = list.length - 1; i >= 0; i--) {
            if (typeof list[i].property.parentid !== 'undefined' && list[i].property.parentid) {
                if (typeof children[list[i].property.parentid] == 'undefined') {
                    children[list[i].property.parentid] = [];
                }
                children[list[i].property.parentid].push(list[i]);
                list.splice(i, 1);
            }
        }
        for (var i = 0; i < list.length; i++) {
            if (typeof list[i].property.id !== 'undefined' && list[i].property.id && typeof children[list[i].property.id] !== 'undefined') {
                children[list[i].property.id].unshift(0);
                children[list[i].property.id].unshift(i + 1);
                list.splice.apply(list, children[list[i].property.id]);
                delete children[list[i].property.id];
            }
        }
        return list;
    };

    /**
     * Get the HTML code of the whole slide
     * @returns {string} HTML
     */
    AdminSlideLayerManager.prototype.getHTML = function () {
        var node = $('<div></div>');

        var list = this.layerList;
        for (var i = 0; i < list.length; i++) {
            node.append(list[i].getHTML(true, true));
        }

        return node.html();
    };


    AdminSlideLayerManager.prototype.getData = function () {
        var layers = [];

        var list = this.layerList;
        for (var i = 0; i < list.length; i++) {
            layers.push(list[i].getData(true));
        }

        return layers;
    };

    AdminSlideLayerManager.prototype.loadData = function (data, overwrite) {
        var layers = $.extend(true, [], data);
        if (overwrite) {
            this.deleteLayers();
        }
        var zIndexOffset = this.zIndexList.length;
        var idTranslation = {};
        for (var i = 0; i < layers.length; i++) {

            var layerData = layers[i],
                layer = $('<div class="n2-ss-layer"></div>')
                    .attr('style', layerData.style);

            var storedZIndex = layer.css('zIndex');
            if (storedZIndex == 'auto') {
                if (layerData.zIndex) {
                    storedZIndex = layerData.zIndex;
                } else {
                    storedZIndex = 1;
                }
            }
            layer.css('zIndex', storedZIndex + zIndexOffset);
            if (layerData.id) {
                var id = $.fn.uid();
                idTranslation[layerData.id] = id;
                layer.attr('id', id);
            }
            if (layerData.parentid) {
                if (typeof idTranslation[layerData.parentid] != 'undefined') {
                    layerData.parentid = idTranslation[layerData.parentid];
                } else {
                    layerData.parentid = '';
                }
            }

            for (var j = 0; j < layerData.items.length; j++) {
                $('<div class="n2-ss-item n2-ss-item-' + layerData.items[j].type + '"></div>')
                    .data('item', layerData.items[j].type)
                    .data('itemvalues', layerData.items[j].values)
                    .appendTo(layer);
            }

            delete layerData.style;
            delete layerData.items;
            layerData.animations = Base64.encode(JSON.stringify(layerData.animations));
            for (var k in layerData) {
                layer.data(k, layerData[k]);
            }
            this.addLayer(layer, false);
        }
        this.reIndexLayers();
        this.refreshMode();

        if (this.activeLayerIndex == -1 && this.layerList.length > 0) {
            this.layerList[0].activate();
        }
    };

    /**
     * Reloads the layers by the class name
     */
    AdminSlideLayerManager.prototype.refreshLayers = function () {
        this.layers = this.layerContainerElement.find(layerClass);
    };

//<editor-fold desc="Toolbox fields and related stuffs">

    /**
     * Initialize the sidebar Layer toolbox
     */
    AdminSlideLayerManager.prototype.initToolbox = function () {

        this.toolboxElement = $('#smartslider-slide-toolbox-layer');

        this.toolboxForm = {
            id: $('#layerid'),
            parentid: $('#layerparentid'),
            parentalign: $('#layerparentalign'),
            parentvalign: $('#layerparentvalign'),
            left: $('#layerleft'),
            top: $('#layertop'),
            responsiveposition: $('#layerresponsive-position'),
            width: $('#layerwidth'),
            height: $('#layerheight'),
            responsivesize: $('#layerresponsive-size'),
            showFieldDesktopPortrait: $('#layershow-desktop-portrait'),
            showFieldDesktopLandscape: $('#layershow-desktop-landscape'),
            showFieldTabletPortrait: $('#layershow-tablet-portrait'),
            showFieldTabletLandscape: $('#layershow-tablet-landscape'),
            showFieldMobilePortrait: $('#layershow-mobile-portrait'),
            showFieldMobileLandscape: $('#layershow-mobile-landscape'),
            crop: $('#layercrop'),
            inneralign: $('#layerinneralign'),
            parallax: $('#layerparallax'),
            align: $('#layeralign'),
            valign: $('#layervalign'),
            fontsize: $('#layerfont-size'),
            adaptivefont: $('#layeradaptive-font'),
            mouseenter: $('#layeronmouseenter'),
            click: $('#layeronclick'),
            mouseleave: $('#layeronmouseleave'),
            play: $('#layeronplay'),
            pause: $('#layeronpause'),
            stop: $('#layeronstop')
        };

        for (var k in this.toolboxForm) {
            this.toolboxForm[k].on('outsideChange', $.proxy(this.activateLayerPropertyChanged, this, k));
        }

        if (!this.responsive.isEnabled('desktop', 'Landscape')) {
            this.toolboxForm.showFieldDesktopLandscape.closest('.n2-mixed-group').css('display', 'none');
        }
        if (!this.responsive.isEnabled('tablet', 'Portrait')) {
            this.toolboxForm.showFieldTabletPortrait.closest('.n2-mixed-group').css('display', 'none');
        }
        if (!this.responsive.isEnabled('tablet', 'Landscape')) {
            this.toolboxForm.showFieldTabletLandscape.closest('.n2-mixed-group').css('display', 'none');
        }
        if (!this.responsive.isEnabled('mobile', 'Portrait')) {
            this.toolboxForm.showFieldMobilePortrait.closest('.n2-mixed-group').css('display', 'none');
        }
        if (!this.responsive.isEnabled('mobile', 'Landscape')) {
            this.toolboxForm.showFieldMobileLandscape.closest('.n2-mixed-group').css('display', 'none');
        }
    };

    AdminSlideLayerManager.prototype.activateLayerPropertyChanged = function (name, e) {
        if (this.activeLayerIndex != -1) {
            //@todo  batch? throttle
            var value = this.toolboxForm[name].val();
            this.layerList[this.activeLayerIndex].setProperty(name, value, 'manager');
        } else {
            var field = this.toolboxForm[name].data('field');
            if (typeof field !== 'undefined') {
                field.insideChange('');
            }
        }
    };

    /**
     * getter for the currently selected layer
     * @returns {jQuery|boolean} layer element in jQuery representation or false
     * @private
     */
    AdminSlideLayerManager.prototype.getSelectedLayer = function () {
        if (this.activeLayerIndex == -1) {
            return false;
        }
        return this.layerList[this.activeLayerIndex];
    };

//</editor-fold>

    AdminSlideLayerManager.prototype.changeActiveLayer = function (index) {
        var lastActive = this.activeLayerIndex;
        if (lastActive != -1) {
            var $layer = this.layerList[lastActive];
            // There is a chance that the layer already deleted
            if ($layer) {
                $layer.$.off('propertyChanged.layerEditor');

                $layer.deActivate();
            }
        }
        this.activeLayerIndex = index;

        if (index != -1) {
            var $layer = this.layerList[index];
            $layer.$.on('propertyChanged.layerEditor', $.proxy(this.activeLayerPropertyChanged, this));

            $layer.animation.activate();

            var properties = $layer.property;
            for (var name in properties) {
                this.activeLayerPropertyChanged({
                    target: $layer
                }, name, properties[name]);
            }
        }
    };

    AdminSlideLayerManager.prototype.activeLayerPropertyChanged = function (e, name, value) {
        if (typeof this['_formSet' + name] === 'function') {
            this['_formSet' + name](value, e.target);
        } else {
            var field = this.toolboxForm[name].data('field');
            if (typeof field !== 'undefined') {
                field.insideChange(value);
            }
        }
    };

    AdminSlideLayerManager.prototype._formSetname = function (value) {

    };

    AdminSlideLayerManager.prototype._formSetnameSynced = function (value) {

    };

    AdminSlideLayerManager.prototype._formSetdesktopPortrait = function (value, layer) {
        this.toolboxForm.showFieldDesktopPortrait.data('field').insideChange(value);
    };

    AdminSlideLayerManager.prototype._formSetdesktopLandscape = function (value, layer) {
        this.toolboxForm.showFieldDesktopLandscape.data('field').insideChange(value);
    };

    AdminSlideLayerManager.prototype._formSettabletPortrait = function (value, layer) {
        this.toolboxForm.showFieldTabletPortrait.data('field').insideChange(value);
    };

    AdminSlideLayerManager.prototype._formSettabletLandscape = function (value, layer) {
        this.toolboxForm.showFieldTabletLandscape.data('field').insideChange(value);
    };

    AdminSlideLayerManager.prototype._formSetmobilePortrait = function (value, layer) {
        this.toolboxForm.showFieldMobilePortrait.data('field').insideChange(value);
    };

    AdminSlideLayerManager.prototype._formSetmobileLandscape = function (value, layer) {
        this.toolboxForm.showFieldMobileLandscape.data('field').insideChange(value);
    };

    scope.NextendSmartSliderAdminSlideLayerManager = AdminSlideLayerManager;

})(nextend.smartSlider, n2, window);