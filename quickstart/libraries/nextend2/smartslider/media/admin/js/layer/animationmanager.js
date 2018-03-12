(function (smartSlider, $, scope, undefined) {
    function LayerAnimationManager(layerEditor) {

        this.layerEditor = layerEditor;

        this.createGroup('in', n2_('in'), '#layer-animation-chain-in');
        this.lists = this.in.list;

        this.createGroup('loop', n2_('loop'), '#layer-animation-chain-loop');
        this.createGroup('out', n2_('out'), '#layer-animation-chain-out');
        this.lists = this.lists.add(this.loop.list).add(this.out.list);


        smartSlider.layerAnimationManager = this;
    };

    LayerAnimationManager.prototype.createGroup = function (identifier, label, container) {
        container = $(container);
        var header = $('<div class="n2-sidebar-row n2-sidebar-header-bg n2-form-dark n2-sets-header"><div class="n2-table"><div class="n2-tr"><div class="n2-td"><div class="n2-h3 n2-uc">' + label + '</div></div><div style="text-align: ' + (nextend.isRTL() ? 'left' : 'right') + ';" class="n2-td"></div></div></div></div>').appendTo(container),
            buttonPlaceholder = header.find('.n2-td').eq(1);

        this[identifier] = {
            container: container,
            header: header,
            list: $('<ul class="n2-list n2-h4 n2-list-orderable n2-ss-animation-list"></ul>')
                .on('click', $.proxy(this.editGroup, this, identifier))
                .data('group', identifier)
                .appendTo(container),
            add: this.getAddButton(identifier, n2_('Add')).appendTo(buttonPlaceholder),
            clear: this.getClearButton(identifier).appendTo(buttonPlaceholder)
        };
    };

    LayerAnimationManager.prototype.getAddButton = function (identifier, label) {
        var button = $('<a href="#" class="n2-button n2-button-medium n2-button-green n2-h5 n2-uc">' + label + '</a>')
            .on('click', $.proxy(this.createAnimation, this, identifier));
        return button;
    };

    LayerAnimationManager.prototype.getClearButton = function (identifier) {
        var button = $('<a href="#" class="n2-button n2-button-medium n2-button-grey n2-h5 n2-uc">' + n2_('Clear') + '</a>')
            .on('click', $.proxy(this.clear, this, identifier));
        return button;
    };

    LayerAnimationManager.prototype.getActiveLayer = function () {
        return this.layerEditor.layerList[this.layerEditor.activeLayerIndex];
    };

    LayerAnimationManager.prototype.editGroup = function (identifier, e) {
        var index = 0;
        if (e) {
            e.preventDefault();
            index = $(e.target).closest('.n2-ss-animation-row').index();
        }
        if (index != -1) {
            var layerAnimations = this.getActiveLayer().animation;
            layerAnimations.edit(identifier, index);
        }
    };

    LayerAnimationManager.prototype.clear = function (group, e) {
        if (e) {
            e.preventDefault();
        }

        this.getActiveLayer().animation.clear(group);
    };

    LayerAnimationManager.prototype.createAnimation = function (group, e) {
        if (e) {
            e.preventDefault();
        }
        var activeLayer = this.getActiveLayer(),
            $layer = activeLayer.layer,
            animationManager = nextend.animationManager;

        animationManager.controller
            .setPreviewSize($layer.width(), $layer.height())
            .setGroup(group);

        var features = {
            repeatable: 1
        };

        if (group == 'in') {
            features.specialZero = 1;
            features.playEvent = 1;
            animationManager.changeSetById(1000);
            animationManager.setTitle(n2_('In animation'));
        } else if (group == 'loop') {
            features.repeat = 1;

            features.playEvent = 1;
            features.pauseEvent = 1;
            features.stopEvent = 1;
            animationManager.changeSetById(1200);
            animationManager.setTitle(n2_('Loop animation'));
        } else if (group == 'out') {
            features.playEvent = 1;
            features.instantOut = 1;
            animationManager.changeSetById(1000);
            animationManager.setTitle(n2_('Out animation'));
        }

        animationManager.show(features, {
            animations: [],
            transformOrigin: '50|*|50|*|0',
            specialZero: activeLayer.animation.data.specialZero,
            repeatCount: activeLayer.animation.data.repeatCount,
            repeatDelay: activeLayer.animation.data.repeatDelay,
            playEvent: '',
            pauseEvent: '',
            stopEvent: '',
            repeatable: activeLayer.animation.data.repeatable,
            instantOut: activeLayer.animation.data.instantOut
        }, $.proxy(this.storeNewAnimation, this, group), {
            previewMode: false,
            previewHTML: false
        });
    };

    LayerAnimationManager.prototype.storeNewAnimation = function (group, e, animationStack) {
        if (animationStack.animations.length > 0) {
            var layerAnimations = this.getActiveLayer().animation;
            layerAnimations.setTransformOrigin(group, animationStack.transformOrigin);
            layerAnimations.setRepeatable(animationStack.repeatable);

            if (group == 'in') {
                layerAnimations.setSpecialZero(group, animationStack.specialZero);
                layerAnimations.setEvent(group, 'PlayEvent', animationStack.playEvent);
            } else if (group == 'loop') {
                layerAnimations.setRepeatCount(group, animationStack.repeatCount);
                layerAnimations.setRepeatStartDelay(group, animationStack.repeatStartDelay);

                layerAnimations.setEvent(group, 'PlayEvent', animationStack.playEvent);
                layerAnimations.setEvent(group, 'PauseEvent', animationStack.pauseEvent);
                layerAnimations.setEvent(group, 'StopEvent', animationStack.stopEvent);
            } else if (group == 'out') {
                layerAnimations.setEvent(group, 'PlayEvent', animationStack.playEvent);
                layerAnimations.setInstantOut(animationStack.instantOut);
            }

            for (var i = 0; i < animationStack.animations.length; i++) {
                layerAnimations.addAnimation(group, animationStack.animations[i]);
            }

            this.update(group);
            $(window).triggerHandler('AnimationAdded');
        }

        this.update(group);
    };

    /**
     * @param animations {NextendSmartSliderLayerAnimations}
     */
    LayerAnimationManager.prototype.activateAnimations = function (animations) {
        animations.inRows.prependTo(this.in.list);

        animations.loopRows.prependTo(this.loop.list);
        animations.outRows.prependTo(this.out.list);


        this.update('in');
        this.update('loop');
        this.update('out');

    };

    LayerAnimationManager.prototype.update = function (group) {
        if (this[group].list.children().length) {
            this[group].add.css('display', 'none');
            this[group].clear.css('display', '');
        } else {
            this[group].add.css('display', '');
            this[group].clear.css('display', 'none');
        }
    };

    scope.NextendSmartSliderLayerAnimationManager = LayerAnimationManager;

})(nextend.smartSlider, n2, window);