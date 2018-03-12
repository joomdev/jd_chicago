;
(function ($, scope) {

    function NextendFontEditorController(previewModesList, defaultFamily) {
        this.defaultFamily = defaultFamily;
        NextendVisualEditorController.prototype.constructor.apply(this, arguments);

        this.preview = $('#n2-font-editor-preview');

        this.initBackgroundColor();
    }

    NextendFontEditorController.prototype = Object.create(NextendVisualEditorController.prototype);
    NextendFontEditorController.prototype.constructor = NextendFontEditorController;

    NextendFontEditorController.prototype.loadDefaults = function () {
        NextendVisualEditorController.prototype.loadDefaults.call(this);
        this.type = 'font';
        this.preview = null;
        this.fontSize = 14;
    };

    NextendFontEditorController.prototype.initPreviewModes = function () {

        this.previewModes = {
            1: [this.previewModesList['simple']],
            2: [this.previewModesList['link'], this.previewModesList['hover'], this.previewModesList['accordionslidetitle']],
            3: [this.previewModesList['paragraph']]
        };
    };

    NextendFontEditorController.prototype.initRenderer = function () {
        return new NextendFontRenderer(this);
    };

    NextendFontEditorController.prototype.initEditor = function () {
        return new NextendFontEditor();
    };

    NextendFontEditorController.prototype._load = function (visual, tabs, parameters) {
        if (visual.length) {
            visual[0] = $.extend({}, this.getEmptyFont(), visual[0]);
        }

        NextendVisualEditorController.prototype._load.call(this, visual, tabs, parameters);
    };

    NextendFontEditorController.prototype.getEmptyFont = function () {
        return {
            color: "000000ff",
            size: "14||px",
            tshadow: "0|*|0|*|0|*|000000ff",
            afont: this.defaultFamily,
            lineheight: "1.5",
            bold: 0,
            italic: 0,
            underline: 0,
            align: "left",
            letterspacing: "normal",
            wordspacing: "normal",
            texttransform: "none",
            extra: ""
        };
    };

    NextendFontEditorController.prototype.getCleanVisual = function () {
        return {
            extra: ''
        };
    };

    NextendFontEditorController.prototype.getEmptyVisual = function () {
        return [this.getEmptyFont()];
    };

    NextendFontEditorController.prototype.setFontSize = function (fontSize) {
        this.fontSize = fontSize;
        this.preview.css('fontSize', fontSize);
    };

    NextendFontEditorController.prototype.initBackgroundColor = function () {

        new NextendElementText("n2-font-editor-background-color");
        new NextendElementColor("n2-font-editor-background-color", 0);

        var box = this.lightbox.find('.n2-editor-preview-box');
        $('#n2-font-editor-background-color').on('nextendChange', function () {
            box.css('background', '#' + $(this).val());
        });
    };

    NextendFontEditorController.prototype._renderPreview = function () {
        NextendVisualEditorController.prototype._renderPreview.call(this);
        this.addStyle(this.renderer.getCSS(this.currentPreviewMode, '', '.' + this.getPreviewCssClass(), this.currentVisual, {
            activeTab: this.currentTabIndex
        }));
    };

    NextendFontEditorController.prototype.setPreview = function (mode) {

        var html = '';
        if (typeof this.localModePreview[mode] !== 'undefined') {
            html = this.localModePreview[mode];
        } else {
            html = this.previewModesList[mode].preview;
        }

        var fontClassName = this.getPreviewCssClass(),
            styleClassName = nextend.fontManager.styleClassName,
            styleClassName2 = nextend.fontManager.styleClassName2;

        html = html.replace(/\{([^]*?)\}/g, function (match, script) {
            return eval(script);
        });

        this.preview.html(html);
    };

    NextendFontEditorController.prototype.getPreviewCssClass = function () {
        return 'n2-' + this.type + '-editor-preview';
    };

    scope.NextendFontEditorController = NextendFontEditorController;

    function NextendFontEditor() {

        NextendVisualEditor.prototype.constructor.apply(this, arguments);

        this.fields = {
            family: {
                element: $('#n2-font-editorfamily'),
                events: {
                    'nextendChange.n2-editor': $.proxy(this.changeFamily, this)
                }
            },
            color: {
                element: $('#n2-font-editorcolor'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeColor, this)
                }
            },
            size: {
                element: $('#n2-font-editorsize'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeSize, this)
                }
            },
            lineHeight: {
                element: $('#n2-font-editorlineheight'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeLineHeight, this)
                }
            },
            decoration: {
                element: $('#n2-font-editordecoration'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeDecoration, this)
                }
            },
            align: {
                element: $('#n2-font-editortextalign'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeAlign, this)
                }
            },
            shadow: {
                element: $('#n2-font-editortshadow'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeShadow, this)
                }
            },
            letterSpacing: {
                element: $('#n2-font-editorletterspacing'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeLetterSpacing, this)
                }
            },
            wordSpacing: {
                element: $('#n2-font-editorwordspacing'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeWordSpacing, this)
                }
            },
            textTransform: {
                element: $('#n2-font-editortexttransform'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeTextTransform, this)
                }
            },
            css: {
                element: $('#n2-font-editorextracss'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeCSS, this)
                }
            }
        }
    };

    NextendFontEditor.prototype = Object.create(NextendVisualEditor.prototype);
    NextendFontEditor.prototype.constructor = NextendFontEditor;

    NextendFontEditor.prototype.load = function (values) {
        this._off();
        var family = values.afont.split('||'); // split for a while for compatibility
        this.fields.family.element.data('field').insideChange(family[0]);

        this.fields.color.element.data('field').insideChange(values.color);
        this.fields.size.element.data('field').insideChange(values.size
                .split('||')
                .join('|*|')
        );

        this.fields.lineHeight.element.data('field').insideChange(values.lineheight);
        this.fields.decoration.element.data('field').insideChange([
            values.bold == 1 ? 'bold' : '',
            values.italic == 1 ? 'italic' : '',
            values.underline == 1 ? 'underline' : ''
        ].join('||'));

        this.fields.align.element.data('field').insideChange(values.align);
        this.fields.shadow.element.data('field').insideChange(values.tshadow.replace(/\|\|px/g, ''));
        this.fields.letterSpacing.element.data('field').insideChange(values.letterspacing);
        this.fields.wordSpacing.element.data('field').insideChange(values.wordspacing);
        this.fields.textTransform.element.data('field').insideChange(values.texttransform);
        this.fields.css.element.data('field').insideChange(values.extra);

        this._on();
    };

    NextendFontEditor.prototype.changeFamily = function () {
        this.trigger('afont', this.fields.family.element.val());
    };

    NextendFontEditor.prototype.changeColor = function () {
        this.trigger('color', this.fields.color.element.val());
    };

    NextendFontEditor.prototype.changeSize = function () {
        this.trigger('size', this.fields.size.element.val().replace('|*|', '||'));
    };

    NextendFontEditor.prototype.changeLineHeight = function () {
        this.trigger('lineheight', this.fields.lineHeight.element.val());
    };

    NextendFontEditor.prototype.changeDecoration = function () {
        var value = this.fields.decoration.element.val();

        var bold = 0;
        if (value.indexOf('bold') != -1) {
            bold = 1;
        }
        this.trigger('bold', bold);

        var italic = 0;
        if (value.indexOf('italic') != -1) {
            italic = 1;
        }
        this.trigger('italic', italic);

        var underline = 0;
        if (value.indexOf('underline') != -1) {
            underline = 1;
        }
        this.trigger('underline', underline);
    };

    NextendFontEditor.prototype.changeAlign = function () {
        this.trigger('align', this.fields.align.element.val());
    };

    NextendFontEditor.prototype.changeShadow = function () {
        this.trigger('tshadow', this.fields.shadow.element.val());
    };

    NextendFontEditor.prototype.changeLetterSpacing = function () {
        this.trigger('letterspacing', this.fields.letterSpacing.element.val());
    };

    NextendFontEditor.prototype.changeWordSpacing = function () {
        this.trigger('wordspacing', this.fields.wordSpacing.element.val());
    };

    NextendFontEditor.prototype.changeTextTransform = function () {
        this.trigger('texttransform', this.fields.textTransform.element.val());
    };

    NextendFontEditor.prototype.changeCSS = function () {
        this.trigger('extra', this.fields.css.element.val());
    };

    scope.NextendFontEditor = NextendFontEditor;


    function NextendFontRenderer() {
        NextendVisualRenderer.prototype.constructor.apply(this, arguments);
    }

    NextendFontRenderer.prototype = Object.create(NextendVisualRenderer.prototype);
    NextendFontRenderer.prototype.constructor = NextendFontRenderer;


    NextendFontRenderer.prototype.getCSS = function (modeKey, pre, selector, visualTabs, parameters) {
        visualTabs = $.extend([], visualTabs);
        visualTabs[0] = $.extend(this.editorController.getEmptyFont(), visualTabs[0]);
        if (this.editorController.previewModesList[modeKey].renderOptions.combined) {
            for (var i = 1; i < visualTabs.length; i++) {
                visualTabs[i] = $.extend({}, visualTabs[i - 1], visualTabs[i]);
                if (visualTabs[i].size == visualTabs[0].size) {
                    visualTabs[i].size = '100||%';
                }
            }
        }
        return NextendVisualRenderer.prototype.getCSS.call(this, modeKey, pre, selector, visualTabs, parameters);
    };

    NextendFontRenderer.prototype.makeStylecolor = function (value, target) {
        target.color = '#' + value.substr(0, 6) + ";\ncolor: " + N2Color.hex2rgbaCSS(value);
    };

    NextendFontRenderer.prototype.makeStylesize = function (value, target) {
        var fontSize = value.split('||');
        if (fontSize[1] == 'px') {
            target.fontSize = (fontSize[0] / this.editorController.fontSize * 100) + '%';
        } else {
            target.fontSize = value.replace('||', '');
        }
    };

    NextendFontRenderer.prototype.makeStyletshadow = function (value, target) {
        var ts = value.split('|*|');
        if (ts[0] == '0' && ts[1] == '0' && ts[2] == '0') {
            target.textShadow = 'none';
        } else {
            target.textShadow = ts[0] + 'px ' + ts[1] + 'px ' + ts[2] + 'px ' + N2Color.hex2rgbaCSS(ts[3]);
        }
    };

    NextendFontRenderer.prototype.makeStyleafont = function (value, target) {
        var families = value.split(',');
        for (var i = 0; i < families.length; i++) {
            families[i] = this.getFamily(families[i]
                .replace(/^\s+|\s+$/gm, '')
                .replace(/"|'/gm, ''));
        }
        target.fontFamily = families.join(',');
    };
    NextendFontRenderer.prototype.getFamily = function (family) {
        $(window).trigger('n2Family', [family]);
        return "'" + family + "'";
    };

    NextendFontRenderer.prototype.makeStylelineheight = function (value, target) {

        target.lineHeight = value;
    };

    NextendFontRenderer.prototype.makeStylebold = function (value, target) {
        if (value == 1) {
            target.fontWeight = 'bold';
        } else {
            target.fontWeight = 'normal';
        }
    };

    NextendFontRenderer.prototype.makeStyleitalic = function (value, target) {
        if (value == 1) {
            target.fontStyle = 'italic';
        } else {
            target.fontStyle = 'normal';
        }
    };

    NextendFontRenderer.prototype.makeStyleunderline = function (value, target) {
        if (value == 1) {
            target.textDecoration = 'underline';
        } else {
            target.textDecoration = 'none';
        }
    };

    NextendFontRenderer.prototype.makeStylealign = function (value, target) {

        target.textAlign = value;
    };

    NextendFontRenderer.prototype.makeStyleletterspacing = function (value, target) {
        target.letterSpacing = value;
    };

    NextendFontRenderer.prototype.makeStylewordspacing = function (value, target) {
        target.wordSpacing = value;
    };

    NextendFontRenderer.prototype.makeStyletexttransform = function (value, target) {
        target.textTransform = value;
    };

    NextendFontRenderer.prototype.makeStyleextra = function (value, target) {

        target.raw = value;
    };

    scope.NextendFontRenderer = NextendFontRenderer;

})
(n2, window);
