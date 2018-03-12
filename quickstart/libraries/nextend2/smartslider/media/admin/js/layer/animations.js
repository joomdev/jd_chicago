(function (smartSlider, $, scope, undefined) {

    var defaults = {
        repeatable: 0,

        in: [],
        specialZeroIn: 0,
        transformOriginIn: '50|*|50|*|0',
        inPlayEvent: '',

        loop: [],
        repeatCount: 0,
        repeatStartDelay: 0,
        transformOriginLoop: '50|*|50|*|0',
        loopPlayEvent: '',
        loopPauseEvent: '',
        loopStopEvent: '',

        out: [],
        transformOriginOut: '50|*|50|*|0',
        outPlayEvent: '',
        instantOut: 1

    };

    function LayerAnimations(layer) {
        this._loaded = false;
        this.active = false;
        this.layer = layer;

        this.data = null;

        layer.layer.data('adminLayerAnimations', this);

        this.inRows = $();
        this.loopRows = $();
        this.outRows = $();

        //this.load();
    };

    /**
     * Here should we remove the nodes what we have added previously
     */
    LayerAnimations.prototype.deActivate = function () {

        this.active = false;
        this.inRows.detach();
        this.loopRows.detach();
        this.outRows.detach();
    };

    /**
     * Add nodes to the layer animation panel when it is activated
     */
    LayerAnimations.prototype.activate = function () {

        // Lazy load...
        //this.load();

        this.active = true;

        smartSlider.layerAnimationManager.activateAnimations(this);
    };

    LayerAnimations.prototype.addAnimation = function (group, data) {
        var animation = new NextendSmartSliderLayerAnimation(this, group, data),
            row = animation.getRow();

        this[group + 'Rows'] = this[group + 'Rows']
            .add(row);

        if (this.active) {
            row.appendTo(smartSlider.layerAnimationManager[group].list);
        }

        this.layer.$.trigger('layerAnimationAdded', [group, animation]);
    };

    /**
     * @param {NextendSmartSliderLayerAnimation} animationObject
     */
    LayerAnimations.prototype.removeAnimation = function (animationObject) {
        var group = animationObject.group;
        this[group + 'Rows'] = this[group + 'Rows'].not(animationObject.row);
    };

    LayerAnimations.prototype.clear = function (group) {
        var rows = this[group + 'Rows'];
        for (var i = 0; i < rows.length; i++) {
            rows.eq(i).data('animation').delete();
        }
    };

    LayerAnimations.prototype.edit = function (group, index) {
        var animations = [];
        for (var i = 0; i < this[group + 'Rows'].length; i++) {
            animations.push(this[group + 'Rows'].eq(i).data('animation').data);
        }

        var animationManager = nextend.animationManager;
        animationManager.controller
            .setPreviewSize(this.layer.layer.width(), this.layer.layer.height())
            .setGroup(group);

        var features = {
                repeatable: 1
            },
            data = {
                animations: animations,
                transformOrigin: this.data['transformOrigin' + this.ucfirst(group)],
                repeatable: this.data.repeatable
            };

        if (group == 'in') {
            features.specialZero = 1;
            data.specialZero = this.data.specialZeroIn;

            features.playEvent = 1;
            data.playEvent = this.data.inPlayEvent;
            animationManager.changeSetById(1000);
            animationManager.setTitle(n2_('In animation'));
        } else if (group == 'loop') {
            features.repeat = 1;
            data.repeatCount = this.data.repeatCount;
            data.repeatStartDelay = this.data.repeatStartDelay;

            features.playEvent = 1;
            data.playEvent = this.data.loopPlayEvent;
            features.pauseEvent = 1;
            data.pauseEvent = this.data.loopPauseEvent;
            features.stopEvent = 1;
            data.stopEvent = this.data.loopStopEvent;
            animationManager.changeSetById(1200);
            animationManager.setTitle(n2_('Loop animation'));
        } else if (group == 'out') {
            features.playEvent = 1;
            features.instantOut = 1;
            data.playEvent = this.data.outPlayEvent;
            data.instantOut = this.data.instantOut;
            animationManager.changeSetById(1000);
            animationManager.setTitle(n2_('Out animation'));
        }

        animationManager.show(features, data, $.proxy(this.storeAnimations, this, group), {
            previewMode: false,
            previewHTML: false
        });
        if (index > 0) {
            animationManager.controller.tabField.options.eq(index).trigger('click');
        }
    };

    LayerAnimations.prototype.storeAnimations = function (group, e, animationStack) {
        var i = 0,
            rows = this[group + 'Rows'];

        this.setTransformOrigin(group, animationStack.transformOrigin);
        this.setRepeatable(animationStack.repeatable);

        if (group == 'in') {
            this.setSpecialZero(group, animationStack.specialZero);
            this.setEvent(group, 'PlayEvent', animationStack.playEvent);
        } else if (group == 'loop') {
            this.setRepeatCount(group, animationStack.repeatCount);
            this.setRepeatStartDelay(group, animationStack.repeatStartDelay);
            this.setEvent(group, 'PlayEvent', animationStack.playEvent);
            this.setEvent(group, 'PauseEvent', animationStack.pauseEvent);
            this.setEvent(group, 'StopEvent', animationStack.stopEvent);
        } else if (group == 'out') {
            this.setEvent(group, 'PlayEvent', animationStack.playEvent);
            this.setInstantOut(animationStack.instantOut);
        }

        for (; i < animationStack.animations.length && i < rows.length; i++) {
            rows.eq(i).data('animation').save(animationStack.animations[i]);
        }
        for (; i < animationStack.animations.length; i++) {
            this.addAnimation(group, animationStack.animations[i]);
        }
        for (; i < rows.length; i++) {
            rows.eq(i).data('animation').delete();
        }

        smartSlider.layerAnimationManager.update(group);
    };

    LayerAnimations.prototype.load = function () {
        if (this._loaded === false) {
            var animationsRaw = this.layer.layer.data('animations');

            this.data = {};

            $.extend(this.data, defaults);

            if (typeof animationsRaw !== 'undefined') {
                $.extend(this.data, $.parseJSON(Base64.decode(animationsRaw)));
            }

            this._load('in');
            this._load('loop');
            this._load('out');


            this._loaded = true;
        }
    };

    LayerAnimations.prototype._load = function (group) {

        if (typeof this.data[group] !== 'undefined') {
            for (var i = 0; i < this.data[group].length; i++) {
                this.addAnimation(group, this.data[group][i]);
            }
            delete this.data[group];
        }
    };

    LayerAnimations.prototype.getAnimationsCode = function () {
        if (this._loaded === false) {
            return this.layer.layer.data('animations');
        } else {
            var animations = $.extend({}, this.data, {
                in: [],
                loop: [],
                out: []
            });

            for (var i = 0; i < this.inRows.length; i++) {
                var animation = this.inRows.eq(i).data('animation');
                animations.in.push(animation.data);
            }

            for (var i = 0; i < this.loopRows.length; i++) {
                var animation = this.loopRows.eq(i).data('animation');
                animations.loop.push(animation.data)
            }

            for (var i = 0; i < this.outRows.length; i++) {
                var animation = this.outRows.eq(i).data('animation');
                animations.out.push(animation.data)
            }

            return Base64.encode(JSON.stringify(animations));
        }
    };

    LayerAnimations.prototype.loadData = function (data) {
        this.clear('in');
        this.clear('loop');
        this.clear('out');

        this.data = {};
        $.extend(this.data, defaults);
        $.extend(this.data, data);


        this._load('in');
        this._load('loop');
        this._load('out');
    };

    LayerAnimations.prototype.getData = function () {
        var animations = $.extend({}, this.data, {
            in: [],
            loop: [],
            out: []
        });

        for (var i = 0; i < this.inRows.length; i++) {
            var animation = this.inRows.eq(i).data('animation');
            animations.in.push($.extend(true, {}, animation.data));
        }

        for (var i = 0; i < this.loopRows.length; i++) {
            var animation = this.loopRows.eq(i).data('animation');
            animations.loop.push($.extend(true, {}, animation.data))
        }

        for (var i = 0; i < this.outRows.length; i++) {
            var animation = this.outRows.eq(i).data('animation');
            animations.out.push($.extend(true, {}, animation.data))
        }
        return animations;
    };

    LayerAnimations.prototype.setSpecialZero = function (group, value) {
        value = parseInt(value) ? 1 : 0;
        if (value != this.data['transformOrigin' + this.ucfirst(group)]) {
            this.data.specialZeroIn = value;
            this.layer.$.trigger('layerAnimationSpecialZeroInChanged');
        }
    };

    LayerAnimations.prototype.setRepeatCount = function (group, value) {
        this.data.repeatCount = value;
    };

    LayerAnimations.prototype.setRepeatStartDelay = function (group, value) {
        this.data.repeatStartDelay = value;
    };

    LayerAnimations.prototype.setEvent = function (group, event, value) {
        this.data[group + event] = value;
    };

    LayerAnimations.prototype.setTransformOrigin = function (group, value) {
        this.data['transformOrigin' + this.ucfirst(group)] = value;
    };

    LayerAnimations.prototype.setRepeatable = function (value) {
        this.data.repeatable = parseInt(value) ? 1 : 0;
    };

    LayerAnimations.prototype.setInstantOut = function (value) {
        this.data.instantOut = parseInt(value) ? 1 : 0;
    };

    LayerAnimations.prototype.ucfirst = function (string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };

    scope.NextendSmartSliderLayerAnimations = LayerAnimations;

})(nextend.smartSlider, n2, window);