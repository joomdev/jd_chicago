;
(function (smartSlider, $, scope, undefined) {


    function SmartSliderAdminSlide(sliderElementID, slideContentElementID, isUploadDisabled, uploadUrl, uploadDir) {
        this.readyDeferred = $.Deferred();
        smartSlider.slide = this;

        this._warnInternetExplorerUsers();

        this.$slideContentElement = $('#' + slideContentElementID);
        this.slideStartValue = this.$slideContentElement.val();
        this.$sliderElement = $('#' + sliderElementID);


        smartSlider.frontend = window["n2-ss-0"];

        var fontSize = this.$sliderElement.data('fontsize');

        nextend.fontManager.setFontSize(fontSize);
        nextend.styleManager.setFontSize(fontSize);


        smartSlider.$currentSlideElement = smartSlider.frontend.adminGetCurrentSlideElement();

        new SmartSliderAdminGenerator();

        smartSlider.$currentSlideElement.addClass('n2-ss-currently-edited-slide');
        var staticSlide = smartSlider.frontend.parameters.isStaticEdited;
        new NextendSmartSliderAdminSlideLayerManager(smartSlider.$currentSlideElement.data('slide'), staticSlide, isUploadDisabled, uploadUrl, uploadDir);

        if (!staticSlide) {
            this._initializeBackgroundChanger();
        }

        this.readyDeferred.resolve();

        $('#smartslider-form').on({
            checkChanged: $.proxy(this.prepareFormForCheck, this),
            submit: $.proxy(this.onSlideSubmit, this)
        });
    };

    SmartSliderAdminSlide.prototype.ready = function (fn) {
        this.readyDeferred.done(fn);
    };

    SmartSliderAdminSlide.prototype.prepareFormForCheck = function () {
        var data = JSON.stringify(smartSlider.layerManager.getData()),
            startData = JSON.stringify(JSON.parse(Base64.decode(this.slideStartValue)));

        this.$slideContentElement.val(startData == data ? this.slideStartValue : Base64.encode(data));
    };

    SmartSliderAdminSlide.prototype.onSlideSubmit = function (e) {
        if (!nextend.isPreview) {
            this.prepareForm();
            e.preventDefault();

            nextend.askToSave = false;
            NextendAjaxHelper.ajax({
                type: 'POST',
                url: NextendAjaxHelper.makeAjaxUrl(window.location.href),
                data: $('#smartslider-form').serialize(),
                dataType: 'json'
            }).done(function () {
                nextend.askToSave = true;
                $('#smartslider-form').trigger('saved');
            });
        }
    };

    SmartSliderAdminSlide.prototype.prepareForm = function () {
        this.$slideContentElement.val(Base64.encode(JSON.stringify(smartSlider.layerManager.getData())));
    };

    SmartSliderAdminSlide.prototype._initializeBackgroundChanger = function () {
        this.background = {
            slideBackgroundColorField: $('#slidebackgroundColor'),
            slideBackgroundImageField: $('#slidebackgroundImage'),
            slideBackgroundImageOpacity: $('#slidebackgroundImageOpacity'),
            slideBackgroundModeField: $('#slidebackgroundMode'),
            backgroundImageElement: smartSlider.$currentSlideElement.find('.nextend-slide-bg'),
            canvas: smartSlider.$currentSlideElement.find('.n2-ss-slide-background')
        };

        this.background.slideBackgroundColorField.on('nextendChange', $.proxy(this.__onAfterBackgroundColorChange, this));
        this.background.slideBackgroundImageField.on('nextendChange', $.proxy(this.__onAfterBackgroundImageChange, this));
        this.background.slideBackgroundImageOpacity.on('nextendChange', $.proxy(this.__onAfterBackgroundImageOpacityChange, this));
        this.background.slideBackgroundModeField.on('nextendChange', $.proxy(this.__onAfterBackgroundImageChange, this));

        // Auto fill thumbnail if empty
        var thumbnail = $('#slidethumbnail');
        if (thumbnail.val() == '') {
            var itemImage = $('#item_imageimage'),
                cb = $.proxy(function (image) {
                    if (image != '' && image != '$system$/images/placeholder/image.png') {
                        thumbnail.val(image).trigger('change');
                        this.background.slideBackgroundImageField.off('.slidethumbnail');
                        itemImage.off('.slidethumbnail');
                    }
                }, this);
            this.background.slideBackgroundImageField.on('nextendChange.slidethumbnail', $.proxy(function () {
                cb(this.background.slideBackgroundImageField.val());
            }, this));
            itemImage.on('nextendChange.slidethumbnail', $.proxy(function () {
                cb(itemImage.val());
            }, this));
        }
    };

    SmartSliderAdminSlide.prototype.__onAfterBackgroundColorChange = function () {
        var backgroundColor = this.background.slideBackgroundColorField.val();
        if (backgroundColor.substr(6, 8) == '00') {
            this.background.canvas.css('background', '');
        } else {
            this.background.canvas.css('background', '#' + backgroundColor.substr(0, 6))
                .css('background', N2Color.hex2rgbaCSS(backgroundColor));
        }
    };

    SmartSliderAdminSlide.prototype.__onAfterBackgroundImageOpacityChange = function () {
        smartSlider.$currentSlideElement.data('slideBackground').setOpacity(this.background.slideBackgroundImageOpacity.val() / 100);
    };

    /**
     * This event callback is responsible for the slide editor to show the apropiate background color and image.
     * @private
     */
    SmartSliderAdminSlide.prototype.__onAfterBackgroundImageChange = function () {
        smartSlider.$currentSlideElement.data('slideBackground').changeDesktop(smartSlider.generator.fill(this.background.slideBackgroundImageField.val()), '', this.background.slideBackgroundModeField.val());
        this.__onAfterBackgroundImageOpacityChange();
    };

    /**
     * Warn old version IE users that the editor may fail to wrok in their browser.
     * @private
     */
    SmartSliderAdminSlide.prototype._warnInternetExplorerUsers = function () {
        var ie = this.__isInternetExplorer();
        if (ie && ie < 10) {
            alert(window.ss2lang.The_editor_was_tested_under_Internet_Explorer_10_Firefox_and_Chrome_Please_use_one_of_the_tested_browser);
        }
    };

    /**
     * @returns Internet Explorer version number or false
     * @private
     */
    SmartSliderAdminSlide.prototype.__isInternetExplorer = function () {
        var myNav = navigator.userAgent.toLowerCase();
        return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
    };

    SmartSliderAdminSlide.prototype.getLayout = function () {
        var propertiesRaw = $('#smartslider-form').serializeArray(),
            properties = {};

        for (var i = 0; i < propertiesRaw.length; i++) {
            var m = propertiesRaw[i].name.match(/slide\[(.*?)\]/);
            if (m) {
                properties[m[1]] = propertiesRaw[i].value;
            }
        }
        delete properties['generator'];
        delete properties['published'];
        delete properties['publishdates'];
        delete properties['record-start'];
        delete properties['record-slides'];
        delete properties['slide'];

        properties['slide'] = smartSlider.layerManager.getData();
        return properties;
    };

    SmartSliderAdminSlide.prototype.loadLayout = function (properties, slideDataOverwrite, layerOverwrite) {
        // we are working on references!
        var slide = properties['slide'];
        delete properties['slide'];
        if (layerOverwrite) {
            smartSlider.layerManager.loadData(slide, true);
        } else {
            smartSlider.layerManager.loadData(slide, false);
        }
        if (slideDataOverwrite) {
            for (var k in properties) {
                $('#slide' + k).val(properties[k]).trigger('change');
            }
        }
        properties['slide'] = slide;
    };

    scope.SmartSliderAdminSlide = SmartSliderAdminSlide;

})(nextend.smartSlider, n2, window);