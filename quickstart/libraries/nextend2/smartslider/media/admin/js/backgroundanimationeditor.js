;
(function ($, scope) {

    function NextendBackgroundAnimationEditorController() {
        this.parameters = {
            shiftedBackgroundAnimation: 0
        };
        NextendVisualEditorController.prototype.constructor.call(this, false);

        this.bgAnimationElement = $('.n2-bg-animation');
        this.slides = $('.n2-bg-animation-slide');
        this.bgImages = $('.n2-bg-animation-slide-bg');
        NextendTween.set(this.bgImages, {
            rotationZ: 0.0001
        });

        this.directionTab = new NextendElementRadio('n2-background-animation-preview-tabs', ['0', '1']);
        this.directionTab.element.on('nextendChange.n2-editor', $.proxy(this.directionTabChanged, this));

        if (!nModernizr.csstransforms3d || !nModernizr.csstransformspreserve3d) {
            nextend.notificationCenter.error('Background animations are not available in your browser. It works if the <i>transform-style: preserve-3d</i> feature available. ')
        }
    };

    NextendBackgroundAnimationEditorController.prototype = Object.create(NextendVisualEditorController.prototype);
    NextendBackgroundAnimationEditorController.prototype.constructor = NextendBackgroundAnimationEditorController;

    NextendBackgroundAnimationEditorController.prototype.loadDefaults = function () {
        NextendVisualEditorController.prototype.loadDefaults.call(this);
        this.type = 'backgroundanimation';
        this.current = 0;
        this.animationProperties = false;
        this.direction = 0;
    };

    NextendBackgroundAnimationEditorController.prototype.get = function () {
        return null;
    };

    NextendBackgroundAnimationEditorController.prototype.load = function (visual, tabs, mode, preview) {
        this.lightbox.addClass('n2-editor-loaded');
    };

    NextendBackgroundAnimationEditorController.prototype.setTabs = function (labels) {

    };

    NextendBackgroundAnimationEditorController.prototype.directionTabChanged = function () {
        this.direction = parseInt(this.directionTab.element.val());
    };

    NextendBackgroundAnimationEditorController.prototype.start = function () {
        if (this.animationProperties) {
            if (!this.timeline) {
                this.next();
            } else {
                this.timeline.play();
            }
        }
    };

    NextendBackgroundAnimationEditorController.prototype.pause = function () {
        if (this.timeline) {
            this.timeline.pause();
        }
    };

    NextendBackgroundAnimationEditorController.prototype.next = function () {
        this.timeline = new NextendTimeline({
            paused: true,
            onComplete: $.proxy(this.ended, this)
        });
        var current = this.bgImages.eq(this.current),
            next = this.bgImages.eq(1 - this.current);

        if (nModernizr.csstransforms3d && nModernizr.csstransformspreserve3d) {
            this.currentAnimation = new window['NextendSmartSliderBackgroundAnimation' + this.animationProperties.type](this, current, next, this.animationProperties, 1, this.direction);

            this.slides.eq(this.current).css('zIndex', 2);
            this.slides.eq(1 - this.current).css('zIndex', 3);

            this.timeline.to(this.slides.eq(this.current), 0.5, {
                opacity: 0
            }, this.currentAnimation.getExtraDelay());

            this.timeline.to(this.slides.eq(1 - this.current), 0.5, {
                opacity: 1
            }, this.currentAnimation.getExtraDelay());


            this.currentAnimation.postSetup();

        } else {

            this.timeline.to(this.slides.eq(this.current), 1.5, {
                opacity: 0
            }, 0);

            this.timeline.to(this.slides.eq(1 - this.current), 1.5, {
                opacity: 1
            }, 0);
        }
        this.current = 1 - this.current;
        this.timeline.play();
    };

    NextendBackgroundAnimationEditorController.prototype.ended = function () {
        if (this.currentAnimation) {
            this.currentAnimation.ended();
        }
        this.next();
    };

    NextendBackgroundAnimationEditorController.prototype.setAnimationProperties = function (animationProperties) {
        var lastAnimationProperties = this.animationProperties;
        this.animationProperties = animationProperties;
        if (!lastAnimationProperties) {
            this.next();
        }
    };

    scope.NextendBackgroundAnimationEditorController = NextendBackgroundAnimationEditorController;

})
(n2, window);
