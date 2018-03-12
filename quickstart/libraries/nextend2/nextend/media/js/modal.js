;
(function ($, scope) {

    var counter = 0;

    function NextendModal(panes, show, args) {
        this.inited = false;
        this.currentPane = null;
        this.customClass = '';
        this.$ = $(this);
        this.counter = counter++;

        this.panes = panes;

        if (show) {
            this.show(null, args);
        }

    }

    NextendModal.prototype.setCustomClass = function (customClass) {
        this.customClass = customClass;
    };

    NextendModal.prototype.lateInit = function () {
        if (!this.inited) {

            for (var k in this.panes) {
                this.panes[k] = $.extend({
                    customClass: '',
                    fit: false,
                    size: false,
                    back: false,
                    close: true,
                    controlsClass: '',
                    controls: [],
                    fn: {}
                }, this.panes[k]);
            }

            var stopClick = false;
            this.modal = $('<div class="n2-modal ' + this.customClass + '"/>').css('opacity', 0)
                .on('click', $.proxy(function (e) {
                    if (stopClick == false) {
                        if (!this.close.hasClass('n2-hidden') && $(e.target).closest('.n2-notification-center-modal').length == 0) {
                            this.hide(e);
                        }
                    }
                    stopClick = false;
                }, this));
            this.window = $('<div class="n2-modal-window n2-border-radius"/>')
                .on('click', function (e) {
                    stopClick = true;
                }).appendTo(this.modal);
            this.notificationStack = new NextendNotificationCenterStackModal(this.modal);

            var titleContainer = $('<div class="n2-modal-title n2-content-box-title-bg"/>')
                .appendTo(this.window);

            this.title = $('<div class="n2-h2 n2-ucf"/>').appendTo(titleContainer);
            this.back = $('<i class="n2-i n2-i-a-back"/>')
                .on('click', $.proxy(this.goBackButton, this))
                .appendTo(titleContainer);
            this.close = $('<i class="n2-i n2-i-a-deletes"/>')
                .on('click', $.proxy(this.hide, this))
                .appendTo(titleContainer);

            this.content = $('<div class="n2-modal-content"/>').appendTo(this.window);
            this.controls = $('<div class="n2-table n2-table-fixed n2-table-auto"/>');

            $('<div class="n2-modal-controls"/>')
                .append(this.controls)
                .appendTo(this.window);

            this.inited = true;
        }
    };

    NextendModal.prototype.show = function (paneId, args) {
        this.lateInit();
        this.notificationStack.enableStack();
        if (typeof paneId === 'undefined' || !paneId) {
            paneId = 'zero';
        }

        NextendEsc.add($.proxy(function () {
            if (!this.close.hasClass('n2-hidden')) {
                this.hide('esc');
                return true;
            }
            return false;
        }, this));

        this.loadPane(paneId, false, true, args);

        NextendTween.fromTo(this.modal, 0.3, {
            opacity: 0
        }, {
            opacity: 1,
            ease: 'easeOutCubic'
        }).play();
    };

    NextendModal.prototype.hide = function (e) {
        this.apply('hide');
        $(window).off('.n2-modal-' + this.counter);
        this.notificationStack.popStack();
        if (arguments.length > 0 && e != 'esc') {
            NextendEsc.pop();
        }
        NextendTween.to(this.modal, 0.3, {
            opacity: 0,
            onComplete: $.proxy(function () {
                this.apply('destroy');
                this.currentPane = null;
                this.modal.detach();
            }, this),
            ease: 'easeOutCubic'
        }).play();
        $(document).off('keyup.n2-esc-modal');
    };

    NextendModal.prototype.destroy = function () {
        this.modal.remove();
    };

    NextendModal.prototype.loadPane = function (id, backward, isShow, args) {
        var end = $.proxy(function () {
            var pane = this.panes[id];
            this.currentPane = pane;

            if (pane.title !== false) {
                this.title.html(pane.title);
            }

            if (pane.back === false) {
                this.back.addClass('n2-hidden');
            } else {
                this.back.removeClass('n2-hidden');
            }

            if (pane.close === false) {
                this.close.addClass('n2-hidden');
            } else {
                this.close.removeClass('n2-hidden');
            }

            this.content.find('> *').detach();
            this.content.append(pane.content);


            var hasControls = false;
            var tr = $('<div class="n2-tr" />');
            var i = 0;
            for (; i < pane.controls.length; i++) {
                $('<div class="n2-td"/>')
                    .addClass('n2-modal-controls-' + i)
                    .html(pane.controls[i])
                    .appendTo(tr);
                hasControls = true;
            }

            tr.addClass('n2-modal-controls-' + i);
            this.controls.html(tr);
            this.controls.attr('class', 'n2-table n2-table-fixed n2-table-auto ' + pane.controlsClass);


            if (typeof isShow == 'undefined' || !isShow) {
                NextendTween.fromTo(this.window, 0.3, {
                    x: backward ? -2000 : 2000
                }, {
                    x: 0,
                    ease: 'easeOutCubic'
                }).play();
            }

            this.modal.appendTo('#n2-admin');

            if (pane.fit) {
                var $w = $(window),
                    margin = 40,
                    resize = $.proxy(function () {
                        var w = $w.width() - 2 * margin,
                            h = $w.height() - 2 * margin;
                        this.window.css({
                            width: w,
                            height: h,
                            marginLeft: w / -2,
                            marginTop: h / -2
                        });

                        this.content.css({
                            height: h - 80 - (hasControls ? this.controls.parent().outerHeight(true) : 0),
                            overflow: 'hidden'
                        });
                    }, this);
                resize();
                $w.on('resize.n2-modal-' + this.counter, resize);
            } else if (pane.size !== false) {
                this.window.css({
                    width: pane.size[0],
                    height: pane.size[1],
                    marginLeft: pane.size[0] / -2,
                    marginTop: pane.size[1] / -2
                });

                this.content.css({
                    height: pane.size[1] - 80 - (hasControls ? this.controls.parent().outerHeight(true) : 0),
                    overflow: 'hidden'
                });

            }

            this.apply('show', args);

        }, this);

        if (this.currentPane !== null) {
            this.apply('destroy');
            NextendTween.to(this.window, 0.3, {
                x: backward ? 2000 : -2000,
                onComplete: end,
                ease: 'easeOutCubic'
            }).play();
        } else {
            end();
        }

    };

    NextendModal.prototype.trigger = function (event, args) {
        this.$.trigger(event, args);
    };

    NextendModal.prototype.on = function (event, fn) {
        this.$.on(event, fn);
    };

    NextendModal.prototype.one = function (event, fn) {
        this.$.one(event, fn);
    };

    NextendModal.prototype.off = function (event, fn) {
        this.$.off(event, fn);
    };

    NextendModal.prototype.goBackButton = function () {
        var args = null;
        if (typeof this.goBackArgs !== null) {
            args = this.goBackArgs;
            this.goBackArgs = null;
        }
        this.goBack(args);
    };

    NextendModal.prototype.goBack = function (args) {
        if (this.apply('goBack', args)) {
            this.loadPane(this.currentPane.back, true, false, args);
        }
    };

    NextendModal.prototype.apply = function (event, args) {
        if (typeof this.currentPane.fn[event] !== 'undefined') {
            return this.currentPane.fn[event].apply(this, args);
        }
        return true;
    };

    NextendModal.prototype.createInput = function (label, id) {
        var style = '';
        if (arguments.length == 3) {
            style = arguments[2];
        }
        return $('<div class="n2-form-element-mixed"><div class="n2-mixed-group"><div class="n2-mixed-label"><label for="' + id + '">' + label + '</label></div><div class="n2-mixed-element"><div class="n2-form-element-text n2-border-radius"><input type="text" id="' + id + '" value="" class="n2-h5" autocomplete="off" style="' + style + '"></div></div></div></div>');
    };

    NextendModal.prototype.createInputUnit = function (label, id, unit) {
        var style = '';
        if (arguments.length == 4) {
            style = arguments[3];
        }
        return $('<div class="n2-form-element-mixed"><div class="n2-mixed-group"><div class="n2-mixed-label"><label for="' + id + '">' + label + '</label></div><div class="n2-mixed-element"><div class="n2-form-element-text n2-border-radius"><input type="text" id="' + id + '" value="" class="n2-h5" autocomplete="off" style="' + style + '"><div class="n2-text-unit n2-h5 n2-uc">' + unit + '</div></div></div></div></div>');
    };

    NextendModal.prototype.createInputSub = function (label, id, sub) {
        var style = '';
        if (arguments.length == 4) {
            style = arguments[3];
        }
        return $('<div class="n2-form-element-mixed"><div class="n2-mixed-group"><div class="n2-mixed-label"><label for="' + id + '">' + label + '</label></div><div class="n2-mixed-element"><div class="n2-form-element-text n2-border-radius"><div class="n2-text-sub-label n2-h5 n2-uc">' + sub + '</div><input type="text" id="' + id + '" value="" class="n2-h5" autocomplete="off" style="' + style + '"></div></div></div></div>');
    };

    NextendModal.prototype.createTextarea = function (label, id) {
        var style = '';
        if (arguments.length == 3) {
            style = arguments[2];
        }
        return $('<div class="n2-form-element-mixed"><div class="n2-mixed-group"><div class="n2-mixed-label"><label for="' + id + '">' + label + '</label></div><div class="n2-mixed-element"><div class="n2-form-element-textarea n2-border-radius"><textarea id="' + id + '" class="n2-h5" autocomplete="off" style="resize:none;' + style + '"></textarea></div></div></div></div>');
    };

    NextendModal.prototype.createHeading = function (title) {
        return $('<h3 class="n2-h3">' + title + '</h3>');
    };
    NextendModal.prototype.createSubHeading = function (title) {
        return $('<h3 class="n2-h4">' + title + '</h3>');
    };

    NextendModal.prototype.createCenteredHeading = function (title) {
        return $('<h3 class="n2-h3 n2-center">' + title + '</h3>');
    };
    NextendModal.prototype.createCenteredSubHeading = function (title) {
        return $('<h3 class="n2-h4 n2-center">' + title + '</h3>');
    };

    NextendModal.prototype.createResult = function () {
        return $('<div class="n2-result"></div>');
    };

    NextendModal.prototype.createTable = function (data, style) {
        var table = $('<table class="n2-table-fancy"/>');
        for (var j = 0; j < data.length; j++) {
            var tr = $('<tr />').appendTo(table);
            for (var i = 0; i < data[j].length; i++) {
                tr.append($('<td style="' + style[i] + '"/>').append(data[j][i]));
            }
        }
        return table;
    };

    NextendModal.prototype.createTableWrap = function () {
        return $('<div class="n2-table-fancy-wrap" style="overflow:auto;height:196px;" />');
    };

    NextendModal.prototype.createImageRadio = function (options) {

        var wrapper = $('<div class="n2-modal-radio" />'),
            input = $('<input type="hidden" value="' + options[0].key + '"/>').appendTo(wrapper);

        for (var i = 0; i < options.length; i++) {
            wrapper.append('<div class="n2-modal-radio-option" data-key="' + options[i].key + '" style="background-image: url(\'' + nextend.imageHelper.fixed(options[i].image) + '\')"><div class="n2-h4">' + options[i].name + '</div></div>')
        }

        var options = wrapper.find('.n2-modal-radio-option');
        options.eq(0).addClass('n2-active');

        options.on('click', function (e) {
            options.removeClass('n2-active');
            var option = $(e.currentTarget);
            option.addClass('n2-active');
            input.val(option.data('key'));
        });

        return wrapper;
    };

    scope.NextendModal = NextendModal;


    scope.NextendModalSetting = {
        show: function (title, url) {
            new NextendModal({
                zero: {
                    size: [
                        1300,
                        700
                    ],
                    title: title,
                    content: '<iframe src="' + url + '" width="1300" height="640" frameborder="0" style="margin:0 -20px -20px -20px;"></iframe>'
                }
            }, true);
        }
    };
    scope.NextendModalDocumentation = function (title, url) {
        new NextendModal({
            zero: {
                size: [
                    760,
                    700
                ],
                title: title,
                content: '<iframe src="' + url + '" width="760" height="640" frameborder="0" style="margin:0 -20px -20px -20px;"></iframe>'
            }
        }, true);
    };

    function NextendSimpleModal(html) {
        this.$ = $(this);
        this.modal = $('<div class="n2-modal n2-modal-simple"/>').css({
            opacity: 0,
            display: 'none'
        }).on('click', $.proxy(this.hide, this))
            .appendTo('#n2-admin');

        $('<i class="n2-i n2-i-a-deletes"/>')
            .appendTo(this.modal);

        this.window = $('<div class="n2-modal-window n2-border-radius"/>')
            .on('click', function (e) {
                e.stopPropagation();
            })
            .appendTo(this.modal);
        this.notificationStack = new NextendNotificationCenterStackModal(this.modal);
        this.content = $(html).appendTo(this.window);
    };

    NextendSimpleModal.prototype.resize = function () {
        this.window.width(this.modal.width() - 100);
        this.window.height(this.modal.height() - 100);
    };

    NextendSimpleModal.prototype.show = function () {
        this.modal.css('display', 'block');
        this.resize();
        $(window).on('resize.n2-simple-modal', $.proxy(this.resize, this));
        this.notificationStack.enableStack();

        NextendEsc.add($.proxy(function () {
            this.hide('esc');
            return true;
        }, this));

        NextendTween.fromTo(this.modal, 0.3, {
            opacity: 0
        }, {
            opacity: 1,
            ease: 'easeOutCubic'
        }).play();
    };

    NextendSimpleModal.prototype.hide = function (e) {
        this.notificationStack.popStack();
        if (arguments.length > 0 && e != 'esc') {
            NextendEsc.pop();
        }
        NextendTween.to(this.modal, 0.3, {
            opacity: 0,
            ease: 'easeOutCubic',
            onComplete: $.proxy(function () {
                this.modal.css('display', 'none');
            }, this)
        }).play();
        $(document).off('keyup.n2-esc-modal');
        $(window).off('.n2-simple-modal');
        this.modal.trigger('ModalHide');
    };

    scope.NextendSimpleModal = NextendSimpleModal;

    function NextendDeleteModal(identifier, instanceName, callback) {
        if ($.jStorage.get('n2-delete-' + identifier, false)) {
            callback();
            return true;
        }
        new NextendModal({
            zero: {
                size: [
                    500,
                    190
                ],
                title: n2_('Delete'),
                back: false,
                close: true,
                content: '',
                controls: ['<a href="#" class="n2-button n2-button-big n2-button-grey n2-uc n2-h4">' + n2_('Cancel') + '</a>', '<div class="n2-button n2-button-with-menu n2-button-big n2-button-red"><a href="#" class="n2-button-inner n2-uc n2-h4">' + n2_('Delete') + '</a><div class="n2-button-menu-open"><i class="n2-i n2-i-buttonarrow"></i><div class="n2-button-menu"><div class="n2-button-menu-inner n2-border-radius"><a href="#" class="n2-h4">' + n2_('Delete and never show again') + '</a></div></div></div></div>'],
                fn: {
                    show: function () {
                        this.createCenteredSubHeading(n2_('Are you sure you want to delete?')).appendTo(this.content);
                        this.controls.find('.n2-button-grey')
                            .on('click', $.proxy(function (e) {
                                e.preventDefault();
                                this.hide(e);
                            }, this));
                        this.controls.find('.n2-button-red a')
                            .on('click', $.proxy(function (e) {
                                e.preventDefault();
                                callback();
                                this.hide(e);
                            }, this));

                        this.controls.find('.n2-button-red .n2-button-menu-inner a')
                            .on('click', $.proxy(function (e) {
                                e.preventDefault();
                                $.jStorage.set('n2-delete-' + identifier, true);
                            }, this));

                        this.controls.find(".n2-button-menu-open").n2opener();

                    },
                    destroy: function () {
                        this.destroy();
                    }
                }
            }
        }, true);
        return false;
    };

    scope.NextendDeleteModal = NextendDeleteModal;

    function NextendDeleteModalLink(element, identifier, instanceName) {

        NextendDeleteModal(identifier, instanceName, function () {
            window.location.href = $(element).attr('href');
        });
        return false;
    };
    scope.NextendDeleteModalLink = NextendDeleteModalLink;

})
(n2, window);