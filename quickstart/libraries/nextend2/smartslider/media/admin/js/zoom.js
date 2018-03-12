(function (smartSlider, $, scope, undefined) {
    nextend['ssBeforeResponsive'] = function () {
        new NextendSmartSliderAdminZoom(this);
    };

    function NextendSmartSliderAdminZoom(responsive) {
        this.key = 'n2-ss-editor-device-lock-mode';
        this.devices = {
            unknownUnknown: $('<div />')
        };
        this.responsive = responsive;
        this.responsive.setOrientation('portrait');
        this.responsive.parameters.onResizeEnabled = 0;
        this.responsive.parameters.forceFull = 0; // We should disable force full feature on admin dashboard as it won't render before the sidebar
        this.responsive._getDevice = this.responsive._getDeviceZoom;

        this.lock = $('#n2-ss-lock').on('click', $.proxy(this.switchLock, this));

        var desktopWidth = responsive.parameters.sliderWidthToDevice['desktopPortrait'];

        this.container = this.responsive.containerElement.closest('.n2-ss-container-device').addBack();
        this.container.width(desktopWidth);
        this.containerWidth = desktopWidth;

        this.initZoom();

        var tr = $('#n2-ss-devices .n2-tr'),
            modes = responsive.parameters.deviceModes;

        this.devices.desktopPortrait = $('<div class="n2-td n2-panel-option" data-device="desktop" data-orientation="portrait"><i class="n2-i n2-it n2-i-v-desktop"></i></div>').appendTo(tr);
        if (modes.desktopLandscape) {
            this.devices.desktopLandscape = $('<div class="n2-td n2-panel-option" data-device="desktop" data-orientation="landscape"><i class="n2-i n2-it n2-i-v-desktop-landscape"></i></div>').appendTo(tr);
        } else {
            this.devices.desktopLandscape = this.devices.desktopPortrait;
        }

        if (modes.tabletPortrait) {
            this.devices.tabletPortrait = $('<div class="n2-td n2-panel-option" data-device="tablet" data-orientation="portrait"><i class="n2-i n2-it n2-i-v-tablet"></i></div>').appendTo(tr);
        } else {
            this.devices.tabletPortrait = this.devices.desktopPortrait;
        }
        if (modes.tabletLandscape) {
            this.devices.tabletLandscape = $('<div class="n2-td n2-panel-option" data-device="tablet" data-orientation="landscape"><i class="n2-i n2-it n2-i-v-tablet-landscape"></i></div>').appendTo(tr);
        } else {
            this.devices.tabletLandscape = this.devices.desktopLandscape;
        }

        if (modes.mobilePortrait) {
            this.devices.mobilePortrait = $('<div class="n2-td n2-panel-option" data-device="mobile" data-orientation="portrait"><i class="n2-i n2-it n2-i-v-mobile"></i></div>').appendTo(tr);
        } else {
            this.devices.mobilePortrait = this.devices.tabletPortrait;
        }
        if (modes.mobileLandscape) {
            this.devices.mobileLandscape = $('<div class="n2-td n2-panel-option" data-device="mobile" data-orientation="landscape"><i class="n2-i n2-it n2-i-v-mobile-landscape"></i></div>').appendTo(tr);
        } else {
            this.devices.mobileLandscape = this.devices.tabletLandscape;
        }

        this.deviceOptions = $('#n2-ss-devices .n2-panel-option');

        $('#n2-ss-devices').css('width', (this.deviceOptions.length * 62) + 'px');

        this.deviceOptions.each($.proxy(function (i, el) {
            $(el).on('click', $.proxy(this.setDeviceMode, this));
        }, this));

        responsive.sliderElement.on('SliderDeviceOrientation', $.proxy(this.onDeviceOrientationChange, this));
    };

    NextendSmartSliderAdminZoom.prototype.onDeviceOrientationChange = function (e, modes) {
        $('#n2-admin').removeClass('n2-ss-mode-' + modes.lastDevice)
            .addClass('n2-ss-mode-' + modes.device);
        this.devices[modes.lastDevice + modes.lastOrientation].removeClass('n2-active');
        this.devices[modes.device + modes.orientation].addClass('n2-active');
    };

    NextendSmartSliderAdminZoom.prototype.setDeviceMode = function (e) {
        var el = $(e.currentTarget);
        if ((e.ctrlKey || e.metaKey) && smartSlider.layerManager) {
            var orientation = el.data('orientation');
            smartSlider.layerManager.copyOrResetMode(el.data('device') + orientation[0].toUpperCase() + orientation.substr(1));
        } else {
            this.responsive.setOrientation(el.data('orientation'));
            this.responsive.setMode(el.data('device'));
        }
    };

    NextendSmartSliderAdminZoom.prototype.switchLock = function (e) {
        e.preventDefault();
        this.lock.toggleClass('n2-active');
        if (this.lock.hasClass('n2-active')) {
            this.setZoomSyncMode();
            this.zoomChange(this.zoom.slider("value"), 'sync', false);

            $.jStorage.set(this.key, 'sync');
        } else {
            this.setZoomFixMode();
            $.jStorage.set(this.key, 'fix');
        }
    };

    NextendSmartSliderAdminZoom.prototype.initZoom = function () {
        var zoom = $("#n2-ss-slider-zoom");
        if (zoom.length > 0) {

            if (typeof zoom[0].slide !== 'undefined') {
                zoom[0].slide = null;
            }

            this.zoom =
                zoom.slider({
                    range: "min",
                    step: 1,
                    value: 1,
                    min: 0,
                    max: 102
                });

            this.responsive.sliderElement.on('SliderResize', $.proxy(this.sliderResize, this));

            if ($.jStorage.get(this.key, 'sync') == 'fix') {
                this.setZoomFixMode();
            } else {
                this.setZoomSyncMode();
                this.lock.addClass('n2-active');
            }

            var parent = zoom.parent(),
                change = $.proxy(function (value) {
                    var oldValue = this.zoom.slider('value');
                    this.zoom.slider('value', oldValue + value);
                }, this),
                interval = null,
                mouseDown = $.proxy(function (value) {
                    change(value);
                    interval = setInterval($.proxy(change, this, value), 1000 / 25);
                    $(window).one('mouseup', function () {
                        if (interval) {
                            clearInterval(interval);
                        }
                    });
                }, this);
            parent.find('.n2-i-minus').on({
                mousedown: $.proxy(mouseDown, this, -1)
            });
            parent.find('.n2-i-plus').on({
                mousedown: $.proxy(mouseDown, this, 1)
            });
        }
    };

    NextendSmartSliderAdminZoom.prototype.sliderResize = function (e, ratios) {
        this.setZoom();
    };

    NextendSmartSliderAdminZoom.prototype.setZoomFixMode = function () {
        this.zoom.off('.n2-ss-zoom')
            .on({
                'slide.n2-ss-zoom': $.proxy(this.zoomChangeFixMode, this),
                'slidechange.n2-ss-zoom': $.proxy(this.zoomChangeFixMode, this)
            });
    };

    NextendSmartSliderAdminZoom.prototype.setZoomSyncMode = function () {

        this.zoom.off('.n2-ss-zoom')
            .on({
                'slide.n2-ss-zoom': $.proxy(this.zoomChangeSyncMode, this),
                'slidechange.n2-ss-zoom': $.proxy(this.zoomChangeSyncMode, this)
            });
    };

    NextendSmartSliderAdminZoom.prototype.zoomChangeFixMode = function (event, ui) {
        this.zoomChange(ui.value, 'fix', ui);
    };

    NextendSmartSliderAdminZoom.prototype.zoomChangeSyncMode = function (event, ui) {
        this.zoomChange(ui.value, 'sync', ui);
    };

    NextendSmartSliderAdminZoom.prototype.zoomChange = function (value, mode, ui) {
        var ratio = 1;
        if (value < 50) {
            ratio = nextend.smallestZoom / this.containerWidth + Math.max(value / 50, 0) * (1 - nextend.smallestZoom / this.containerWidth);
        } else if (value > 52) {
            ratio = 1 + (value - 52) / 50;
        }
        var width = parseInt(ratio * this.containerWidth);
        this.container.width(width);

        switch (mode) {
            case 'sync':
                this.responsive.doResize();
                break;
            default:
                this.responsive.doResize(true);
                break;
        }
        if (ui) {
            ui.handle.innerHTML = width + 'px';
        }
    };

    NextendSmartSliderAdminZoom.prototype.setZoom = function () {
        var ratio = this.responsive.containerElement.width() / this.containerWidth;
        var v = 50;
        if (ratio < 1) {
            v = (ratio - nextend.smallestZoom / this.containerWidth) / (1 - nextend.smallestZoom / this.containerWidth) * 50;
        } else if (ratio > 1) {
            v = (ratio - 1) * 50 + 52;
        }
        var oldValue = this.zoom.slider('value');
        this.zoom.slider('value', v);
    };
})
(nextend.smartSlider, n2, window);