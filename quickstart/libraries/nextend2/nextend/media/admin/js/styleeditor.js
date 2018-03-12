;
(function ($, scope) {

    function NextendStyleEditorController() {
        NextendVisualEditorController.prototype.constructor.apply(this, arguments);

        this.preview = $('#n2-style-editor-preview');

        this.initBackgroundColor();
    }

    NextendStyleEditorController.prototype = Object.create(NextendVisualEditorController.prototype);
    NextendStyleEditorController.prototype.constructor = NextendStyleEditorController;

    NextendStyleEditorController.prototype.loadDefaults = function () {
        NextendVisualEditorController.prototype.loadDefaults.call(this);
        this.type = 'style';
        this.preview = null;
    };

    NextendStyleEditorController.prototype.initPreviewModes = function () {

        this.previewModes = {
            2: [this.previewModesList['button'], this.previewModesList['box']],
            3: [this.previewModesList['paragraph']]
        };
    };

    NextendStyleEditorController.prototype.initRenderer = function () {
        return new NextendStyleRenderer(this);
    };

    NextendStyleEditorController.prototype.initEditor = function () {
        return new NextendStyleEditor();
    };

    NextendStyleEditorController.prototype._load = function (visual, tabs, parameters) {
        if (visual.length) {
            visual[0] = $.extend({}, this.getEmptyStyle(), visual[0]);
        }

        NextendVisualEditorController.prototype._load.call(this, visual, tabs, parameters);
    };

    NextendStyleEditorController.prototype.getEmptyStyle = function () {
        return {
            backgroundcolor: 'ffffff00',
            padding: '0|*|0|*|0|*|0|*|px',
            boxshadow: '0|*|0|*|0|*|0|*|000000ff',
            border: '0|*|solid|*|000000ff',
            borderradius: '0',
            extra: ''
        };
    };

    NextendStyleEditorController.prototype.getCleanVisual = function () {
        return {
            extra: ''
        };
    };

    NextendStyleEditorController.prototype.getEmptyVisual = function () {
        return [this.getEmptyStyle()];
    };

    NextendStyleEditorController.prototype.setFontSize = function (fontSize) {
        this.preview.css('fontSize', fontSize);
    };

    NextendStyleEditorController.prototype.initBackgroundColor = function () {

        new NextendElementText("n2-style-editor-background-color");
        new NextendElementColor("n2-style-editor-background-color", 0);

        var box = this.lightbox.find('.n2-editor-preview-box');
        $('#n2-style-editor-background-color').on('nextendChange', function () {
            box.css('background', '#' + $(this).val());
        });
    };

    NextendStyleEditorController.prototype._renderPreview = function () {
        NextendVisualEditorController.prototype._renderPreview.call(this);

        this.addStyle(this.renderer.getCSS(this.currentPreviewMode, '', '.' + this.getPreviewCssClass(), this.currentVisual, {
            activeTab: this.currentTabIndex
        }));
    };

    NextendStyleEditorController.prototype.setPreview = function (mode) {

        var html = '';
        if (typeof this.localModePreview[mode] !== 'undefined' && this.localModePreview[mode] != '') {
            html = this.localModePreview[mode];
        } else {
            html = this.previewModesList[mode].preview;
        }

        var styleClassName = this.getPreviewCssClass(),
            fontClassName = nextend.styleManager.fontClassName,
            fontClassName2 = nextend.styleManager.fontClassName2,
            styleClassName2 = nextend.styleManager.styleClassName2;

        html = html.replace(/\{([^]*?)\}/g, function (match, script) {
            return eval(script);
        });

        this.preview.html(html);
    };

    NextendStyleEditorController.prototype.getPreviewCssClass = function () {
        return 'n2-' + this.type + '-editor-preview';
    };

    scope.NextendStyleEditorController = NextendStyleEditorController;

    function NextendStyleEditor() {

        NextendVisualEditor.prototype.constructor.apply(this, arguments);

        this.fields = {
            backgroundColor: {
                element: $('#n2-style-editorbackgroundcolor'),
                events: {
                    'nextendChange.n2-editor': $.proxy(this.changeBackgroundColor, this)
                }
            },
            padding: {
                element: $('#n2-style-editorpadding'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changePadding, this)
                }
            },
            boxShadow: {
                element: $('#n2-style-editorboxshadow'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeBoxShadow, this)
                }
            },
            border: {
                element: $('#n2-style-editorborder'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeBorder, this)
                }
            },
            borderRadius: {
                element: $('#n2-style-editorborderradius'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeBorderRadius, this)
                }
            },
            extracss: {
                element: $('#n2-style-editorextracss'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeExtraCSS, this)
                }
            }
        };
    };

    NextendStyleEditor.prototype = Object.create(NextendVisualEditor.prototype);
    NextendStyleEditor.prototype.constructor = NextendStyleEditor;

    NextendStyleEditor.prototype.load = function (values) {
        this._off();
        this.fields.backgroundColor.element.data('field').insideChange(values.backgroundcolor);
        this.fields.padding.element.data('field').insideChange(values.padding);
        this.fields.boxShadow.element.data('field').insideChange(values.boxshadow);
        this.fields.border.element.data('field').insideChange(values.border);
        this.fields.borderRadius.element.data('field').insideChange(values.borderradius);
        this.fields.extracss.element.data('field').insideChange(values.extra);
        this._on();
    };

    NextendStyleEditor.prototype.changeBackgroundColor = function () {
        this.trigger('backgroundcolor', this.fields.backgroundColor.element.val());

    };

    NextendStyleEditor.prototype.changePadding = function () {
        this.trigger('padding', this.fields.padding.element.val());
    };

    NextendStyleEditor.prototype.changeBoxShadow = function () {
        this.trigger('boxshadow', this.fields.boxShadow.element.val());
    };

    NextendStyleEditor.prototype.changeBorder = function () {
        this.trigger('border', this.fields.border.element.val());
    };

    NextendStyleEditor.prototype.changeBorderRadius = function () {
        this.trigger('borderradius', this.fields.borderRadius.element.val());
    };

    NextendStyleEditor.prototype.changeExtraCSS = function () {
        this.trigger('extra', this.fields.extracss.element.val());
    };

    scope.NextendStyleEditor = NextendStyleEditor;


    function NextendStyleRenderer() {
        NextendVisualRenderer.prototype.constructor.apply(this, arguments);
    }

    NextendStyleRenderer.prototype = Object.create(NextendVisualRenderer.prototype);
    NextendStyleRenderer.prototype.constructor = NextendStyleRenderer;


    NextendStyleRenderer.prototype.getCSS = function (modeKey, pre, selector, visualTabs, parameters) {
        visualTabs[0] = $.extend(this.editorController.getEmptyStyle(), visualTabs[0]);
        return NextendVisualRenderer.prototype.getCSS.call(this, modeKey, pre, selector, visualTabs, parameters);
    };

    NextendStyleRenderer.prototype.makeStylebackgroundcolor = function (value, target) {
        target.background = '#' + value.substr(0, 6) + ";\n\tbackground: " + N2Color.hex2rgbaCSS(value);
    };

    NextendStyleRenderer.prototype.makeStylepadding = function (value, target) {
        var padding = value.split('|*|'),
            unit = padding.pop();
        for (var i = 0; i < padding.length; i++) {
            padding[i] += unit;
        }
        target.padding = padding.join(' ');
    };

    NextendStyleRenderer.prototype.makeStyleboxshadow = function (value, target) {
        var s = value.split('|*|');
        if (s[0] == '0' && s[1] == '0' && s[2] == '0' && s[3] == '0') {
            target.boxShadow = 'none';
        } else {
            target.boxShadow = s[0] + 'px ' + s[1] + 'px ' + s[2] + 'px ' + s[3] + 'px ' + N2Color.hex2rgbaCSS(s[4]);
        }
    };

    NextendStyleRenderer.prototype.makeStyleborder = function (value, target) {
        var border = value.split('|*|');

        target.borderWidth = border[0] + 'px';
        target.borderStyle = border[1];
        target.borderColor = '#' + border[2].substr(0, 6) + ";\n\tborder-color:" + N2Color.hex2rgbaCSS(border[2]);
    };

    NextendStyleRenderer.prototype.makeStyleborderradius = function (value, target) {
        var radius = value.split('|*|');
        radius.push('');
        target.borderRadius = value + 'px';
    };

    NextendStyleRenderer.prototype.makeStyleextra = function (value, target) {

        target.raw = value;
    }

})
(n2, window);
