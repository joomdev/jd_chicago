;
(function ($, scope) {

    function NextendVisualManagerModals(visualManager) {
        this.visualManager = visualManager;
        this.linkedOverwriteOrSaveAs = null;
        this.saveAs = null;
    };

    NextendVisualManagerModals.prototype.getLinkedOverwriteOrSaveAs = function () {
        if (this.linkedOverwriteOrSaveAs == null) {
            var context = this;
            this.linkedOverwriteOrSaveAs = new NextendModal({
                zero: {
                    size: [
                        500,
                        140
                    ],
                    title: '',
                    back: false,
                    close: true,
                    content: '',
                    controls: ['<a href="#" class="n2-button n2-button-big n2-button-grey n2-uc n2-h4">' + n2_('Save as new') + '</a>', '<a href="#" class="n2-button n2-button-big n2-button-green n2-uc n2-h4">' + n2_('Overwrite current') + '</a>'],
                    fn: {
                        show: function () {
                            this.title.html(n2_printf(n2_('%s changed - %s'), context.visualManager.labels.visual, context.visualManager.activeVisual.name));
                            if (context.visualManager.activeVisual && !context.visualManager.activeVisual.isEditable()) {
                                this.loadPane('saveAsNew');
                            } else {
                                this.controls.find('.n2-button-green')
                                    .on('click', $.proxy(function (e) {
                                        e.preventDefault();
                                        context.visualManager.saveActiveVisual(context.visualManager.activeVisual.name)
                                            .done($.proxy(function () {
                                                this.hide(e);
                                                context.visualManager.setAndClose(context.visualManager.activeVisual.id);
                                                context.visualManager.hide();
                                            }, this));
                                    }, this));

                                this.controls.find('.n2-button-grey')
                                    .on('click', $.proxy(function (e) {
                                        e.preventDefault();
                                        this.loadPane('saveAsNew');
                                    }, this));
                            }
                        }
                    }
                },
                saveAsNew: {
                    size: [
                        500,
                        220
                    ],
                    title: n2_('Save as'),
                    back: 'zero',
                    close: true,
                    content: '<form class="n2-form"></form>',
                    controls: ['<a href="#" class="n2-button n2-button-big n2-button-green n2-uc n2-h4">' + n2_('Save as new') + '</a>'],
                    fn: {
                        show: function () {

                            var button = this.controls.find('.n2-button'),
                                form = this.content.find('.n2-form').on('submit', function (e) {
                                    e.preventDefault();
                                    button.trigger('click');
                                }).append(this.createInput(n2_('Name'), 'n2-visual-name', 'width: 446px;')),
                                nameField = this.content.find('#n2-visual-name').focus();

                            if (context.visualManager.activeVisual) {
                                nameField.val(context.visualManager.activeVisual.name);
                            }

                            button.on('click', $.proxy(function (e) {
                                e.preventDefault();
                                var name = nameField.val();
                                if (name == '') {
                                    nextend.notificationCenter.error(n2_('Please fill the name field!'));
                                } else {
                                    context.visualManager._saveAsNew(name)
                                        .done($.proxy(function () {
                                            this.hide(e);
                                            context.visualManager.setAndClose(context.visualManager.activeVisual.id);
                                            context.visualManager.hide();
                                        }, this));
                                }
                            }, this));
                        }
                    }
                }
            }, false);
        }
        return this.linkedOverwriteOrSaveAs;
    };

    NextendVisualManagerModals.prototype.getSaveAs = function () {
        if (this.saveAs === null) {
            var context = this;
            this.saveAs = new NextendModal({
                zero: {
                    size: [
                        500,
                        220
                    ],
                    title: n2_('Save as'),
                    back: false,
                    close: true,
                    content: '<form class="n2-form"></form>',
                    controls: ['<a href="#" class="n2-button n2-button-big n2-button-green n2-uc n2-h4">' + n2_('Save as new') + '</a>'],
                    fn: {
                        show: function () {

                            var button = this.controls.find('.n2-button'),
                                form = this.content.find('.n2-form').on('submit', function (e) {
                                    e.preventDefault();
                                    button.trigger('click');
                                }).append(this.createInput(n2_('Name'), 'n2-visual-name', 'width: 446px;')),
                                nameField = this.content.find('#n2-visual-name').focus();

                            if (context.visualManager.activeVisual) {
                                nameField.val(context.visualManager.activeVisual.name);
                            }

                            button.on('click', $.proxy(function (e) {
                                e.preventDefault();
                                var name = nameField.val();
                                if (name == '') {
                                    nextend.notificationCenter.error(n2_('Please fill the name field!'));
                                } else {
                                    context.visualManager._saveAsNew(name)
                                        .done($.proxy(this.hide, this, e));
                                }
                            }, this));
                        }
                    }
                }
            }, false);
        }
        return this.saveAs;
    };

    scope.NextendVisualManagerModals = NextendVisualManagerModals;
})(n2, window);