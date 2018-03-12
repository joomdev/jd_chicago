;
(function ($, scope) {

    function NextendNotificationCenter() {
        this.stack = [];
        this.tween = null;

        nextend.ready($.proxy(function () {
            var mainTopBar = $('#n2-admin').find('.n2-main-top-bar');
            if (mainTopBar.length > 0) {
                var stack = new NextendNotificationCenterStack($('#n2-admin').find('.n2-main-top-bar'));
                stack.enableStack();
            } else {
                var stack = new NextendNotificationCenterStackModal($('#n2-admin'));
                stack.enableStack();
            }
        }, this));
    };


    NextendNotificationCenter.prototype.add = function (stack) {
        this.stack.push(stack);
    };

    NextendNotificationCenter.prototype.popStack = function () {
        this.stack.pop();
    };

    /**
     * @returns {NextendNotificationCenterStack}
     */
    NextendNotificationCenter.prototype.getCurrentStack = function () {
        return this.stack[this.stack.length - 1];
    };

    NextendNotificationCenter.prototype.success = function (message, parameters) {
        this.getCurrentStack().success(message, parameters);
    };

    NextendNotificationCenter.prototype.error = function (message, parameters) {
        this.getCurrentStack().error(message, parameters);
    };

    NextendNotificationCenter.prototype.notice = function (message, parameters) {
        this.getCurrentStack().notice(message, parameters);
    };

    window.nextend.notificationCenter = new NextendNotificationCenter();


    function NextendNotificationCenterStack(bar) {
        this.messages = [];
        this.isShow = false;
        this.importantOnly = 0;

        this.importantOnlyNode = $('<div class="n2-notification-important n2-h5 ' + (this.importantOnly ? 'n2-active' : '') + '"><span>' + n2_('Show only errors') + '</span><div class="n2-checkbox n2-light"><i class="n2-i n2-i-tick"></i></div></div>')
            .on('click', $.proxy(this.changeImportant, this));
        $.jStorage.listenKeyChange('ss-important-only', $.proxy(this.importantOnlyChanged, this));
        this.importantOnlyChanged();

        this._init(bar);
        this.emptyMessage = $('<div class="n2-notification-empty n2-h4">' + n2_('There are no messages to display.') + '</div>');
    }

    NextendNotificationCenterStack.prototype._init = function (bar) {

        this.showButton = bar.find('.n2-notification-button')
            .on('click', $.proxy(this.hideOrShow, this));

        var settings = $('<div class="n2-notification-settings"></div>')
            .append($('<div class="n2-button n2-button-blue n2-button-small n2-h5 n2-uc n2-notification-clear">' + n2_('Got it!') + '</div>').on('click', $.proxy(this.clear, this)))
            .append(this.importantOnlyNode);


        this.container = this.messageContainer = $('<div class="n2-notification-center n2-border-radius-br n2-border-radius-bl"></div>')
            .append(settings)
            .appendTo(bar);
    };

    NextendNotificationCenterStack.prototype.enableStack = function () {
        nextend.notificationCenter.add(this);
    };

    NextendNotificationCenterStack.prototype.popStack = function () {
        nextend.notificationCenter.popStack();
    };

    NextendNotificationCenterStack.prototype.hideOrShow = function (e) {
        e.preventDefault();
        if (this.isShow) {
            this.hide()
        } else {
            this.show();
        }
    };

    NextendNotificationCenterStack.prototype.show = function () {
        if (!this.isShow) {
            this.isShow = true;

            if (this.messages.length == 0) {
                this.showEmptyMessage();
            }

            if (this.showButton) {
                this.showButton.addClass('n2-active');
            }
            this.container.addClass('n2-active');

            this.container.css('display', 'block');

            this._animateShow();
        }
    };

    NextendNotificationCenterStack.prototype.hide = function () {
        if (this.isShow) {
            if (this.showButton) {
                this.showButton.removeClass('n2-active');
            }
            this.container.removeClass('n2-active');

            this._animateHide();

            this.container.css('display', 'none');

            this.isShow = false;
        }
    };

    NextendNotificationCenterStack.prototype._animateShow = function () {
        if (this.tween) {
            this.tween.pause();
        }
        this.tween = NextendTween.fromTo(this.container, 0.4, {
            opacity: 0
        }, {
            opacity: 1
        }).play();
    };

    NextendNotificationCenterStack.prototype._animateHide = function () {
        if (this.tween) {
            this.tween.pause();
        }
    };

    NextendNotificationCenterStack.prototype.success = function (message, parameters) {
        this._message('success', n2_('success'), message, parameters);
    };

    NextendNotificationCenterStack.prototype.error = function (message, parameters) {
        this._message('error', n2_('error'), message, parameters);
    };

    NextendNotificationCenterStack.prototype.notice = function (message, parameters) {
        this._message('notice', n2_('notice'), message, parameters);
    };

    NextendNotificationCenterStack.prototype._message = function (type, label, message, parameters) {

        this.hideEmptyMessage();

        parameters = $.extend({
            timeout: false,
            remove: false
        }, parameters);

        var messageNode = $('<div></div>');

        if (parameters.timeout) {
            setTimeout($.proxy(function () {
                this.hideMessage(messageNode, parameters.remove);
            }, this), parameters.timeout * 1000);
        }

        messageNode
            .addClass('n2-table n2-table-fixed n2-h3 n2-border-radius n2-notification-message n2-notification-message-' + type)
            .append($('<div class="n2-tr"></div>')
                .append('<div class="n2-td n2-first"><i class="n2-i n2-i-n-' + type + '"/></div>')
                .append('<div class="n2-td n2-message"><h4 class="n2-h4 n2-uc">' + label + '</h4><p class="n2-h4">' + message + '</p></div>'))
            .prependTo(this.messageContainer);

        this.messages.push(messageNode);
        if (this.messages.length > 3) {
            this.messages.shift().remove();
        }

        if (!this.importantOnly || type == 'error' || type == 'notice') {
            this.show();
        }
        return messageNode;
    };

    NextendNotificationCenterStack.prototype.hideMessage = function (message, remove) {
        if (remove) {
            this.deleteMessage(message);
        } else {
            this.hide();
        }
    };

    NextendNotificationCenterStack.prototype.deleteMessage = function (message) {
        var index = $.inArray(message, this.messages);
        if (index > -1) {
            this.messages.splice(index, 1);
            message.remove();
        }
        if (this.messages.length == 0) {
            this.hide();
        }
    };
    NextendNotificationCenterStack.prototype.clear = function () {
        for (var i = this.messages.length - 1; i >= 0; i--) {
            this.messages.pop().remove();
        }

        this.showEmptyMessage();

        this.hide();
    };
    NextendNotificationCenterStack.prototype.changeImportant = function () {
        if (this.importantOnly) {
            $.jStorage.set('ss-important-only', 0);
        } else {
            $.jStorage.set('ss-important-only', 1);
        }
    };

    NextendNotificationCenterStack.prototype.importantOnlyChanged = function () {
        this.importantOnly = parseInt($.jStorage.get('ss-important-only', 0));
        if (this.importantOnly) {
            this.importantOnlyNode.addClass('n2-active');
        } else {
            this.importantOnlyNode.removeClass('n2-active');
        }
    };

    NextendNotificationCenterStack.prototype.showEmptyMessage = function () {
        this.emptyMessage.prependTo(this.container);
    };

    NextendNotificationCenterStack.prototype.hideEmptyMessage = function () {
        this.emptyMessage.detach();
    };

    scope.NextendNotificationCenterStack = NextendNotificationCenterStack;


    function NextendNotificationCenterStackModal() {
        NextendNotificationCenterStack.prototype.constructor.apply(this, arguments);
    }

    NextendNotificationCenterStackModal.prototype = Object.create(NextendNotificationCenterStack.prototype);
    NextendNotificationCenterStackModal.prototype.constructor = NextendNotificationCenterStackModal;


    NextendNotificationCenterStackModal.prototype._init = function (bar) {
        var settings = $('<div class="n2-notification-settings"></div>')
            .append($('<div class="n2-button n2-button-blue n2-button-small n2-h5 n2-uc n2-notification-clear">Got it!</div>').on('click', $.proxy(this.clear, this)))
            .append(this.importantOnlyNode);

        this.messageContainer = $('<div class="n2-notification-center n2-border-radius"></div>')
            .append(settings);
        this.container = $('<div class="n2-notification-center-modal"></div>')
            .append(this.messageContainer)
            .appendTo(bar);
    };

    NextendNotificationCenterStackModal.prototype.show = function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }
        NextendEsc.add($.proxy(function () {
            this.clear();
            return false;
        }, this));

        NextendNotificationCenterStack.prototype.show.apply(this, arguments);
    };

    NextendNotificationCenterStackModal.prototype.hide = function () {
        NextendEsc.pop();

        NextendNotificationCenterStack.prototype.hide.apply(this, arguments);
    };

    NextendNotificationCenterStackModal.prototype._animateShow = function () {

    };

    NextendNotificationCenterStackModal.prototype._animateHide = function () {

    };

    scope.NextendNotificationCenterStackModal = NextendNotificationCenterStackModal;

})(n2, window);