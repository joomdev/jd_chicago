(function ($, scope) {
    "use strict";
    function NextendVisualEditorControllerBase() {
        this.loadDefaults();
        this.lightbox = $('#n2-lightbox-' + this.type);
    }

    NextendVisualEditorControllerBase.prototype.loadDefaults = function () {
        this.type = '';
        this._style = false;
        this.isChanged = false;
        this.visible = false;
    };

    NextendVisualEditorControllerBase.prototype.init = function () {
        this.lightbox = $('#n2-lightbox-' + this.type);
    };


    NextendVisualEditorControllerBase.prototype.pause = function () {

    };

    NextendVisualEditorControllerBase.prototype.getEmptyVisual = function () {
        return [];
    };

    NextendVisualEditorControllerBase.prototype.get = function () {
        return this.currentVisual;
    };

    NextendVisualEditorControllerBase.prototype.load = function (visual, tabs, parameters) {
        this.isChanged = false;
        this.lightbox.addClass('n2-editor-loaded');
        if (visual == '') {
            visual = this.getEmptyVisual();
        }
        this._load(visual, tabs, parameters);
    };

    NextendVisualEditorControllerBase.prototype._load = function (visual, tabs, parameters) {
        this.currentVisual = $.extend(true, {}, visual);
    };

    NextendVisualEditorControllerBase.prototype.addStyle = function (style) {
        if (this._style) {
            this._style.remove();
        }
        this._style = $("<style>" + style + "</style>").appendTo("head");
    };

    NextendVisualEditorControllerBase.prototype.show = function () {
        this.visible = true;
    };

    NextendVisualEditorControllerBase.prototype.close = function () {
        this.visible = false;
    };
    scope.NextendVisualEditorControllerBase = NextendVisualEditorControllerBase;

    function NextendVisualEditorControllerWithEditor() {

        NextendVisualEditorControllerBase.prototype.constructor.apply(this, arguments);

        this.editor = this.initEditor();
        this.editor.$.on('change', $.proxy(this.propertyChanged, this));
    };


    NextendVisualEditorControllerWithEditor.prototype = Object.create(NextendVisualEditorControllerBase.prototype);
    NextendVisualEditorControllerWithEditor.prototype.constructor = NextendVisualEditorControllerWithEditor;


    NextendVisualEditorControllerWithEditor.prototype.initEditor = function () {
        return new NextendVisualEditor();
    };

    NextendVisualEditorControllerWithEditor.prototype.propertyChanged = function (e, property, value) {
        this.isChanged = true;
        this.currentVisual[property] = value;
    };

    NextendVisualEditorControllerWithEditor.prototype._load = function (visual, tabs, parameters) {
        NextendVisualEditorControllerBase.prototype._load.apply(this, arguments);
        this.loadToEditor();
    };

    NextendVisualEditorControllerWithEditor.prototype.loadToEditor = function () {
        this.editor.load(this.currentVisual);
    };

    scope.NextendVisualEditorControllerWithEditor = NextendVisualEditorControllerWithEditor;


    function NextendVisualEditorController(previewModesList) {
        NextendVisualEditorControllerWithEditor.prototype.constructor.apply(this, arguments);

        this.previewModesList = previewModesList;

        this.initPreviewModes();
        if (previewModesList) {

            this.renderer = this.initRenderer();

            this.clearTabButton = this.lightbox.find('.n2-editor-clear-tab')
                .on('click', $.proxy(this.clearCurrentTab, this));


            this.tabField = new NextendElementRadio('n2-' + this.type + '-editor-tabs', ['0']);
            this.tabField.element.on('nextendChange.n2-editor', $.proxy(this.tabChanged, this));

            this.previewModeField = new NextendElementRadio('n2-' + this.type + '-editor-preview-mode', ['0']);
            this.previewModeField.element.on('nextendChange.n2-editor', $.proxy(this.previewModeChanged, this));

            this.previewModeField.options.eq(0).html(this.previewModesList[0].label);
        }
    }

    NextendVisualEditorController.prototype = Object.create(NextendVisualEditorControllerWithEditor.prototype);
    NextendVisualEditorController.prototype.constructor = NextendVisualEditorController;

    NextendVisualEditorController.prototype.loadDefaults = function () {
        NextendVisualEditorControllerWithEditor.prototype.loadDefaults.call(this);

        this.currentPreviewMode = '0';
        this.currentTabIndex = 0;
        this._renderTimeout = 0;
        this._delayStart = 0;
    };

    NextendVisualEditorController.prototype.initPreviewModes = function () {
    };

    NextendVisualEditorController.prototype.initRenderer = function () {
    };

    NextendVisualEditorController.prototype._load = function (visual, tabs, parameters) {

        this.currentVisual = [];
        for (var i = 0; i < visual.length; i++) {
            this.currentVisual[i] = $.extend(true, this.getCleanVisual(), visual[i]);
        }

        this.localModePreview = {};
        if (parameters.previewMode === false) {
            this.availablePreviewMode = false;
        } else {
            this.availablePreviewMode = parameters.previewMode;
            if (tabs === false) {
                tabs = this.getTabs();
            }
            for (var i = this.currentVisual.length; i < tabs.length; i++) {
                this.currentVisual[i] = this.getCleanVisual();
            }
            if (parameters.previewHTML !== false && parameters.previewHTML != '') {
                this.localModePreview[parameters.previewMode] = parameters.previewHTML;
            }
        }

        this.currentTabs = tabs;

        if (tabs === false) {
            tabs = [];
            for (var i = 0; i < this.currentVisual.length; i++) {
                tabs.push('#' + i);
            }
        }

        this.setTabs(tabs);
    };

    NextendVisualEditorController.prototype.getCleanVisual = function () {
        return {};
    };

    NextendVisualEditorController.prototype.getTabs = function () {
        return this.previewModesList[this.availablePreviewMode].tabs;
    };

    NextendVisualEditorController.prototype.setTabs = function (labels) {
        this.tabField.insideChange('0');
        for (var i = this.tabField.values.length - 1; i > 0; i--) {
            this.tabField.removeTabOption(this.tabField.values[i]);
        }
        this.tabField.options.eq(0).html(labels[0]);
        for (var i = 1; i < labels.length; i++) {
            this.tabField.addTabOption(i + '', labels[i]);
        }

        this.makePreviewModes();
    };

    NextendVisualEditorController.prototype.tabChanged = function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }

        var tab = this.tabField.element.val();

        this.currentTabIndex = tab;
        if (typeof this.currentVisual[tab] === 'undefined') {
            this.currentVisual[tab] = {};
        }
        var values = $.extend({}, this.currentVisual[0]);
        if (tab != 0) {
            $.extend(values, this.currentVisual[tab]);
            this.clearTabButton.css('display', '');
        } else {
            this.clearTabButton.css('display', 'none');
        }

        this.editor.load(values);
        this._tabChanged();
    };

    NextendVisualEditorController.prototype._tabChanged = function () {
        this._renderPreview();
    };

    NextendVisualEditorController.prototype.clearCurrentTab = function (e) {
        if (e) {
            e.preventDefault();
        }
        this.currentVisual[this.currentTabIndex] = {};
        this.tabChanged();
        this._renderPreview();
    };

    NextendVisualEditorController.prototype.makePreviewModes = function () {
        var modes = [];
        // Show all preview mode for the tab count
        if (this.availablePreviewMode === false) {
            var tabCount = this.tabField.options.length;
            if (typeof this.previewModes[tabCount] !== "undefined") {
                modes = this.previewModes[tabCount];
            }
            this.setPreviewModes(modes);
        } else {
            modes = [this.previewModesList[this.availablePreviewMode]];
            this.setPreviewModes(modes, this.availablePreviewMode);
        }
    };

    NextendVisualEditorController.prototype.setPreviewModes = function (modes, defaultMode) {
        for (var i = this.previewModeField.values.length - 1; i > 0; i--) {
            this.previewModeField.removeTabOption(this.previewModeField.values[i]);
        }
        for (var i = 0; i < modes.length; i++) {
            this.previewModeField.addTabOption(modes[i].id, modes[i].label);
        }
        if (typeof defaultMode === 'undefined') {
            defaultMode = '0';
        }
        this.previewModeField.insideChange(defaultMode);
    };

    NextendVisualEditorController.prototype.previewModeChanged = function () {
        var mode = this.previewModeField.element.val();

        if (this.currentTabs === false) {
            if (mode == 0) {
                for (var i = 0; i < this.currentVisual.length; i++) {
                    this.tabField.options.eq(i).html('#' + i);
                }
            } else {
                var tabs = this.previewModesList[mode].tabs;
                if (tabs) {
                    for (var i = 0; i < this.currentVisual.length; i++) {
                        this.tabField.options.eq(i).html(tabs[i]);
                    }
                }
            }
        }
        this.currentPreviewMode = mode;
        this._renderPreview();

        this.setPreview(mode);
    };

    NextendVisualEditorController.prototype.setPreview = function (mode) {
    };

    NextendVisualEditorController.prototype.propertyChanged = function (e, property, value) {
        this.isChanged = true;
        this.currentVisual[this.currentTabIndex][property] = value;
        this.renderPreview();
    };

    NextendVisualEditorController.prototype.renderPreview = function () {
        var now = $.now();
        if (this._renderTimeout) {
            clearTimeout(this._renderTimeout);
            if (now - this._delayStart > 100) {
                this._renderPreview();
                this._delayStart = now;
            }
        } else {
            this._delayStart = now;
        }
        this._renderTimeout = setTimeout($.proxy(this._renderPreview, this), 33);
    };

    NextendVisualEditorController.prototype._renderPreview = function () {
        this._renderTimeout = false;
    };

    scope.NextendVisualEditorController = NextendVisualEditorController;

    function NextendVisualEditor() {
        this.fields = {};
        this.$ = $(this);
    };

    NextendVisualEditor.prototype.load = function (values) {
        this._off();
        this._on();
    };

    NextendVisualEditor.prototype._on = function () {
        for (var id in this.fields) {
            this.fields[id].element.on(this.fields[id].events);
        }
    };

    NextendVisualEditor.prototype._off = function () {
        for (var id in this.fields) {
            this.fields[id].element.off('.n2-editor');
        }
    };

    NextendVisualEditor.prototype.trigger = function (property, value) {
        this.$.trigger('change', [property, value]);
    };

    scope.NextendVisualEditor = NextendVisualEditor;

    function NextendVisualRenderer(editorController) {
        this.editorController = editorController;
    }

    NextendVisualRenderer.prototype.deleteRules = function (modeKey, pre, selector) {
        var mode = this.editorController.previewModesList[modeKey],
            rePre = new RegExp('@pre', "g"),
            reSelector = new RegExp('@selector', "g");
        for (var k in mode.selectors) {
            var rule = k
                .replace(rePre, pre)
                .replace(reSelector, selector);
            nextend.css.deleteRule(rule);
        }
    };

    NextendVisualRenderer.prototype.getCSS = function (modeKey, pre, selector, visualTabs, parameters) {
        var css = '',
            mode = this.editorController.previewModesList[modeKey],
            rePre = new RegExp('@pre', "g"),
            reSelector = new RegExp('@selector', "g");

        for (var k in mode.selectors) {
            var rule = k
                .replace(rePre, pre)
                .replace(reSelector, selector);

            css += rule + "{\n" + mode.selectors[k] + "}\n";
            if (typeof parameters.deleteRule !== 'undefined') {
                nextend.css.deleteRule(rule);
            }
        }


        if (modeKey == 0) {
            var visualTab = visualTabs[parameters.activeTab];
            if (parameters.activeTab != 0) {
                visualTab = $.extend({}, visualTabs[0], visualTab);
            }
            css = css.replace(new RegExp('@tab[0-9]*', "g"), this.render(visualTab));
        } else if (mode.renderOptions.combined) {
            for (var i = 0; i < visualTabs.length; i++) {
                css = css.replace(new RegExp('@tab' + i, "g"), this.render(visualTabs[i]));
            }
        } else {
            for (var i = 0; i < visualTabs.length; i++) {
                visualTabs[i] = $.extend({}, visualTabs[i])
                css = css.replace(new RegExp('@tab' + i, "g"), this.render(visualTabs[i]));
            }
        }
        return css;
    };

    NextendVisualRenderer.prototype.render = function (visualData) {
        var visual = this.makeVisualData(visualData);
        var css = '',
            raw = '';
        if (typeof visual.raw !== "undefined") {
            raw = visual.raw;
            delete visual.raw;
        }
        for (var k in visual) {

            css += this.deCase(k) + ": " + visual[k] + ";\n";
        }
        css += raw;
        return css;
    };

    NextendVisualRenderer.prototype.makeVisualData = function (visualData) {
        var visual = {};
        for (var property in visualData) {
            if (visualData.hasOwnProperty(property) && typeof visualData[property] !== 'function') {
                this['makeStyle' + property](visualData[property], visual);
            }
        }
        return visual;
    };

    NextendVisualRenderer.prototype.deCase = function (s) {
        return s.replace(/[A-Z]/g, function (a) {
            return '-' + a.toLowerCase()
        });
    };

    scope.NextendVisualRenderer = NextendVisualRenderer;

})(n2, window);
