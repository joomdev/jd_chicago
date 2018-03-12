;
(function ($, scope) {

    function NextendImageEditorController() {
        NextendVisualEditorControllerWithEditor.prototype.constructor.apply(this, arguments);
    };

    NextendImageEditorController.prototype = Object.create(NextendVisualEditorControllerWithEditor.prototype);
    NextendImageEditorController.prototype.constructor = NextendImageEditorController;

    NextendImageEditorController.prototype.loadDefaults = function () {
        NextendVisualEditorControllerWithEditor.prototype.loadDefaults.call(this);
        this.type = 'image';
        this.currentImage = '';
    };

    NextendImageEditorController.prototype.get = function (type) {
        return this.currentVisual;
    };

    NextendImageEditorController.prototype.getEmptyVisual = function () {
        return {
            desktop: {
                size: '0|*|0'
            },
            tablet: {
                image: '',
                size: '0|*|0'
            },
            mobile: {
                image: '',
                size: '0|*|0'
            }
        };
    };

    NextendImageEditorController.prototype.initEditor = function () {
        return new NextendImageEditor();
    };

    NextendImageEditorController.prototype._load = function (visual, tabs, parameters) {
        this.currentImage = visual.visual.image;
        NextendVisualEditorControllerWithEditor.prototype._load.call(this, visual.value, tabs, parameters);
    };

    NextendImageEditorController.prototype.loadToEditor = function () {
        this.editor.load(this.currentImage, this.currentVisual);
    };

    NextendImageEditorController.prototype.propertyChanged = function (e, device, property, value) {
        this.isChanged = true;
        this.currentVisual[device][property] = value;
    };

    scope.NextendImageEditorController = NextendImageEditorController;

    function NextendImageEditor() {
        this.previews = null;
        this.desktopImage = '';

        NextendVisualEditor.prototype.constructor.apply(this, arguments);

        this.fields = {
            'desktop-size': {
                element: $('#n2-image-editordesktop-size'),
                events: {
                    'nextendChange.n2-editor': $.proxy(this.changeSize, this, 'desktop')
                }
            },
            'tablet-image': {
                element: $('#n2-image-editortablet-image'),
                events: {
                    'nextendChange.n2-editor': $.proxy(this.changeImage, this, 'tablet')
                }
            },
            'tablet-size': {
                element: $('#n2-image-editortablet-size'),
                events: {
                    'nextendChange.n2-editor': $.proxy(this.changeSize, this, 'tablet')
                }
            },
            'mobile-image': {
                element: $('#n2-image-editormobile-image'),
                events: {
                    'nextendChange.n2-editor': $.proxy(this.changeImage, this, 'mobile')
                }
            },
            'mobile-size': {
                element: $('#n2-image-editormobile-size'),
                events: {
                    'nextendChange.n2-editor': $.proxy(this.changeSize, this, 'mobile')
                }
            }
        }

        this.previews = {
            desktop: $('#n2-image-editordesktop-preview'),
            tablet: $('#n2-image-editortablet-preview'),
            mobile: $('#n2-image-editormobile-preview')
        };

        var generateTablet = $(this.buttonGenerate())
            .on('click', $.proxy(this.generateImage, this, 'tablet'))
            .insertAfter(this.fields['tablet-image'].element.parent());

        var generateMobile = $(this.buttonGenerate())
            .on('click', $.proxy(this.generateImage, this, 'mobile'))
            .insertAfter(this.fields['mobile-image'].element.parent());
    };

    NextendImageEditor.prototype = Object.create(NextendVisualEditor.prototype);
    NextendImageEditor.prototype.constructor = NextendImageEditor;

    NextendImageEditor.prototype.load = function (image, values) {
        this._off();
        for (var k in this.fields) {
            var keys = k.split('-');
            this.fields[k].element.data('field').insideChange(values[keys[0]][keys[1]]);
        }
        this.desktopImage = image;
        this.makePreview('desktop', image);

        if (values.desktop.size == '0|*|0') {
            this.getImageSize(image)
                .done($.proxy(function (width, height) {
                    this.fields['desktop-size'].element.data('field').insideChange(width + '|*|' + height);
                }, this));
        }

        for (var k in values) {
            if (typeof values[k].image != 'undefined') {
                this.makePreview(k, values[k].image);
            }
        }
        this._on();
    };

    NextendImageEditor.prototype.changeImage = function (device, e, field) {
        var image = field.element.val();
        if (this.makePreview(device, image)) {
            this.getImageSize(image)
                .done($.proxy(function (width, height) {
                    this.fields[device + '-size'].element.data('field').insideChange(width + '|*|' + height);
                }, this));
        } else {
            this.fields[device + '-size'].element.data('field').insideChange('0|*|0');
        }

        this.trigger(device, 'image', image);
    };

    NextendImageEditor.prototype.changeSize = function (device, e, field) {
        this.trigger(device, 'size', field.element.val());
    };

    NextendImageEditor.prototype.makePreview = function (device, image) {
        if (image) {
            this.previews[device].html('<img style="max-width:100%; max-height: 300px;" src="' + nextend.imageHelper.fixed(image) + '" />');
            return true;
        } else {
            this.previews[device].html('');
            return false;
        }
    };
    NextendImageEditor.prototype.getImageSize = function (image) {
        var deferred = $.Deferred(),
            newImage = new Image();

        newImage.onload = function () {
            deferred.resolve(newImage.width, newImage.height);
        }

        newImage.src = nextend.imageHelper.fixed(image);
        if (newImage.complete || newImage.readyState === 4) {
            newImage.onload();
        }
        return deferred;
    };

    NextendImageEditor.prototype.buttonGenerate = function () {
        return '<a href="#" class="n2-button n2-button-medium n2-button-grey n2-h5 n2-uc">' + n2_('Generate') + '</a>';
    };

    NextendImageEditor.prototype.generateImage = function (device) {
        var image = this.desktopImage;
        if (image == '') {
            nextend.notificationCenter.error(n2_('Desktop image is empty!'), {
                timeout: 3
            });
            return false;
        } else {
            return NextendAjaxHelper.ajax({
                type: "POST",
                url: NextendAjaxHelper.makeAjaxUrl(nextend.imageManager.parameters.ajaxUrl, {
                    nextendaction: 'generateImage'
                }),
                data: {
                    device: device,
                    image: image
                },
                dataType: 'json'
            }).done($.proxy(function (response) {
                var image = response.data.image;
                this.fields[device + '-image'].element.data('field').insideChange(nextend.imageHelper.make(image));
            }, this));
        }
    };

    NextendImageEditor.prototype.trigger = function (device, property, value) {
        this.$.trigger('change', [device, property, value]);
    };

    scope.NextendImageEditor = NextendImageEditor;

})
(n2, window);
