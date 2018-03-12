;
(function ($, scope) {

    function NextendFontManager() {
        NextendVisualManagerSetsAndMore.prototype.constructor.apply(this, arguments);
        this.setFontSize(16);
    };

    NextendFontManager.prototype = Object.create(NextendVisualManagerSetsAndMore.prototype);
    NextendFontManager.prototype.constructor = NextendFontManager;

    NextendFontManager.prototype.loadDefaults = function () {
        NextendVisualManagerSetsAndMore.prototype.loadDefaults.apply(this, arguments);
        this.type = 'font';
        this.labels = {
            visual: n2_('font'),
            visuals: n2_('fonts')
        };

        this.styleClassName = '';
        this.styleClassName2 = '';
    };

    NextendFontManager.prototype.initController = function () {
        return new NextendFontEditorController(this.parameters.renderer.modes, this.parameters.defaultFamily);
    };

    NextendFontManager.prototype.addVisualUsage = function (mode, fontValue, pre) {
        /**
         * if fontValue is numeric, then it is a linked font!
         */
        if (parseInt(fontValue) > 0) {
            return this._addLinkedFont(mode, fontValue, pre);
        } else {
            try {
                this._renderStaticFont(mode, fontValue, pre);
                return true;
            } catch (e) {
                // Empty font
                return false;
            }
        }
    };

    NextendFontManager.prototype._addLinkedFont = function (mode, fontId, pre) {
        var used = this.parameters.renderer.usedFonts,
            d = $.Deferred();
        $.when(this.getVisual(fontId))
            .done($.proxy(function (font) {
                if (font.id > 0) {
                    if (typeof pre === 'undefined') {
                        if (typeof used[font.id] === 'undefined') {
                            used[font.id] = [mode];
                            this.renderLinkedFont(mode, font, pre);
                        } else if ($.inArray(mode, used[font.id]) == -1) {
                            used[font.id].push(mode);
                            this.renderLinkedFont(mode, font, pre);
                        }
                    } else {
                        this.renderLinkedFont(mode, font, pre);
                    }
                    d.resolve(true);
                } else {
                    d.resolve(false);
                }
            }, this))
            .fail(function () {
                d.resolve(false);
            });
        return d;
    };

    NextendFontManager.prototype.renderLinkedFont = function (mode, font, pre) {
        if (typeof pre === 'undefined') {
            pre = this.parameters.renderer.pre;
        }
        nextend.css.add(this.renderer.getCSS(mode, pre, '.' + this.getClass(font.id, mode), font.value, {
            deleteRule: true
        }));

    };

    NextendFontManager.prototype._renderStaticFont = function (mode, font, pre) {
        if (typeof pre === 'undefined') {
            pre = this.parameters.renderer.pre;
        }
        nextend.css.add(this.renderer.getCSS(mode, pre, '.' + this.getClass(font, mode), JSON.parse(Base64.decode(font)).data, {}));
    };

    /**
     * We should never use this method as we do not track if a font used with the same mode multiple times.
     * So there is no sync and if we delete a used font, other usages might fail to update correctly in
     * special circumstances.
     * @param mode
     * @param fontId
     */
    NextendFontManager.prototype.removeUsedFont = function (mode, fontId) {
        var used = this.parameters.renderer.usedFonts;
        if (typeof used[fontId] !== 'undefined') {
            var index = $.inArray(mode, used[fontId]);
            if (index > -1) {
                used[fontId].splice(index, 1);
            }
        }
    };

    NextendFontManager.prototype.getClass = function (font, mode) {
        if (parseInt(font) > 0) {
            return 'n2-font-' + font + '-' + mode;
        } else if (font == '') {
            // Empty font
            return '';
        }
        // Font might by empty with this class too, but we do not care as nothing wrong if it has an extra class
        // We could do try catch to JSON.parse(Base64.decode(font)), but it is wasting resource
        return 'n2-font-' + md5(font) + '-' + mode;
    };

    NextendFontManager.prototype.createVisual = function (visual, set) {
        return new NextendFont(visual, set, this);
    };

    NextendFontManager.prototype.setConnectedStyle = function (styleId) {
        this.styleClassName = $('#' + styleId).data('field').renderStyle();
    };

    NextendFontManager.prototype.setConnectedStyle2 = function (styleId) {
        this.styleClassName2 = $('#' + styleId).data('field').renderStyle();
    };

    NextendFontManager.prototype.setFontSize = function (fontSize) {
        this.controller.setFontSize(fontSize)
    };

    scope.NextendFontManager = NextendFontManager;


    function NextendFont() {
        NextendVisualWithSetRow.prototype.constructor.apply(this, arguments);
    };

    NextendFont.prototype = Object.create(NextendVisualWithSetRow.prototype);
    NextendFont.prototype.constructor = NextendFont;

    NextendFont.prototype.removeRules = function () {
        var used = this.isUsed();
        if (used) {
            for (var i = 0; i < used.length; i++) {
                this.visualManager.removeRules(used[i], this);
            }
        }
    };

    NextendFont.prototype.render = function () {
        var used = this.isUsed();
        if (used) {
            for (var i = 0; i < used.length; i++) {
                this.visualManager.renderLinkedFont(used[i], this);
            }
        }
    };

    NextendFont.prototype.isUsed = function () {
        if (typeof this.visualManager.parameters.renderer.usedFonts[this.id] !== 'undefined') {
            return this.visualManager.parameters.renderer.usedFonts[this.id];
        }
        return false;
    };

    scope.NextendFont = NextendFont;

})(n2, window);
