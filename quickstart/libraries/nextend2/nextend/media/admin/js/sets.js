;
(function ($, scope) {

    function NextendVisualSetsManager(visualManager) {
        this.visualManager = visualManager;
        this.$ = $(this);
    }

    scope.NextendVisualSetsManager = NextendVisualSetsManager;

    function NextendVisualSetsManagerEditable(visualManager) {
        this.modal = null;
        NextendVisualSetsManager.prototype.constructor.apply(this, arguments);

        this.$.on({
            setAdded: function (e, set) {
                new NextendVisualSet(set, visualManager);
            },
            setChanged: function (e, set) {
                visualManager.sets[set.id].rename(set.value);
            },
            setDeleted: function (e, set) {
                visualManager.sets[set.id].delete();
                visualManager.setsSelector.trigger('change');
            }
        });

        this.manageButton = $('#' + visualManager.parameters.setsIdentifier + '-manage')
            .on('click', $.proxy(this.showManageSets, this));

    };

    NextendVisualSetsManagerEditable.prototype = Object.create(NextendVisualSetsManager.prototype);
    NextendVisualSetsManagerEditable.prototype.constructor = NextendVisualSetsManagerEditable;

    NextendVisualSetsManagerEditable.prototype.isSetAllowedToEdit = function (id) {
        if (id == -1 || typeof this.visualManager.sets[id] == 'undefined' || this.visualManager.sets[id].set.editable == 0) {
            return false;
        }
        return true;
    };


    NextendVisualSetsManagerEditable.prototype.createVisualSet = function (name) {
        return NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(this.visualManager.parameters.ajaxUrl, {
                nextendaction: 'createSet'
            }),
            data: {
                name: name
            },
            dataType: 'json'
        })
            .done($.proxy(function (response) {
                this.$.trigger('setAdded', response.data.set)
            }, this));
    };

    NextendVisualSetsManagerEditable.prototype.renameVisualSet = function (id, name) {
        return NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(this.visualManager.parameters.ajaxUrl, {
                nextendaction: 'renameSet'
            }),
            data: {
                setId: id,
                name: name
            },
            dataType: 'json'
        })
            .done($.proxy(function (response) {
                this.$.trigger('setChanged', response.data.set);
                nextend.notificationCenter.success(n2_('Set renamed'));
            }, this));
    };

    NextendVisualSetsManagerEditable.prototype.deleteVisualSet = function (id) {

        var d = $.Deferred(),
            set = this.visualManager.sets[id],
            deferreds = [];

        $.when(set._loadVisuals())
            .done($.proxy(function () {
                for (var k in set.visuals) {
                    deferreds.push(set.visuals[k]._delete());
                }

                $.when.apply($, deferreds).then($.proxy(function () {
                    NextendAjaxHelper.ajax({
                        type: "POST",
                        url: NextendAjaxHelper.makeAjaxUrl(this.visualManager.parameters.ajaxUrl, {
                            nextendaction: 'deleteSet'
                        }),
                        data: {
                            setId: id
                        },
                        dataType: 'json'
                    })
                        .done($.proxy(function (response) {
                            d.resolve();
                            this.$.trigger('setDeleted', response.data.set);
                        }, this));
                }, this));
            }, this))
            .fail(function () {
                d.reject();
            });
        return d
            .fail(function () {
                nextend.notificationCenter.error(n2_('Unable to delete the set'));
            });
    };

    NextendVisualSetsManagerEditable.prototype.showManageSets = function () {
        var visualManager = this.visualManager,
            setsManager = this;
        if (this.modal === null) {
            this.modal = new NextendModal({
                zero: {
                    size: [
                        500,
                        390
                    ],
                    title: n2_('Sets'),
                    back: false,
                    close: true,
                    content: '',
                    controls: ['<a href="#" class="n2-add-new n2-button n2-button-big n2-button-green n2-uc n2-h4">' + n2_('Add new') + '</a>'],
                    fn: {
                        show: function () {
                            this.title.html(n2_printf(n2_('%s sets'), visualManager.labels.visual));

                            this.createHeading(n2_('Sets')).appendTo(this.content);
                            var data = [];
                            for (var k in visualManager.sets) {
                                var id = visualManager.sets[k].set.id;
                                if (setsManager.isSetAllowedToEdit(id)) {
                                    data.push([visualManager.sets[k].set.value, $('<div class="n2-button n2-button-grey n2-button-x-small n2-uc n2-h5">' + n2_('Rename') + '</div>')
                                        .on('click', {id: id}, $.proxy(function (e) {
                                            this.loadPane('rename', false, false, [e.data.id]);
                                        }, this)), $('<div class="n2-button n2-button-red n2-button-x-small n2-uc n2-h5">' + n2_('Delete') + '</div>')
                                        .on('click', {id: id}, $.proxy(function (e) {
                                            this.loadPane('delete', false, false, [e.data.id]);
                                        }, this))]);
                                } else {
                                    data.push([visualManager.sets[k].set.value, '', '']);
                                }
                            }
                            this.createTable(data, ['width:100%;', '', '']).appendTo(this.createTableWrap().appendTo(this.content));

                            this.controls.find('.n2-add-new')
                                .on('click', $.proxy(function (e) {
                                    e.preventDefault();
                                    this.loadPane('addNew');
                                }, this));
                        }
                    }
                },
                addNew: {
                    title: n2_('Create set'),
                    size: [
                        500,
                        220
                    ],
                    back: 'zero',
                    close: true,
                    content: '<form class="n2-form"></form>',
                    controls: ['<a href="#" class="n2-button n2-button-big n2-button-green n2-uc n2-h4">' + n2_('Add') + '</a>'],
                    fn: {
                        show: function () {

                            var button = this.controls.find('.n2-button'),
                                form = this.content.find('.n2-form').on('submit', function (e) {
                                    e.preventDefault();
                                    button.trigger('click');
                                }).append(this.createInput(n2_('Name'), 'n2-visual-name', 'width: 446px;')),
                                nameField = this.content.find('#n2-visual-name').focus();

                            button.on('click', $.proxy(function (e) {
                                var name = nameField.val();
                                if (name == '') {
                                    nextend.notificationCenter.error(n2_('Please fill the name field!'));
                                } else {
                                    setsManager.createVisualSet(name)
                                        .done($.proxy(function (response) {
                                            this.hide(e);
                                            nextend.notificationCenter.success(n2_('Set added'));
                                            visualManager.setsSelector.val(response.data.set.id).trigger('change')
                                        }, this));
                                }
                            }, this));
                        }
                    }
                },
                rename: {
                    title: n2_('Rename set'),
                    size: [
                        500,
                        220
                    ],
                    back: 'zero',
                    close: true,
                    content: '<form class="n2-form"></form>',
                    controls: ['<a href="#" class="n2-button n2-button-big n2-button-green n2-uc n2-h4">' + n2_('Rename') + '</a>'],
                    fn: {
                        show: function (id) {

                            var button = this.controls.find('.n2-button'),
                                form = this.content.find('.n2-form').on('submit', function (e) {
                                    e.preventDefault();
                                    button.trigger('click');
                                }).append(this.createInput(n2_('Name'), 'n2-visual-name', 'width: 446px;')),
                                nameField = this.content.find('#n2-visual-name')
                                    .val(visualManager.sets[id].set.value).focus();

                            button.on('click', $.proxy(function () {
                                var name = nameField.val();
                                if (name == '') {
                                    nextend.notificationCenter.error(n2_('Please fill the name field!'));
                                } else {
                                    setsManager.renameVisualSet(id, name)
                                        .done($.proxy(this.goBack, this));
                                }
                            }, this));
                        }
                    }
                },
                'delete': {
                    title: n2_('Delete set'),
                    size: [
                        500,
                        190
                    ],
                    back: 'zero',
                    close: true,
                    content: '',
                    controls: ['<a href="#" class="n2-button n2-button-big n2-button-grey n2-uc n2-h4">' + n2_('Cancel') + '</a>', '<a href="#" class="n2-button n2-button-big n2-button-red n2-uc n2-h4">' + n2_('Yes') + '</a>'],
                    fn: {
                        show: function (id) {

                            this.createCenteredSubHeading(n2_printf(n2_('Do you really want to delete the set and all associated %s?'), visualManager.labels.visuals)).appendTo(this.content);

                            this.controls.find('.n2-button-grey')
                                .on('click', $.proxy(function (e) {
                                    e.preventDefault();
                                    this.goBack();
                                }, this));

                            this.controls.find('.n2-button-red')
                                .html('Yes, delete "' + visualManager.sets[id].set.value + '"')
                                .on('click', $.proxy(function (e) {
                                    e.preventDefault();
                                    setsManager.deleteVisualSet(id)
                                        .done($.proxy(this.goBack, this));
                                }, this));
                        }
                    }
                }
            }, false);
        }
        this.modal.show(false, [this.visualManager.setsSelector.val()]);
    };

    scope.NextendVisualSetsManagerEditable = NextendVisualSetsManagerEditable;


    function NextendVisualSet(set, visualManager) {
        this.set = set;
        this.visualManager = visualManager;

        this.visualList = $('<ul class="n2-list n2-h4"></ul>');


        this.visualManager.sets[set.id] = this;
        if (set.referencekey != '') {
            this.visualManager.setsByReference[set.referencekey] = set;
        }

        this.option = $('<option value="' + set.id + '">' + set.value + '</option>')
            .appendTo(this.visualManager.setsSelector);
    };


    NextendVisualSet.prototype.active = function () {
        $.when(this._loadVisuals())
            .done($.proxy(function () {
                this.visualList.appendTo(this.visualManager.visualListContainer);
            }, this));
    };

    NextendVisualSet.prototype.notActive = function () {
        this.visualList.detach();
    };

    NextendVisualSet.prototype.loadVisuals = function (visuals) {
        if (typeof this.visuals === 'undefined') {
            this.visuals = {};
            for (var i = 0; i < visuals.length; i++) {
                this.addVisual(visuals[i]);
            }
        }
    };

    NextendVisualSet.prototype._loadVisuals = function () {
        if (this.visuals == null) {
            return NextendAjaxHelper.ajax({
                type: "POST",
                url: NextendAjaxHelper.makeAjaxUrl(this.visualManager.parameters.ajaxUrl, {
                    nextendaction: 'loadVisualsForSet'
                }),
                data: {
                    setId: this.set.id
                },
                dataType: 'json'
            })
                .done($.proxy(function (response) {
                    this.loadVisuals(response.data.visuals);
                }, this));
        }
        return true;
    };

    NextendVisualSet.prototype.addVisual = function (visual) {
        if (typeof this.visuals[visual.id] === 'undefined') {
            this.visuals[visual.id] = this.visualManager.createVisual(visual, this);
            this.visualList.append(this.visuals[visual.id].createRow());
        }
        return this.visuals[visual.id];
    };

    NextendVisualSet.prototype.rename = function (name) {
        this.set.value = name;
        this.option.html(name);
    };

    NextendVisualSet.prototype.delete = function () {
        this.option.remove();
        delete this.visualManager.sets[this.set.id];
    };

    scope.NextendVisualSet = NextendVisualSet;

})(n2, window);