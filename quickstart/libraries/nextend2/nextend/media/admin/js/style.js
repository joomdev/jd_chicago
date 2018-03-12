;
(function ($, scope) {

    function NextendStyleManager() {
        NextendVisualManagerSetsAndMore.prototype.constructor.apply(this, arguments);
        this.setFontSize(14);
    };

    NextendStyleManager.prototype = Object.create(NextendVisualManagerSetsAndMore.prototype);
    NextendStyleManager.prototype.constructor = NextendStyleManager;

    NextendStyleManager.prototype.loadDefaults = function () {
        NextendVisualManagerSetsAndMore.prototype.loadDefaults.apply(this, arguments);
        this.type = 'style';
        this.labels = {
            visual: n2_('style'),
            visuals: n2_('styles')
        };

        this.styleClassName2 = '';
        this.fontClassName = '';
        this.fontClassName2 = '';
    };


    NextendStyleManager.prototype.initController = function () {
        return new NextendStyleEditorController(this.parameters.renderer.modes);
    };

    NextendStyleManager.prototype.addVisualUsage = function (mode, styleValue, pre) {
        /**
         * if styleValue is numeric, then it is a linked style!
         */
        if (parseInt(styleValue) > 0) {
            return this._addLinkedStyle(mode, styleValue, pre);
        } else {
            try {
                this._renderStaticStyle(mode, styleValue, pre);
                return true;
            } catch (e) {
                return false;
            }
        }
    };

    NextendStyleManager.prototype._addLinkedStyle = function (mode, styleId, pre) {
        var used = this.parameters.renderer.usedStyles,
            d = $.Deferred();
        $.when(this.getVisual(styleId))
            .done($.proxy(function (style) {
                if (style.id > 0) {
                    if (typeof pre === 'undefined') {
                        if (typeof used[style.id] === 'undefined') {
                            used[style.id] = [mode];
                            this.renderLinkedStyle(mode, style, pre);
                        } else if ($.inArray(mode, used[style.id]) == -1) {
                            used[style.id].push(mode);
                            this.renderLinkedStyle(mode, style, pre);
                        }
                    } else {
                        this.renderLinkedStyle(mode, style, pre);
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

    NextendStyleManager.prototype.renderLinkedStyle = function (mode, style, pre) {
        if (typeof pre === 'undefined') {
            pre = this.parameters.renderer.pre;
        }
        nextend.css.add(this.renderer.getCSS(mode, pre, '.' + this.getClass(style.id, mode), style.value, {
            deleteRule: true
        }));

    };

    NextendStyleManager.prototype._renderStaticStyle = function (mode, style, pre) {
        if (typeof pre === 'undefined') {
            pre = this.parameters.renderer.pre;
        }
        nextend.css.add(this.renderer.getCSS(mode, pre, '.' + this.getClass(style, mode), JSON.parse(Base64.decode(style)).data, {}));
    };

    /**
     * We should never use this method as we do not track if a style used with the same mode multiple times.
     * So there is no sync and if we delete a used style, other usages might fail to update correctly in
     * special circumstances.
     * @param mode
     * @param styleId
     */
    NextendStyleManager.prototype.removeUsedStyle = function (mode, styleId) {
        var used = this.parameters.renderer.usedStyles;
        if (typeof used[styleId] !== 'undefined') {
            var index = $.inArray(mode, used[styleId]);
            if (index > -1) {
                used[styleId].splice(index, 1);
            }
        }
    };

    NextendStyleManager.prototype.getClass = function (style, mode) {
        if (parseInt(style) > 0) {
            return 'n2-style-' + style + '-' + mode;
        } else if (style == '') {
            return '';
        }
        // style might by empty with this class too, but we do not care as nothing wrong if it has an extra class
        // We could do try catch to JSON.parse(Base64.decode(style)), but it is wasting resource
        return 'n2-style-' + md5(style) + '-' + mode;
    };

    NextendStyleManager.prototype.createVisual = function (visual, set) {
        return new NextendStyle(visual, set, this);
    };

    NextendStyleManager.prototype.setConnectedStyle = function (styleId) {
        this.styleClassName2 = $('#' + styleId).data('field').renderStyle();
    };

    NextendStyleManager.prototype.setConnectedFont = function (fontId) {
        this.fontClassName = $('#' + fontId).data('field').renderFont();
    };

    NextendStyleManager.prototype.setConnectedFont2 = function (fontId) {
        this.fontClassName2 = $('#' + fontId).data('field').renderFont();
    };

    NextendStyleManager.prototype.setFontSize = function (fontSize) {
        this.controller.setFontSize(fontSize)
    };

    scope.NextendStyleManager = NextendStyleManager;

    function NextendStyle() {
        NextendVisualWithSetRow.prototype.constructor.apply(this, arguments);
    };

    NextendStyle.prototype = Object.create(NextendVisualWithSetRow.prototype);
    NextendStyle.prototype.constructor = NextendStyle;


    NextendStyle.prototype.removeRules = function () {
        var used = this.isUsed();
        if (used) {
            for (var i = 0; i < used.length; i++) {
                this.visualManager.removeRules(used[i], this);
            }
        }
    };

    NextendStyle.prototype.render = function () {
        var used = this.isUsed();
        if (used) {
            for (var i = 0; i < used.length; i++) {
                this.visualManager.renderLinkedStyle(used[i], this);
            }
        }
    };

    NextendStyle.prototype.isUsed = function () {
        if (typeof this.visualManager.parameters.renderer.usedStyles[this.id] !== 'undefined') {
            return this.visualManager.parameters.renderer.usedStyles[this.id];
        }
        return false;
    };

    scope.NextendStyle = NextendStyle;

})(n2, window);
