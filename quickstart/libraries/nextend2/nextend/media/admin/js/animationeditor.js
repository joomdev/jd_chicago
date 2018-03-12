;
(function ($, scope) {

    var zero = {
        opacity: 1,
        x: 0,
        y: 0,
        z: 0,
        rotationX: 0,
        rotationY: 0,
        rotationZ: 0,
        scaleX: 1,
        scaleY: 1,
        scaleZ: 1,
        skewX: 0
    };

    function NextendAnimationEditorController() {

        this.timeline = new NextendTimeline();

        this.initTabOrdering();

        NextendVisualEditorController.prototype.constructor.apply(this, arguments);

        $('#n2-animation-editor-tab-add').on('click', $.proxy(this.addTab, this));
        $('#n2-animation-editor-tab-delete').on('click', $.proxy(this.deleteCurrentTab, this));
        this.preview = $('<div class="n2-animation-preview-box" style="background-image: url(' + nextend.imageHelper.getRepeatedPlaceholder() + ')"></div>').appendTo($('#n2-animation-editor-preview'));
        this.setPreviewSize(400, 100);

        this.initBackgroundColor();
    }

    NextendAnimationEditorController.prototype = Object.create(NextendVisualEditorController.prototype);
    NextendAnimationEditorController.prototype.constructor = NextendAnimationEditorController;

    NextendAnimationEditorController.prototype.loadDefaults = function () {
        NextendVisualEditorController.prototype.loadDefaults.call(this);
        this.type = 'animation';
        this.group = 'in';
        this.mode = 0;
        this.playing = false;
        this.specialZero = 0;
        this.transformOrigin = '0|*|0|*|0';
        this.playEvent = '';
        this.pauseEvent = '';
        this.stopEvent = '';
        this.repeatable = 0;
        this.instantOut = 0;
    };


    NextendAnimationEditorController.prototype.setPreviewSize = function (w, h) {
        this.preview.css({
            width: w,
            height: h,
            marginLeft: -w / 2,
            marginTop: -h / 2
        });
        return this;
    };

    NextendAnimationEditorController.prototype.setGroup = function (group) {
        this.group = group;
        return this;
    };

    NextendAnimationEditorController.prototype.initPreviewModes = function () {

        this.previewModes = [this.previewModesList['solo']];
    };

    NextendAnimationEditorController.prototype.makePreviewModes = function () {
        if (this.tabField.options.length > 0) {
            this.setPreviewModes(this.previewModes);
        } else {
            this.setPreviewModes([]);
        }
    };

    NextendAnimationEditorController.prototype.initRenderer = function () {
        return new NextendVisualRenderer(this);
    };

    NextendAnimationEditorController.prototype.initEditor = function () {
        var editor = new NextendAnimationEditor();
        editor.$.on('nameChanged', $.proxy(this.animationNameChanged, this));
        return editor;
    };


    NextendAnimationEditorController.prototype.pause = function () {
        this.clearTimeline();
    };

    NextendAnimationEditorController.prototype.get = function (type) {
        if (type == 'saveAsNew') {
            return {
                specialZero: this.specialZero,
                transformOrigin: this.transformOrigin,
                animations: this.currentVisual
            };
        }
        return this.currentVisual;
    };

    NextendAnimationEditorController.prototype.getEmptyAnimation = function () {
        return {
            name: 'Animation',
            duration: 0.8,
            delay: 0,
            ease: 'easeOutCubic',
            opacity: 1,
            x: 0,
            y: 0,
            z: 0,
            rotationX: 0,
            rotationY: 0,
            rotationZ: 0,
            scaleX: 1,
            scaleY: 1,
            scaleZ: 1,
            skewX: 0
        };
    };

    NextendAnimationEditorController.prototype.getEmptyVisual = function () {
        return [this.getEmptyAnimation()];
    };

    NextendAnimationEditorController.prototype.initBackgroundColor = function () {

        new NextendElementText("n2-animation-editor-background-color");
        new NextendElementColor("n2-animation-editor-background-color", 0);

        var box = this.lightbox.find('.n2-editor-preview-box');
        $('#n2-animation-editor-background-color').on('nextendChange', function () {
            box.css('background', '#' + $(this).val());
        });
    };

    NextendAnimationEditorController.prototype._renderPreview = function () {
        NextendVisualEditorController.prototype._renderPreview.call(this);

        if (this.visible) {
            this.refreshTimeline();
        }
    };

    NextendAnimationEditorController.prototype.setPreview = function (mode) {
        if (this.visible) {
            this.refreshTimeline();
        }
    };

    NextendAnimationEditorController.prototype.getPreviewCssClass = function () {
        return 'n2-' + this.type + '-editor-preview';
    };

    NextendAnimationEditorController.prototype._load = function (visual, tabs, parameters) {

        parameters.previewMode = true;

        for (var i = 0; i < visual.length; i++) {
            visual[i] = $.extend({}, this.getEmptyAnimation(), visual[i]);
        }

        NextendVisualEditorController.prototype._load.call(this, visual, tabs, parameters);
    };

    NextendAnimationEditorController.prototype.initTabOrdering = function () {
        var originalIndex = -1,
            parent = $('#n2-animation-editor-tabs').parent();
        parent.sortable({
            items: '.n2-radio-option',
            start: $.proxy(function (e, ui) {
                originalIndex = this.tabField.options.index(ui.item);
            }, this),
            update: $.proxy(function (e, ui) {
                var targetIndex = ui.item.index();
                parent.sortable('cancel');

                var visualTab = this.currentVisual[originalIndex];
                this.currentVisual.splice(originalIndex, 1);
                this.currentVisual.splice(targetIndex, 0, visualTab);
                for (var i = 0; i < this.currentVisual.length; i++) {
                    this.tabField.options.eq(i).html(this.currentVisual[i].name);
                }
                this.tabField.options.eq(targetIndex).trigger('click');
                originalIndex = -1;

                if (this.currentPreviewMode != 'solo') {
                    this.refreshTimeline();
                } else {
                    this._tabChanged();
                }
            }, this)
        });
    };

    NextendAnimationEditorController.prototype.getTabs = function () {
        var tabs = [];
        for (var i = 0; i < this.currentVisual.length; i++) {
            tabs.push(this.currentVisual[i].name);
        }
        return tabs;
    };

    NextendAnimationEditorController.prototype._tabChanged = function () {
        if (this.currentPreviewMode == 'solo') {
            this.refreshTimeline();
        }
    };

    NextendAnimationEditorController.prototype.clearCurrentTab = function (e) {
        if (e) {
            e.preventDefault();
        }
    };

    NextendAnimationEditorController.prototype.addTab = function (e, force) {
        if (e) {
            e.preventDefault();
        }
        var i = this.tabField.values.length;
        this.currentVisual[i] = this.getEmptyAnimation();
        this.tabField.addTabOption(i + '', this.currentVisual[i].name);

        this.tabField.options.eq(i).trigger('click');

        if (this.currentPreviewMode != 'solo') {
            this.refreshTimeline();
        }
    };

    NextendAnimationEditorController.prototype.deleteCurrentTab = function (e) {
        if (e) {
            e.preventDefault();
        }
        this.deleteTab(this.currentTabIndex);

        this.currentTabIndex = Math.min(this.currentTabIndex, this.currentVisual.length - 1);
        this.tabField.options.eq(this.currentTabIndex).trigger('click');
    };

    NextendAnimationEditorController.prototype.deleteTab = function (index) {
        if (this.currentVisual.length > 1) {
            this.tabField.removeTabOption(this.tabField.values[index]);
            this.tabField.values = [];
            for (var i = 0; i < this.tabField.options.length; i++) {
                this.tabField.values.push(i + '');
            }
            this.currentVisual.splice(index, 1);
        } else {
            this.addTab(null, true);
            this.deleteTab(0);
        }

        if (this.currentPreviewMode != 'solo') {
            this.refreshTimeline();
        }
    };

    NextendAnimationEditorController.prototype.animationNameChanged = function (e, name) {
        this.tabField.options.eq(this.currentTabIndex).html(name);
    };

    NextendAnimationEditorController.prototype.show = function () {
        NextendVisualEditorController.prototype.show.call(this);
        this.createTimeline();
    };

    NextendAnimationEditorController.prototype.hide = function () {
        this.clearTimeline();
        NextendVisualEditorController.prototype.hide.call(this);
    };

    NextendAnimationEditorController.prototype.createTimeline = function () {
        if (this.timeline) {
            this.timeline.pause(0);
        }
        this.timeline = new NextendTimeline({
            paused: 1
        });

        this.timeline.eventCallback("onComplete", $.proxy(function () {
            this.timeline.play(0, false);
        }, this));

        this.timeline.set(this.preview.get(0), {
            transformOrigin: this.transformOrigin.split('|*|').join('% ') + 'px'
        }, 0);

        var animations = [];

        switch (this.currentPreviewMode) {
            case 'solo':
                animations.push($.extend({}, this.currentVisual[this.currentTabIndex]));
                break;
            default:
                $.extend(true, animations, this.currentVisual);
        }

        for (var i = 0; i < animations.length; i++) {
            if (animations[i].delay > 0.5) {
                animations[i].delay = 0.5;
            }
        }

        switch (this.group) {

            case 'in':
                this.buildTimelineIn(this.timeline, this.preview.get(0), animations, 1);
                break;
            case 'loop':
                if (this.currentPreviewMode == 'solo') {
                    this.buildTimelineOut(this.timeline, this.preview.get(0), animations, 1);
                } else {
                    this.buildTimelineLoop(this.timeline, this.preview.get(0), animations, 1);
                }
                break;
            case 'out':
                this.buildTimelineOut(this.timeline, this.preview.get(0), animations, 1);
                break;
            default:
                console.log(this.group + ' animation is not supported!');
        }
        if (this.timeline.totalDuration() > 0) {
            this.timeline.play();
        }
    };

    NextendAnimationEditorController.prototype.refreshTimeline = function () {
        this.clearTimeline();
        this.createTimeline();
    };

    NextendAnimationEditorController.prototype.clearTimeline = function () {
        if (this.timeline) {
            this.timeline.pause();
            if (this.repeatTimeout) {
                clearTimeout(this.repeatTimeout);
            }
            this.timeline.progress(1, true);
        }
    };

    NextendAnimationEditorController.prototype.setCurrentZero = function (element) {
        NextendTween.set(element, $.extend({}, this.currentZero));
    };

    NextendAnimationEditorController.prototype.buildTimelineIn = function (timeline, element, animations, ratio) {

        this.currentZero = zero;
        if (this.group == 'in' && (this.currentPreviewMode != 'solo' || this.currentTabIndex == this.currentVisual.length - 1) && this.specialZero && animations.length > 0) {
            this.currentZero = animations.pop();
            delete this.currentZero.name;
            this.currentZero.x = this.currentZero.x * ratio;
            this.currentZero.y = this.currentZero.y * ratio;
            this.currentZero.z = this.currentZero.z * ratio;
            this.currentZero.rotationX = -this.currentZero.rotationX;
            this.currentZero.rotationY = -this.currentZero.rotationY;
            this.currentZero.rotationZ = -this.currentZero.rotationZ;
            this.setCurrentZero(element);
        }

        var duration = 0,
            chain = this._buildAnimationChainIn(animations, ratio, this.currentZero);
        var i = 0;
        if (chain.length > 0) {
            timeline.fromTo(element, chain[i].duration, chain[i].from, chain[i].to, duration);
            duration += chain[i].duration + chain[i].to.delay;
            i++;

            for (; i < chain.length; i++) {
                timeline.to(element, chain[i].duration, chain[i].to, duration);
                duration += chain[i].duration + chain[i].to.delay;
            }
        }
    };

    NextendAnimationEditorController.prototype._buildAnimationChainIn = function (animations, ratio, currentZero) {
        var preparedAnimations = [
            {
                from: currentZero
            }
        ];
        for (var i = animations.length - 1; i >= 0; i--) {
            var animation = animations[i],
                delay = animation.delay,
                duration = animation.duration,
                ease = animation.ease;
            delete animation.delay;
            delete animation.duration;
            delete animation.ease;
            delete animation.name;

            var previousAnimation = preparedAnimations[0].from;

            //animation.x = parseFloat(previousAnimation.x) + animation.x * ratio;
            //animation.y = parseFloat(previousAnimation.y) + animation.y * ratio;
            //animation.z = parseFloat(previousAnimation.z) + animation.z * ratio;
            animation.x = -animation.x * ratio;
            animation.y = -animation.y * ratio;
            animation.z = -animation.z * ratio;
            animation.rotationX = -animation.rotationX;
            animation.rotationY = -animation.rotationY;
            animation.rotationZ = -animation.rotationZ;

            preparedAnimations.unshift({
                duration: duration,
                from: animation,
                to: $.extend({}, previousAnimation, {
                    ease: ease,
                    delay: delay
                })
            });
        }
        preparedAnimations.pop();
        return preparedAnimations;
    };

    NextendAnimationEditorController.prototype.buildTimelineLoop = function (timeline, element, animations, ratio) {

        var chain = this._buildAnimationChainLoop(animations, ratio);
        var i = 0;
        if (chain.length > 0) {
            timeline.fromTo(element, chain[i].duration, chain[i].from, chain[i].to);
            i++;
            for (; i < chain.length; i++) {
                timeline.to(element, chain[i].duration, chain[i].to);
            }
        }
    };

    NextendAnimationEditorController.prototype._buildAnimationChainLoop = function (animations, ratio) {

        delete animations[0].name;

        if (animations.length == 1) {
            var singleAnimation = animations[0],
                animation = $.extend({}, zero);
            animation.duration = singleAnimation.duration;
            animation.ease = singleAnimation.ease;
            if ((singleAnimation.rotationX == 360 || singleAnimation.rotationY == 360 || singleAnimation.rotationZ == 360) && singleAnimation.opacity == 1 && singleAnimation.x == 0 && singleAnimation.y == 0 && singleAnimation.z == 0 && singleAnimation.scaleX == 1 && singleAnimation.scaleY == 1 && singleAnimation.scaleZ == 1 && singleAnimation.skewX == 0) {
                return [
                    {
                        duration: animations[0].duration,
                        from: $.extend({}, zero),
                        to: animations[0]
                    }
                ];
            } else {
                animations.unshift(animation);
            }
        }
        var i = 0;
        var preparedAnimations = [
            {
                duration: animations[i].duration,
                to: animations[i]
            }
        ];
        i++;
        for (; i < animations.length; i++) {
            var animation = animations[i],
                duration = animation.duration;
            delete animation.duration;
            delete animation.name;

            var previousAnimation = $.extend({}, preparedAnimations[preparedAnimations.length - 1].to);
            delete previousAnimation.delay;
            delete previousAnimation.ease;

            //animation.x = parseFloat(previousAnimation.x) + animation.x * ratio;
            //animation.y = parseFloat(previousAnimation.y) + animation.y * ratio;
            //animation.z = parseFloat(previousAnimation.z) + animation.z * ratio;
            animation.x = animation.x * ratio;
            animation.y = animation.y * ratio;
            animation.z = animation.z * ratio;

            preparedAnimations.push({
                duration: duration,
                from: previousAnimation,
                to: animation
            });
        }

        preparedAnimations.push({
            duration: preparedAnimations[0].duration,
            from: $.extend({}, preparedAnimations[preparedAnimations.length - 1].to),
            to: $.extend({}, preparedAnimations[0].to)
        });
        preparedAnimations.shift();

        return preparedAnimations;
    };

    NextendAnimationEditorController.prototype.buildTimelineOut = function (timeline, element, animations, ratio) {

        var duration = 0,
            chain = this._buildAnimationChainOut(animations, ratio);

        var i = 0;
        if (chain.length > 0) {
            timeline.fromTo(element, chain[i].duration, chain[i].from, chain[i].to, duration);
            duration += chain[i].duration + chain[i].to.delay;
            i++;

            for (; i < chain.length; i++) {
                timeline.to(element, chain[i].duration, chain[i].to, duration);
                duration += chain[i].duration + chain[i].to.delay;
            }
        }

    };

    NextendAnimationEditorController.prototype._buildAnimationChainOut = function (animations, ratio) {
        var preparedAnimations = [
            {
                to: zero
            }
        ];
        for (var i = 0; i < animations.length; i++) {
            var animation = animations[i],
                duration = animation.duration;
            delete animation.duration;
            delete animation.name;

            var previousAnimation = $.extend({}, preparedAnimations[preparedAnimations.length - 1].to);
            delete previousAnimation.delay;
            delete previousAnimation.ease;

            //animation.x = parseFloat(previousAnimation.x) + animation.x * ratio;
            //animation.y = parseFloat(previousAnimation.y) + animation.y * ratio;
            //animation.z = parseFloat(previousAnimation.z) + animation.z * ratio;
            animation.x = animation.x * ratio;
            animation.y = animation.y * ratio;
            animation.z = animation.z * ratio;

            preparedAnimations.push({
                duration: duration,
                from: previousAnimation,
                to: animation
            });
        }
        preparedAnimations.shift();
        return preparedAnimations;
    };

    NextendAnimationEditorController.prototype.loadSpecialZero = function (specialZero) {
        this.editor.fields.specialZero.element.data('field').insideChange(specialZero);
        this.refreshSpecialZero(specialZero);
    };

    NextendAnimationEditorController.prototype.refreshSpecialZero = function (specialZero) {
        this.specialZero = parseInt(specialZero) ? 1 : 0;
        this.refreshTimeline();
    };

    NextendAnimationEditorController.prototype.loadRepeatCount = function (repeatCount) {
        this.editor.fields.repeatCount.element.data('field').insideChange(repeatCount);
        this.refreshRepeatCount(repeatCount);
    };

    NextendAnimationEditorController.prototype.refreshRepeatCount = function (repeatCount) {
        this.repeatCount = Math.max(0, parseInt(repeatCount));
    };

    NextendAnimationEditorController.prototype.loadRepeatStartDelay = function (repeatStartDelay) {
        this.editor.fields.repeatStartDelay.element.data('field').insideChange(repeatStartDelay * 1000);
        this.refreshRepeatStartDelay(repeatStartDelay);
    };

    NextendAnimationEditorController.prototype.refreshRepeatStartDelay = function (repeatStartDelay) {
        this.repeatStartDelay = Math.max(0, parseFloat(repeatStartDelay));
    };

    NextendAnimationEditorController.prototype.loadTransformOrigin = function (transformOrigin) {
        this.editor.fields.transformOrigin.element.data('field').insideChange(transformOrigin);
        this.refreshTransformOrigin(transformOrigin);
    };

    NextendAnimationEditorController.prototype.refreshTransformOrigin = function (transformOrigin) {

        this.transformOrigin = transformOrigin;

        NextendTween.set(this.preview.parent().get(0), {
            perspective: '1000px'
        });
        this.refreshTimeline();
    };

    NextendAnimationEditorController.prototype.loadPlayEvent = function (event) {
        this.editor.fields.playEvent.element.data('field').insideChange(event);
        this.refreshPlayEvent(event);
    };

    NextendAnimationEditorController.prototype.refreshPlayEvent = function (event) {

        this.playEvent = event;
    };

    NextendAnimationEditorController.prototype.loadPauseEvent = function (event) {
        this.editor.fields.pauseEvent.element.data('field').insideChange(event);
        this.refreshPauseEvent(event);
    };

    NextendAnimationEditorController.prototype.refreshPauseEvent = function (event) {
        this.pauseEvent = event;
    };

    NextendAnimationEditorController.prototype.loadStopEvent = function (event) {
        this.editor.fields.stopEvent.element.data('field').insideChange(event);
        this.refreshStopEvent(event);
    };

    NextendAnimationEditorController.prototype.refreshStopEvent = function (event) {
        this.stopEvent = event;
    };

    NextendAnimationEditorController.prototype.loadRepeatable = function (repeatable) {
        this.editor.fields.repeatable.element.data('field').insideChange(repeatable);
        this.refreshRepeatable(repeatable);
    };

    NextendAnimationEditorController.prototype.refreshRepeatable = function (repeatable) {
        this.repeatable = repeatable;
    };

    NextendAnimationEditorController.prototype.loadInstantOut = function (instantOut) {
        this.editor.fields.instantOut.element.data('field').insideChange(instantOut);
        this.refreshInstantOut(instantOut);
    };

    NextendAnimationEditorController.prototype.refreshInstantOut = function (instantOut) {
        this.instantOut = instantOut;
    };

    NextendAnimationEditorController.prototype.featureSpecialZero = function (enabled) {
        var row = this.editor.fields.specialZero.element.closest('tr');
        if (enabled) {
            row.removeClass('n2-hidden');
        } else {
            row.addClass('n2-hidden');
        }
    };

    NextendAnimationEditorController.prototype.featureRepeat = function (enabled) {
        var rows = this.editor.fields.repeatCount.element.closest('tr')
            .add(this.editor.fields.repeatStartDelay.element.closest('tr'));
        if (enabled) {
            rows.removeClass('n2-hidden');
        } else {
            rows.addClass('n2-hidden');
        }
    };

    NextendAnimationEditorController.prototype.featurePlayEvent = function (enabled) {
        var row = this.editor.fields.playEvent.element.closest('.n2-mixed-group');
        if (enabled) {
            row.removeClass('n2-hidden');
        } else {
            row.addClass('n2-hidden');
        }
    };

    NextendAnimationEditorController.prototype.featurePauseEvent = function (enabled) {
        var row = this.editor.fields.pauseEvent.element.closest('.n2-mixed-group');
        if (enabled) {
            row.removeClass('n2-hidden');
        } else {
            row.addClass('n2-hidden');
        }
    };

    NextendAnimationEditorController.prototype.featureStopEvent = function (enabled) {
        var row = this.editor.fields.stopEvent.element.closest('.n2-mixed-group');
        if (enabled) {
            row.removeClass('n2-hidden');
        } else {
            row.addClass('n2-hidden');
        }
    };

    NextendAnimationEditorController.prototype.featureRepeatable = function (enabled) {
        var row = this.editor.fields.repeatable.element.closest('.n2-mixed-group');
        if (enabled) {
            row.removeClass('n2-hidden');
        } else {
            row.addClass('n2-hidden');
        }
    };

    NextendAnimationEditorController.prototype.featureInstantOut = function (enabled) {
        var row = this.editor.fields.instantOut.element.closest('.n2-mixed-group');
        if (enabled) {
            row.removeClass('n2-hidden');
        } else {
            row.addClass('n2-hidden');
        }
    };

    scope.NextendAnimationEditorController = NextendAnimationEditorController;

    function NextendAnimationEditor() {
        NextendVisualEditor.prototype.constructor.apply(this, arguments);

        this.fields = {
            name: {
                element: $('#n2-animation-editorname'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeName, this)
                }
            },
            duration: {
                element: $('#n2-animation-editorduration'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeDuration, this)
                }
            },
            delay: {
                element: $('#n2-animation-editordelay'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeDelay, this)
                }
            },
            easing: {
                element: $('#n2-animation-editoreasing'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeEasing, this)
                }
            },
            opacity: {
                element: $('#n2-animation-editoropacity'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeOpacity, this)
                }
            },
            offset: {
                element: $('#n2-animation-editoroffset'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeOffset, this)
                }
            },
            rotate: {
                element: $('#n2-animation-editorrotate'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeRotate, this)
                }
            },
            scale: {
                element: $('#n2-animation-editorscale'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeScale, this)
                }
            },
            skew: {
                element: $('#n2-animation-editorskew'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeSkew, this)
                }
            },
            specialZero: {
                element: $('#n2-animation-editorspecial-zero'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeSpecialZero, this)
                }
            },
            repeatCount: {
                element: $('#n2-animation-editorrepeat-count'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeRepeatCount, this)
                }
            },
            repeatStartDelay: {
                element: $('#n2-animation-editorrepeat-start-delay'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeRepeatStartDelay, this)
                }
            },
            transformOrigin: {
                element: $('#n2-animation-editortransformorigin'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeTransformOrigin, this)
                }
            },
            playEvent: {
                element: $('#n2-animation-editorplay'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changePlayEvent, this)
                }
            },
            pauseEvent: {
                element: $('#n2-animation-editorpause'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changePauseEvent, this)
                }
            },
            stopEvent: {
                element: $('#n2-animation-editorstop'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeStopEvent, this)
                }
            },
            repeatable: {
                element: $('#n2-animation-editorrepeatable'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeRepeatable, this)
                }
            },
            instantOut: {
                element: $('#n2-animation-editorinstant-out'),
                events: {
                    'outsideChange.n2-editor': $.proxy(this.changeInstantOut, this)
                }
            }
        }
    }

    NextendAnimationEditor.prototype = Object.create(NextendVisualEditor.prototype);
    NextendAnimationEditor.prototype.constructor = NextendAnimationEditor;

    NextendAnimationEditor.prototype.load = function (values) {
        this._off();
        this.fields.name.element.data('field').insideChange(values.name);
        this.fields.duration.element.data('field').insideChange(values.duration * 1000);
        this.fields.delay.element.data('field').insideChange(values.delay * 1000);
        this.fields.easing.element.data('field').insideChange(values.ease);
        this.fields.opacity.element.data('field').insideChange(values.opacity * 100);

        this.fields.offset.element.data('field').insideChange(values.x + '|*|' + values.y + '|*|' + values.z);
        this.fields.rotate.element.data('field').insideChange(values.rotationX + '|*|' + values.rotationY + '|*|' + values.rotationZ);
        this.fields.scale.element.data('field').insideChange(values.scaleX * 100 + '|*|' + values.scaleY * 100 + '|*|' + values.scaleZ * 100);
        this.fields.skew.element.data('field').insideChange(values.skewX);
        this.fields.specialZero.element.data('field').insideChange(nextend.animationManager.controller.specialZero);
        this.fields.repeatCount.element.data('field').insideChange(nextend.animationManager.controller.repeatCount);
        this.fields.repeatStartDelay.element.data('field').insideChange(nextend.animationManager.controller.repeatStartDelay * 1000);
        this.fields.transformOrigin.element.data('field').insideChange(nextend.animationManager.controller.transformOrigin);


        this.fields.playEvent.element.data('field').insideChange(nextend.animationManager.controller.playEvent);
        this.fields.pauseEvent.element.data('field').insideChange(nextend.animationManager.controller.pauseEvent);
        this.fields.stopEvent.element.data('field').insideChange(nextend.animationManager.controller.stopEvent);
        this.fields.repeatable.element.data('field').insideChange(nextend.animationManager.controller.repeatable);
        this.fields.instantOut.element.data('field').insideChange(nextend.animationManager.controller.instantOut);

        this._on();
    };

    NextendAnimationEditor.prototype.changeName = function () {
        this.trigger('name', this.fields.name.element.val());
        this.$.trigger('nameChanged', this.fields.name.element.val());
    };

    NextendAnimationEditor.prototype.changeDuration = function () {
        this.trigger('duration', this.fields.duration.element.val() / 1000);
    };

    NextendAnimationEditor.prototype.changeDelay = function () {
        this.trigger('delay', this.fields.delay.element.val() / 1000);
    };

    NextendAnimationEditor.prototype.changeEasing = function () {
        this.trigger('ease', this.fields.easing.element.val());
    };

    NextendAnimationEditor.prototype.changeOpacity = function () {
        this.trigger('opacity', this.fields.opacity.element.val() / 100);
    };

    NextendAnimationEditor.prototype.changeOffset = function () {
        var offset = this.fields.offset.element.val().split('|*|');
        this.trigger('x', offset[0]);
        this.trigger('y', offset[1]);
        this.trigger('z', offset[2]);
    };

    NextendAnimationEditor.prototype.changeRotate = function () {
        var rotate = this.fields.rotate.element.val().split('|*|');
        this.trigger('rotationX', rotate[0]);
        this.trigger('rotationY', rotate[1]);
        this.trigger('rotationZ', rotate[2]);
    };

    NextendAnimationEditor.prototype.changeScale = function () {
        var scale = this.fields.scale.element.val().split('|*|');
        this.trigger('scaleX', scale[0] / 100);
        this.trigger('scaleY', scale[1] / 100);
        this.trigger('scaleZ', scale[2] / 100);
    };

    NextendAnimationEditor.prototype.changeSkew = function () {
        this.trigger('skewX', this.fields.skew.element.val());
    };

    NextendAnimationEditor.prototype.changeTransformOrigin = function () {
        nextend.animationManager.controller.refreshTransformOrigin(this.fields.transformOrigin.element.val());
    };

    NextendAnimationEditor.prototype.changeSpecialZero = function () {
        nextend.animationManager.controller.refreshSpecialZero(this.fields.specialZero.element.val());
    };

    NextendAnimationEditor.prototype.changeRepeatCount = function () {
        nextend.animationManager.controller.refreshRepeatCount(this.fields.repeatCount.element.val());
    };

    NextendAnimationEditor.prototype.changeRepeatStartDelay = function () {
        nextend.animationManager.controller.refreshRepeatStartDelay(this.fields.repeatStartDelay.element.val() / 1000);
    };

    NextendAnimationEditor.prototype.changePlayEvent = function () {
        nextend.animationManager.controller.refreshPlayEvent(this.fields.playEvent.element.val());
    };

    NextendAnimationEditor.prototype.changePauseEvent = function () {
        nextend.animationManager.controller.refreshPauseEvent(this.fields.pauseEvent.element.val());
    };

    NextendAnimationEditor.prototype.changeStopEvent = function () {
        nextend.animationManager.controller.refreshStopEvent(this.fields.stopEvent.element.val());
    };

    NextendAnimationEditor.prototype.changeRepeatable = function () {
        nextend.animationManager.controller.refreshRepeatable(this.fields.repeatable.element.val());
    };

    NextendAnimationEditor.prototype.changeInstantOut = function () {
        nextend.animationManager.controller.refreshInstantOut(this.fields.instantOut.element.val());
    };

    scope.NextendAnimationEditor = NextendAnimationEditor;

})
(n2, window);
