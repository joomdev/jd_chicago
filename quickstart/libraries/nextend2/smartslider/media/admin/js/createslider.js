(function ($, scope) {

    function NextendSmartSliderCreateSlider(ajaxUrl) {
        this.createSliderModal = null;
        this.ajaxUrl = ajaxUrl;
        $('.n2-ss-create-slider').click($.proxy(function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            this.showModal();
        }, this));

        this.notificationStack = new NextendNotificationCenterStackModal($('body'));
        $('.n2-ss-demo-slider').click($.proxy(function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            this.showDemoSliders();
        

        }, this));
    }

    NextendSmartSliderCreateSlider.prototype.showModal = function () {
        if (!this.createSliderModal) {
            var ajaxUrl = this.ajaxUrl;
            var presets = [];

            presets.push({
                key: 'default',
                name: n2_('Default'),
                image: '$ss$/admin/images/sliderpresets/default.png'
            });
            presets.push({
                key: 'thumbnailhorizontal',
                name: n2_('Thumbnail - horizontal'),
                image: '$ss$/admin/images/sliderpresets/thumbnailhorizontal.png'
            });
            presets.push({
                key: 'caption',
                name: n2_('Caption'),
                image: '$ss$/admin/images/sliderpresets/caption.png'
            });
            this.createSliderModal = new NextendModal({
                zero: {
                    size: [
                        N2SSPRO ? 750 : 580,
                        N2SSPRO ? 630 : 390
                    ],
                    title: n2_('Create Slider'),
                    back: false,
                    close: true,
                    content: '<form class="n2-form"></form>',
                    controls: [
                        '<a href="#" class="n2-button n2-button-big n2-button-green n2-uc n2-h4">' + n2_('Create') + '</a>'
                    ],
                    fn: {
                        show: function () {

                            var button = this.controls.find('.n2-button-green'),
                                form = this.content.find('.n2-form').on('submit', function (e) {
                                    e.preventDefault();
                                    button.trigger('click');
                                });

                            form.append(this.createInput(n2_('Slider name'), 'slidertitle', 'width: 240px;'));
                            form.append(this.createInputUnit(n2_('Width'), 'sliderwidth', 'px', 'width: 30px;'));
                            form.append(this.createInputUnit(n2_('Height'), 'sliderheight', 'px', 'width: 30px;'));

                            new NextendElementAutocompleteSimple("sliderwidth", ["1920", "1400", "1000", "800", "600", "400"]);
                            new NextendElementAutocompleteSimple("sliderheight", ["800", "600", "500", "400", "300", "200"]);

                            var sliderTitle = $('#slidertitle').val(n2_('Slider')).focus(),
                                sliderWidth = $('#sliderwidth').val(900),
                                sliderHeight = $('#sliderheight').val(500);

                            sliderWidth.parent().addClass('n2-form-element-autocomplete ui-front');
                            sliderHeight.parent().addClass('n2-form-element-autocomplete ui-front');

                            this.createHeading(n2_('Preset')).appendTo(this.content);

                            var imageRadio = this.createImageRadio(presets)
                                    .css('height', N2SSPRO ? 360 : 240)
                                    .appendTo(this.content),
                                sliderPreset = imageRadio.find('input');

                            button.on('click', $.proxy(function () {

                                NextendAjaxHelper.ajax({
                                    type: "POST",
                                    url: NextendAjaxHelper.makeAjaxUrl(ajaxUrl, {
                                        nextendaction: 'create'
                                    }),
                                    data: {
                                        sliderTitle: sliderTitle.val(),
                                        sliderSizeWidth: sliderWidth.val(),
                                        sliderSizeHeight: sliderHeight.val(),
                                        preset: sliderPreset.val()
                                    },
                                    dataType: 'json'
                                }).done($.proxy(function (response) {
                                    NextendAjaxHelper.startLoading();
                                }, this));

                            }, this));
                        }
                    }
                }
            });
        }
        this.createSliderModal.show();
    };

    NextendSmartSliderCreateSlider.prototype.showDemoSliders = function () {
        var that = this;
        $('body').css('overflow', 'hidden');
        var frame = $('<iframe src="//smartslider3.com/demo-import/?pro=' + (N2SSPRO ? '1' : '0') + '" frameborder="0"></iframe>').css({
                position: 'fixed',
                zIndex: 100000,
                left: 0,
                top: 0,
                width: '100%',
                height: '100%'
            }).appendTo('body'),
            closeFrame = function () {
                $('body').css('overflow', '');
                frame.remove();
                window.removeEventListener("message", listener, false);
                that.notificationStack.popStack();
            },
            listener = function (e) {
                if (e.origin !== "http://smartslider3.com" && e.origin !== "https://smartslider3.com")
                    return;
                var msg = e.data;
                switch (msg.key) {
                    case 'importSlider':
                        NextendAjaxHelper.ajax({
                            type: "POST",
                            url: NextendAjaxHelper.makeAjaxUrl(that.ajaxUrl, {
                                nextendaction: 'importDemo'
                            }),
                            data: {
                                key: Base64.encode(msg.data.href.replace(/^(http(s)?:)?\/\//, '//'))
                            },
                            dataType: 'json'
                        }).fail(function () {
                            //closeFrame();
                        });
                        break;
                    case 'closeWindow':
                        closeFrame();
                }
            };

        this.notificationStack.enableStack();
        NextendEsc.add($.proxy(function () {
            closeFrame();
            return true;
        }, this));

        window.addEventListener("message", listener, false);
    };

    scope.NextendSmartSliderCreateSlider = NextendSmartSliderCreateSlider;

})(n2, window);