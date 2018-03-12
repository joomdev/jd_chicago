;
(function ($, scope) {

    function NextendVisualManagerCore(parameters) {
        this.loadDefaults();

        this.$ = $(this);

        window.nextend[this.type + 'Manager'] = this;

        this.modals = this.initModals();

        this.lightbox = $('#n2-lightbox-' + this.type);

        this.notificationStack = new NextendNotificationCenterStack(this.lightbox.find('.n2-top-bar'));

        this.visualListContainer = this.lightbox.find('.n2-lightbox-sidebar-list');

        this.parameters = parameters;

        this.visuals = {};

        this.controller = this.initController();
        if (this.controller) {
            this.renderer = this.controller.renderer;
        }

        this.firstLoadVisuals(parameters.visuals);

        $('.n2-' + this.type + '-save-as-new')
            .on('click', $.proxy(this.saveAsNew, this));

        this.cancelButton = $('#n2-' + this.type + '-editor-cancel')
            .on('click', $.proxy(this.hide, this));

        this.saveButton = $('#n2-' + this.type + '-editor-save')
            .off('click')
            .on('click', $.proxy(this.setVisual, this));
    };

    NextendVisualManagerCore.prototype.setTitle = function (title) {
        this.lightbox.find('.n2-logo').html(title);
    };

    NextendVisualManagerCore.prototype.loadDefaults = function () {
        this.mode = 'linked';
        this.labels = {
            visual: n2_('visual'),
            visuals: n2_('visuals')
        };
        this.visualLoadDeferreds = {};
        this.showParameters = false;
    }


    NextendVisualManagerCore.prototype.initModals = function () {
        return new NextendVisualManagerModals(this);
    };

    NextendVisualManagerCore.prototype.firstLoadVisuals = function (visuals) {

        for (var k in visuals) {
            this.sets[k].loadVisuals(visuals[k]);
        }
    };

    NextendVisualManagerCore.prototype.initController = function () {

    };

    NextendVisualManagerCore.prototype.getVisual = function (id) {
        if (parseInt(id) > 0) {
            if (typeof this.visuals[id] !== 'undefined') {
                return this.visuals[id];
            } else if (typeof this.visualLoadDeferreds[id] !== 'undefined') {
                return this.visualLoadDeferreds[id];
            } else {
                var deferred = $.Deferred();
                this.visualLoadDeferreds[id] = deferred;
                this._loadVisualFromServer(id)
                    .done($.proxy(function () {
                        deferred.resolve(this.visuals[id]);
                        delete this.visualLoadDeferreds[id];
                    }, this))
                    .fail($.proxy(function () {
                        // This visual is Empty!!!
                        deferred.resolve({
                            id: -1,
                            name: n2_('Empty')
                        });
                        delete this.visualLoadDeferreds[id];
                    }, this));
                return deferred;
            }
        } else {
            try {
                JSON.parse(Base64.decode(id));
                return {
                    id: 0,
                    name: n2_('Static')
                };
            } catch (e) {
                // This visual is Empty!!!
                return {
                    id: -1,
                    name: n2_('Empty')
                };
            }
        }
    };

    NextendVisualManagerCore.prototype._loadVisualFromServer = function (visualId) {
        return NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(this.parameters.ajaxUrl, {
                nextendaction: 'loadVisual'
            }),
            data: {
                visualId: visualId
            },
            dataType: 'json'
        })
            .done($.proxy(function (response) {
                n2c.error('@todo: load the visual data!');
            }, this));
    };

    NextendVisualManagerCore.prototype.show = function (data, saveCallback, showParameters) {

        NextendEsc.add($.proxy(function () {
            this.hide();
            return true;
        }, this));

        this.notificationStack.enableStack();

        this.showParameters = $.extend({
            previewMode: false,
            previewHTML: false
        }, showParameters);

        $('body').css('overflow', 'hidden');
        this.lightbox.css('display', 'block');
        $(window)
            .on('resize.' + this.type + 'Manager', $.proxy(this.resize, this));
        this.resize();

        this.loadDataToController(data);
        this.controller.show();

        this.$.on('save', saveCallback);
    };

    NextendVisualManagerCore.prototype.setAndClose = function (data) {
        this.$.trigger('save', [data]);
    };

    NextendVisualManagerCore.prototype.hide = function (e) {
        this.controller.pause();
        this.notificationStack.popStack();
        if (typeof e !== 'undefined') {
            e.preventDefault();
            NextendEsc.pop();
        }
        this.controller.close();
        this.$.off('save');
        $(window).off('resize.' + this.type + 'Manager');
        $('body').css('overflow', '');
        this.lightbox.css('display', 'none');
    };

    NextendVisualManagerCore.prototype.resize = function () {
        var h = this.lightbox.height();
        var sidebar = this.lightbox.find('.n2-sidebar');
        sidebar.find('.n2-lightbox-sidebar-list').height(h - 1 - sidebar.find('.n2-logo').outerHeight() - sidebar.find('.n2-sidebar-row').outerHeight() - sidebar.find('.n2-save-as-new-container').parent().height());

        var contentArea = this.lightbox.find('.n2-content-area');
        contentArea.height(h - 1 - contentArea.siblings('.n2-top-bar, .n2-table').outerHeight());
    };

    NextendVisualManagerCore.prototype.loadDataToController = function (data) {
        if (this.isVisualData(data)) {
            $.when(this.getVisual(data)).done($.proxy(function (visual) {
                if (visual.id > 0) {
                    visual.activate();
                } else {
                    console.error(data + ' visual is not found linked');
                }
            }, this));
        } else {
            console.error(data + ' visual not found');
        }
    };

    NextendVisualManagerCore.prototype.isVisualData = function (data) {
        return parseInt(data) > 0;
    };

    NextendVisualManagerCore.prototype.setVisual = function (e) {
        e.preventDefault();
        switch (this.mode) {
            case 0:
                break;
            case 'static':
                this.modals.getLinkedOverwriteOrSaveAs()
                    .show('saveAsNew');
                break;
            case 'linked':
            default:
                if (this.activeVisual) {
                    if (this.activeVisual.compare(this.controller.get('set'))) {
                        //if (this.getBase64(this.activeVisual.name) == this.activeVisual.base64) {
                        this.setAndClose(this.activeVisual.id);
                        this.hide(e);
                    } else {

                        if (this.activeVisual && !this.activeVisual.isEditable()) {
                            this.modals.getLinkedOverwriteOrSaveAs()
                                .show('saveAsNew');
                        } else {
                            this.modals.getLinkedOverwriteOrSaveAs()
                                .show();
                        }
                    }
                } else {
                    this.modals.getLinkedOverwriteOrSaveAs()
                        .show('saveAsNew');
                }
                break;
        }
    };

    NextendVisualManagerCore.prototype.saveAsNew = function (e) {
        e.preventDefault();

        this.modals.getSaveAs()
            .show();
    };

    NextendVisualManagerCore.prototype._saveAsNew = function (name) {
        return NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(this.parameters.ajaxUrl, {
                nextendaction: 'addVisual'
            }),
            data: {
                setId: this.setsSelector.val(),
                value: Base64.encode(JSON.stringify({
                    name: name,
                    data: this.controller.get('saveAsNew')
                }))
            },
            dataType: 'json'
        })
            .done($.proxy(function (response) {
                var visual = response.data.visual;
                this.changeActiveVisual(this.sets[visual.referencekey].addVisual(visual));
            }, this));
    };

    NextendVisualManagerCore.prototype.saveActiveVisual = function (name) {

        return NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(this.parameters.ajaxUrl, {
                nextendaction: 'changeVisual'
            }),
            data: {
                visualId: this.activeVisual.id,
                value: this.getBase64(name)
            },
            dataType: 'json'
        }).done($.proxy(function (response) {
            this.activeVisual.setValue(response.data.visual.value, true);
        }, this));
    };

    NextendVisualManagerCore.prototype.changeActiveVisual = function (visual) {
        if (this.activeVisual) {
            this.activeVisual.notActive();
            this.activeVisual = false;
        }
        if (visual /*&& (this.mode == 0 || this.mode == 'linked')*/) {
            if (this.mode == 'static') {
                this.setMode('linked');
            }
            visual.active();
            this.activeVisual = visual;
        }
    };

    NextendVisualManagerCore.prototype.getBase64 = function (name) {

        return Base64.encode(JSON.stringify({
            name: name,
            data: this.controller.get('set')
        }));
    };

    NextendVisualManagerCore.prototype.removeRules = function (mode, visual) {
        this.renderer.deleteRules(mode, this.parameters.renderer.pre, '.' + this.getClass(visual.id, mode));
    };

    scope.NextendVisualManagerCore = NextendVisualManagerCore;

    /**
     * Sets are visible
     */
    function NextendVisualManagerVisibleSets() {
        NextendVisualManagerCore.prototype.constructor.apply(this, arguments);
    }

    NextendVisualManagerVisibleSets.prototype = Object.create(NextendVisualManagerCore.prototype);
    NextendVisualManagerVisibleSets.prototype.constructor = NextendVisualManagerVisibleSets;

    NextendVisualManagerVisibleSets.prototype.firstLoadVisuals = function (visuals) {
        this.sets = {};
        this.setsByReference = {};

        this.setsSelector = $('#' + this.parameters.setsIdentifier + 'sets_select');
        for (var i = 0; i < this.parameters.sets.length; i++) {
            this.newVisualSet(this.parameters.sets[i]);
        }
        this.initSetsManager();

        for (var k in visuals) {
            this.sets[k].loadVisuals(visuals[k])
        }

        this.activeSet = this.sets[this.setsSelector.val()];
        this.activeSet.active();

        this.setsSelector.on('change', $.proxy(function () {
            this.activeSet.notActive();
            this.activeSet = this.sets[this.setsSelector.val()];
            this.activeSet.active();
        }, this));
    };


    NextendVisualManagerVisibleSets.prototype.initSetsManager = function () {
        new NextendVisualSetsManager(this);
    };

    NextendVisualManagerVisibleSets.prototype._loadVisualFromServer = function (visualId) {
        return NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(this.parameters.ajaxUrl, {
                nextendaction: 'loadSetByVisualId'
            }),
            data: {
                visualId: visualId
            },
            dataType: 'json'
        })
            .done($.proxy(function (response) {
                this.sets[response.data.set.setId].loadVisuals(response.data.set.visuals);

            }, this));
    };

    NextendVisualManagerVisibleSets.prototype.changeSet = function (setId) {
        if (this.setsSelector.val() != setId) {
            this.setsSelector.val(setId)
                .trigger('change');
        }
    };

    NextendVisualManagerVisibleSets.prototype.changeSetById = function (id) {
        if (typeof this.sets[id] !== 'undefined') {
            this.changeSet(id);
        }
    };

    NextendVisualManagerVisibleSets.prototype.newVisualSet = function (set) {
        return new NextendVisualSet(set, this);
    };

    scope.NextendVisualManagerVisibleSets = NextendVisualManagerVisibleSets;

    /**
     * Sets are editable
     * Ex.: Layout
     */
    function NextendVisualManagerEditableSets() {
        NextendVisualManagerVisibleSets.prototype.constructor.apply(this, arguments);
    }

    NextendVisualManagerEditableSets.prototype = Object.create(NextendVisualManagerVisibleSets.prototype);
    NextendVisualManagerEditableSets.prototype.constructor = NextendVisualManagerEditableSets;

    NextendVisualManagerEditableSets.prototype.initSetsManager = function () {
        new NextendVisualSetsManagerEditable(this);
    };

    scope.NextendVisualManagerEditableSets = NextendVisualManagerEditableSets;

    /**
     * Static and linked mode
     * Ex.: Style, Fonts, Animation
     */

    function NextendVisualManagerSetsAndMore() {
        NextendVisualManagerEditableSets.prototype.constructor.apply(this, arguments);

        this.linkedButton = $('#n2-' + this.type + '-editor-set-as-linked');
        this.setMode(0);
    }

    NextendVisualManagerSetsAndMore.prototype = Object.create(NextendVisualManagerEditableSets.prototype);
    NextendVisualManagerSetsAndMore.prototype.constructor = NextendVisualManagerSetsAndMore;


    NextendVisualManagerSetsAndMore.prototype.setMode = function (newMode) {
        if (newMode == 'static') {
            this.changeActiveVisual(null);
        }
        if (this.mode != newMode) {
            switch (newMode) {
                case 0:
                    //this.modeRadio.parent.css('display', 'none');
                    this.cancelButton.css('display', 'none');
                    this.saveButton
                        .off('click');
                    break;

                case 'static':
                default:
                    this.cancelButton.css('display', 'inline-block');
                    this.saveButton
                        .off('click')
                        .on('click', $.proxy(this.setVisualAsStatic, this));
                    this.linkedButton
                        .off('click')
                        .on('click', $.proxy(this.setVisualAsLinked, this));
                    break;
            }
            this.mode = newMode;
        }
    };

    NextendVisualManagerSetsAndMore.prototype.loadDataToController = function (data) {
        if (parseInt(data) > 0) {
            $.when(this.getVisual(data)).done($.proxy(function (visual) {
                if (visual.id > 0) {
                    this.setMode('linked');
                    visual.activate();
                } else {
                    this.setMode('static');
                    this.controller.load('', false, this.showParameters);
                }
            }, this));
        } else {
            var visualData = '';
            this.setMode('static');
            try {
                visualData = this.getStaticData(data);
            } catch (e) {
                // This visual is Empty!!!
            }
            this.controller.load(visualData, false, this.showParameters);
        }
    };

    NextendVisualManagerSetsAndMore.prototype.getStaticData = function (data) {
        var d = JSON.parse(Base64.decode(data)).data;
        if (typeof d === 'undefined') {
            return '';
        }
        return d;
    };

    NextendVisualManagerSetsAndMore.prototype.setVisualAsLinked = function (e) {
        this.setVisual(e);
    };

    NextendVisualManagerSetsAndMore.prototype.setVisualAsStatic = function (e) {
        e.preventDefault();
        this.setAndClose(this.getBase64(n2_('Static')));
        this.hide(e);
    };

    scope.NextendVisualManagerSetsAndMore = NextendVisualManagerSetsAndMore;


    /**
     * Multiple selection
     * Ex.: Background animation, Post background animation
     */

    function NextendVisualManagerMultipleSelection(parameters) {

        window.nextend[this.type + 'Manager'] = this;

        // Push the constructor to the first show as an optimization.
        this._lateInit = $.proxy(function (parameters) {
            NextendVisualManagerVisibleSets.prototype.constructor.call(this, parameters);
        }, this, parameters);

    }

    NextendVisualManagerMultipleSelection.prototype = Object.create(NextendVisualManagerVisibleSets.prototype);
    NextendVisualManagerMultipleSelection.prototype.constructor = NextendVisualManagerMultipleSelection;


    NextendVisualManagerMultipleSelection.prototype.lateInit = function () {
        if (!this.inited) {
            this.inited = true;

            this._lateInit();
        }
    };

    NextendVisualManagerMultipleSelection.prototype.show = function (data, saveCallback, controllerParameters) {

        this.lateInit();

        this.notificationStack.enableStack();

        NextendEsc.add($.proxy(function () {
            this.hide();
            return true;
        }, this));

        $('body').css('overflow', 'hidden');
        this.lightbox.css('display', 'block');
        $(window)
            .on('resize.' + this.type + 'Manager', $.proxy(this.resize, this));
        this.resize();

        var i = 0;
        if (data != '') {
            var selected = data.split('||'),
                hasSelected = false;
            for (; i < selected.length; i++) {
                $.when(this.getVisual(selected[i])).done(function (visual) {
                    if (visual && visual.check) {
                        visual.check();
                        if (!hasSelected) {
                            hasSelected = true;
                            visual.activate();
                        }
                    }
                });
            }
        }

        this.$.on('save', saveCallback);

        this.controller.start(controllerParameters);

        if (i == 0) {
            $.when(this.activeSet._loadVisuals())
                .done($.proxy(function () {
                    for (var k in this.activeSet.visuals) {
                        this.activeSet.visuals[k].activate();
                        break;
                    }
                }, this));
        }
    };

    NextendVisualManagerMultipleSelection.prototype.setVisual = function (e) {
        e.preventDefault();
        this.setAndClose(this.getAsString());
        this.hide(e);
    };

    NextendVisualManagerMultipleSelection.prototype.getAsString = function () {
        var selected = [];
        for (var k in this.sets) {
            var set = this.sets[k];
            for (var i in set.visuals) {
                if (set.visuals[i].checked) {
                    selected.push(set.visuals[i].id);
                }
            }
        }
        if (selected.length == 0 && this.activeVisual) {
            selected.push(this.activeVisual.id);
        }
        return selected.join('||');
    };

    NextendVisualManagerMultipleSelection.prototype.hide = function (e) {
        NextendVisualManagerVisibleSets.prototype.hide.apply(this, arguments);

        for (var k in this.sets) {
            var set = this.sets[k];
            for (var i in set.visuals) {
                set.visuals[i].unCheck();
            }
        }
    };

    scope.NextendVisualManagerMultipleSelection = NextendVisualManagerMultipleSelection;


    function NextendVisualCore(visual, visualManager) {
        this.id = visual.id;
        this.visualManager = visualManager;
        this.setValue(visual.value, false);
        this.visual = visual;
        this.visualManager.visuals[this.id] = this;
    };

    NextendVisualCore.prototype.compare = function (value) {

        var length = Math.max(this.value.length, value.length);
        for (var i = 0; i < length; i++) {
            if (!this._compareTab(typeof this.value[i] === 'undefined' ? {} : this.value[i], typeof value[i] === 'undefined' ? {} : value[i])) {
                return false;
            }
        }
        return true;
    };

    NextendVisualCore.prototype._compareTab = function (a, b) {
        var aProps = Object.getOwnPropertyNames(a);
        var bProps = Object.getOwnPropertyNames(b);
        if (a.length === 0 && bProps.length === 0) {
            return true;
        }

        if (aProps.length != bProps.length) {
            return false;
        }

        for (var i = 0; i < aProps.length; i++) {
            var propName = aProps[i];

            // If values of same property are not equal,
            // objects are not equivalent
            if (a[propName] !== b[propName]) {
                return false;
            }
        }

        return true;
    };

    NextendVisualCore.prototype.setValue = function (value, render) {
        var data = null;
        if (typeof value == 'string') {
            this.base64 = value;
            data = JSON.parse(Base64.decode(value));
        } else {
            data = value;
        }
        this.name = data.name;
        this.value = data.data;

        if (render) {
            this.render();
        }
    };

    NextendVisualCore.prototype.isSystem = function () {
        return (this.visual.system == 1);
    };

    NextendVisualCore.prototype.isEditable = function () {
        return (this.visual.editable == 1);
    };

    NextendVisualCore.prototype.activate = function (e) {
        if (typeof e !== 'undefined') {
            e.preventDefault();
        }
        this.visualManager.changeActiveVisual(this);
        this.visualManager.controller.load(this.value, false, this.visualManager.showParameters);
    };

    NextendVisualCore.prototype.active = function () {
    };

    NextendVisualCore.prototype.notActive = function () {
    };

    NextendVisualCore.prototype.delete = function (e) {
        if (e) {
            e.preventDefault();
        }
        NextendDeleteModal('n2-visual', this.name, $.proxy(function () {
            this._delete();
        }, this));
    };
    NextendVisualCore.prototype._delete = function () {

        return NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(this.visualManager.parameters.ajaxUrl, {
                nextendaction: 'deleteVisual'
            }),
            data: {
                visualId: this.id
            },
            dataType: 'json'
        })
            .done($.proxy(function (response) {
                var visual = response.data.visual;

                if (this.visualManager.activeVisual && this.id == this.visualManager.activeVisual.id) {
                    this.visualManager.changeActiveVisual(null);
                }
                this.removeRules();
                delete this.visualManager.visuals[this.id];
                delete this.set.visuals[this.id];
                this.row.remove();
                this.visualManager.$.trigger('visualDelete', [this.id]);
            }, this));
    };

    NextendVisualCore.prototype.removeRules = function () {

    };

    NextendVisualCore.prototype.render = function () {

    };

    NextendVisualCore.prototype.isUsed = function () {
        return false;
    };

    scope.NextendVisualCore = NextendVisualCore;

    function NextendVisualWithSet(visual, set, visualManager) {
        this.set = set;
        NextendVisualCore.prototype.constructor.call(this, visual, visualManager);
    };

    NextendVisualWithSet.prototype = Object.create(NextendVisualCore.prototype);
    NextendVisualWithSet.prototype.constructor = NextendVisualWithSet;

    NextendVisualWithSet.prototype.active = function () {
        var setId = this.set.set.id;
        this.visualManager.changeSet(setId);

        NextendVisualCore.prototype.active.call(this);
    };

    scope.NextendVisualWithSet = NextendVisualWithSet;


    function NextendVisualWithSetRow() {
        NextendVisualWithSet.prototype.constructor.apply(this, arguments);
    };

    NextendVisualWithSetRow.prototype = Object.create(NextendVisualWithSet.prototype);
    NextendVisualWithSetRow.prototype.constructor = NextendVisualWithSetRow;


    NextendVisualWithSetRow.prototype.createRow = function () {
        this.row = $('<li></li>')
            .append($('<a href="#">' + this.name + '</a>')
                .on('click', $.proxy(this.activate, this)));
        if (!this.isSystem()) {
            this.row.append($('<span class="n2-actions"></span>')
                .append($('<a href="#"><i class="n2-i n2-i-delete n2-i-grey-opacity"></i></a>')
                    .on('click', $.proxy(this.delete, this))));
        }
        return this.row;
    };

    NextendVisualWithSetRow.prototype.setValue = function (value, render) {
        NextendVisualWithSet.prototype.setValue.call(this, value, render);

        if (this.row) {
            this.row.find('> a').html(this.name);
        }
    };

    NextendVisualWithSetRow.prototype.active = function () {
        this.row.addClass('n2-active');
        NextendVisualWithSet.prototype.active.call(this);
    };

    NextendVisualWithSetRow.prototype.notActive = function () {
        this.row.removeClass('n2-active');
        NextendVisualWithSet.prototype.notActive.call(this);
    };

    scope.NextendVisualWithSetRow = NextendVisualWithSetRow;


    function NextendVisualWithSetRowMultipleSelection(visual, set, visualManager) {
        this.checked = false;
        visual.system = 1;
        visual.editable = 0;
        NextendVisualWithSetRow.prototype.constructor.apply(this, arguments);
    };

    NextendVisualWithSetRowMultipleSelection.prototype = Object.create(NextendVisualWithSetRow.prototype);
    NextendVisualWithSetRowMultipleSelection.prototype.constructor = NextendVisualWithSetRowMultipleSelection;


    NextendVisualWithSetRowMultipleSelection.prototype.createRow = function () {
        var row = NextendVisualWithSetRow.prototype.createRow.call(this);
        this.checkbox = $('<div class="n2-list-checkbox"><i class="n2-i n2-i-tick"></i></div>')
            .on('click', $.proxy(this.checkOrUnCheck, this))
            .prependTo(row.find('a'));

        return row;
    };

    NextendVisualWithSetRowMultipleSelection.prototype.setValue = function (data, render) {
        this.name = data.name;
        this.value = data.data;
        if (this.row) {
            this.row.find('> a').html(this.name);
        }

        if (render) {
            this.render();
        }
    };

    NextendVisualWithSetRowMultipleSelection.prototype.activate = function (e) {
        if (typeof e !== 'undefined') {
            e.preventDefault();
        }
        this.visualManager.changeActiveVisual(this);
        this.visualManager.controller.setAnimationProperties(this.value);
    };

    NextendVisualWithSetRowMultipleSelection.prototype.checkOrUnCheck = function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (this.checked) {
            this.unCheck();
        } else {
            this.check();
        }
    };

    NextendVisualWithSetRowMultipleSelection.prototype.check = function () {
        this.checked = true;
        this.checkbox.addClass('n2-active');
        this.activate();
    };

    NextendVisualWithSetRowMultipleSelection.prototype.unCheck = function () {
        this.checked = false;
        this.checkbox.removeClass('n2-active');
        this.activate();
    };

    scope.NextendVisualWithSetRowMultipleSelection = NextendVisualWithSetRowMultipleSelection;

})(n2, window);
