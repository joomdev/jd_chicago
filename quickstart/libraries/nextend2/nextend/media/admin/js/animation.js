;
(function ($, scope) {

    function NextendAnimationManager() {
        NextendVisualManagerSetsAndMore.prototype.constructor.apply(this, arguments);
    };

    NextendAnimationManager.prototype = Object.create(NextendVisualManagerSetsAndMore.prototype);
    NextendAnimationManager.prototype.constructor = NextendAnimationManager;

    NextendAnimationManager.prototype.loadDefaults = function () {
        NextendVisualManagerSetsAndMore.prototype.loadDefaults.apply(this, arguments);
        this.type = 'animation';
        this.labels = {
            visual: n2_('animation'),
            visuals: n2_('animations')
        };
        this.availableFeatures = {
            repeatable: 0,
            specialZero: 0,
            repeat: 0,
            playEvent: 0,
            pauseEvent: 0,
            stopEvent: 0,
            instantOut: 0
        };
    };

    NextendAnimationManager.prototype.initController = function () {
        return new NextendAnimationEditorController(this.parameters.renderer.modes);
    };

    NextendAnimationManager.prototype.show = function (features, data, saveCallback, showParameters) {
        this.currentFeatures = $.extend({}, this.availableFeatures, features);

        NextendVisualManagerSetsAndMore.prototype.show.call(this, data.animations, saveCallback, showParameters);

        this.controller.loadTransformOrigin(data.transformOrigin);

        if (this.currentFeatures.repeatable) {
            this.controller.featureRepeatable(1);
            if (!data.repeatable) {
                data.repeatable = 0;
            }
            this.controller.loadRepeatable(data.repeatable);
        } else {
            this.controller.featureRepeatable(0);
        }

        if (this.currentFeatures.specialZero) {
            this.controller.featureSpecialZero(1);
            if (!data.specialZero) {
                data.specialZero = 0;
            }
            this.controller.loadSpecialZero(data.specialZero);
        } else {
            this.controller.featureSpecialZero(0);
        }

        if (this.currentFeatures.instantOut) {
            this.controller.featureInstantOut(1);
            if (!data.instantOut) {
                data.instantOut = 0;
            }
            this.controller.loadInstantOut(data.instantOut);
        } else {
            this.controller.featureInstantOut(0);
        }

        if (this.currentFeatures.repeat) {
            this.controller.featureRepeat(1);
            if (!data.repeatCount) {
                data.repeatCount = 0;
            }
            this.controller.loadRepeatCount(data.repeatCount);

            if (!data.repeatStartDelay) {
                data.repeatStartDelay = 0;
            }
            this.controller.loadRepeatStartDelay(data.repeatStartDelay);
        } else {
            this.controller.featureRepeat(0);
        }

        if (this.currentFeatures.playEvent) {
            this.controller.featurePlayEvent(1);
            if (!data.playEvent) {
                data.playEvent = '';
            }
            this.controller.loadPlayEvent(data.playEvent);
        } else {
            this.controller.featurePlayEvent(0);
        }

        if (this.currentFeatures.pauseEvent) {
            this.controller.featurePauseEvent(1);
            if (!data.pauseEvent) {
                data.pauseEvent = '';
            }
            this.controller.loadPauseEvent(data.pauseEvent);
        } else {
            this.controller.featurePauseEvent(0);
        }

        if (this.currentFeatures.stopEvent) {
            this.controller.featureStopEvent(1);
            if (!data.stopEvent) {
                data.stopEvent = '';
            }
            this.controller.loadStopEvent(data.stopEvent);
        } else {
            this.controller.featureStopEvent(0);
        }

        if (data.animations.length == 0) {
            $.when(this.activeSet._loadVisuals())
                .done($.proxy(function () {
                    for (var k in this.activeSet.visuals) {
                        this.activeSet.visuals[k].activate();
                        break;
                    }
                }, this));
        }
    };

    NextendAnimationManager.prototype.setAndClose = function (data) {
        if (data.length == 1 && this.isEquivalent(data[0], this.controller.getEmptyAnimation())) {
            data = [];
        }
        var animationData = {
            transformOrigin: this.controller.transformOrigin,
            animations: data
        };

        if (this.currentFeatures.repeatable) {
            animationData.repeatable = this.controller.repeatable;
        }

        if (this.currentFeatures.specialZero) {
            animationData.specialZero = this.controller.specialZero;
        }

        if (this.currentFeatures.instantOut) {
            animationData.instantOut = this.controller.instantOut;
        }

        if (this.currentFeatures.repeat) {
            animationData.repeatCount = this.controller.repeatCount;
            animationData.repeatStartDelay = this.controller.repeatStartDelay;
        }

        if (this.currentFeatures.playEvent) {
            animationData.playEvent = this.controller.playEvent;
        }

        if (this.currentFeatures.pauseEvent) {
            animationData.pauseEvent = this.controller.pauseEvent;
        }

        if (this.currentFeatures.stopEvent) {
            animationData.stopEvent = this.controller.stopEvent;
        }

        this.$.trigger('save', animationData);
    };

    NextendAnimationManager.prototype.createVisual = function (visual, set) {
        return new NextendAnimation(visual, set, this);
    };

    NextendAnimationManager.prototype.setVisualAsStatic = function (e) {
        e.preventDefault();
        this.setAndClose(this.controller.get('save'));
        this.hide(e);
    };

    NextendAnimationManager.prototype.setMode = function (newMode) {
        NextendVisualManagerSetsAndMore.prototype.setMode.call(this, 'static');
    };

    NextendAnimationManager.prototype.getStaticData = function (data) {
        return data;
    };

    NextendAnimationManager.prototype.isEquivalent = function (a, b) {
        // Create arrays of property names
        var aProps = Object.getOwnPropertyNames(a);
        var bProps = Object.getOwnPropertyNames(b);

        // If number of properties is different,
        // objects are not equivalent
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

        // If we made it this far, objects
        // are considered equivalent
        return true;
    };

    scope.NextendAnimationManager = NextendAnimationManager;


    function NextendAnimation() {
        NextendVisualWithSetRow.prototype.constructor.apply(this, arguments);
    };

    NextendAnimation.prototype = Object.create(NextendVisualWithSetRow.prototype);
    NextendAnimation.prototype.constructor = NextendAnimation;

    NextendAnimation.prototype.activate = function (e) {
        if (typeof e !== 'undefined') {
            e.preventDefault();
        }
        this.visualManager.changeActiveVisual(this);
        if (typeof this.value.specialZero !== 'undefined') {
            this.visualManager.controller.loadSpecialZero(this.value.specialZero);
        }
        if (typeof this.value.transformOrigin !== 'undefined') {
            this.visualManager.controller.loadTransformOrigin(this.value.transformOrigin);
        }
        this.visualManager.controller.load(this.value.animations, false, this.visualManager.showParameters);
    };

    scope.NextendAnimation = NextendAnimation;

})(n2, window);
