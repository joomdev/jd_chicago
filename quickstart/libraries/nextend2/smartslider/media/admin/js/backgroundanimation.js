;
(function (smartSlider, $, scope) {

    function NextendBackgroundAnimationManager() {
        this.type = 'backgroundanimation';
        NextendVisualManagerMultipleSelection.prototype.constructor.apply(this, arguments);
    };

    NextendBackgroundAnimationManager.prototype = Object.create(NextendVisualManagerMultipleSelection.prototype);
    NextendBackgroundAnimationManager.prototype.constructor = NextendBackgroundAnimationManager;

    NextendBackgroundAnimationManager.prototype.loadDefaults = function () {
        NextendVisualManagerMultipleSelection.prototype.loadDefaults.apply(this, arguments);
        this.type = 'backgroundanimation';
        this.labels = {
            visual: 'Background animation',
            visuals: 'Background animations'
        };
    };

    NextendBackgroundAnimationManager.prototype.initController = function () {
        return new NextendBackgroundAnimationEditorController();
    };

    NextendBackgroundAnimationManager.prototype.createVisual = function (visual, set) {
        return new NextendVisualWithSetRowMultipleSelection(visual, set, this);
    };

    scope.NextendBackgroundAnimationManager = NextendBackgroundAnimationManager;

})(nextend.smartSlider, n2, window);
