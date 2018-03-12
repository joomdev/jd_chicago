(function ($, scope, undefined) {

    function NextendSmartSliderSimple(sliderElement, parameters) {

        this.type = 'simple';
        this.responsiveClass = 'NextendSmartSliderResponsiveSimple';

        parameters = $.extend({
            bgAnimations: 0,
            carousel: 1
        }, parameters);

        NextendSmartSliderAbstract.prototype.constructor.call(this, sliderElement, parameters);
    };

    NextendSmartSliderSimple.prototype = Object.create(NextendSmartSliderAbstract.prototype);
    NextendSmartSliderSimple.prototype.constructor = NextendSmartSliderSimple;

    NextendSmartSliderSimple.prototype.initMainAnimation = function () {

        if (nModernizr.csstransforms3d && nModernizr.csstransformspreserve3d && this.parameters.bgAnimations) {
            this.mainAnimation = new NextendSmartSliderFrontendBackgroundAnimation(this, this.parameters.mainanimation, this.parameters.bgAnimations);
        } else {
            this.mainAnimation = new NextendSmartSliderMainAnimationSimple(this, this.parameters.mainanimation);
        }
    };

    scope.NextendSmartSliderSimple = NextendSmartSliderSimple;

})(n2, window);