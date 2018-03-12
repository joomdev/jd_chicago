(function ($, scope, undefined) {
    function NextendSmartSliderBackgroundAnimationAbstract(sliderBackgroundAnimation, currentImage, nextImage, animationProperties, durationMultiplier, reversed) {

        this.durationMultiplier = durationMultiplier;

        this.original = {
            currentImage: currentImage,
            nextImage: nextImage
        };

        this.animationProperties = animationProperties;

        this.reversed = reversed;

        this.timeline = sliderBackgroundAnimation.timeline;

        this.containerElement = sliderBackgroundAnimation.bgAnimationElement;

        this.shiftedBackgroundAnimation = sliderBackgroundAnimation.parameters.shiftedBackgroundAnimation;

        this.clonedImages = {};

    };

    NextendSmartSliderBackgroundAnimationAbstract.prototype.postSetup = function () {
    };

    NextendSmartSliderBackgroundAnimationAbstract.prototype.ended = function () {

    };

    NextendSmartSliderBackgroundAnimationAbstract.prototype.placeNextImage = function () {
        this.clonedImages.nextImage = this.original.nextImage.clone().css({
            position: 'absolute',
            top: 0,
            left: 0
        });

        this.containerElement.append(this.clonedImages.nextImage);
    };

    NextendSmartSliderBackgroundAnimationAbstract.prototype.placeCurrentImage = function () {
        this.clonedImages.currentImage = this.original.currentImage.clone().css({
            position: 'absolute',
            top: 0,
            left: 0
        });

        this.containerElement.append(this.clonedImages.currentImage);
    };

    NextendSmartSliderBackgroundAnimationAbstract.prototype.hideOriginals = function () {
        this.original.currentImage.css('opacity', 0);
        this.original.nextImage.css('opacity', 0);
    };

    NextendSmartSliderBackgroundAnimationAbstract.prototype.resetAll = function () {
        this.original.currentImage.css('opacity', 1);
        this.original.nextImage.css('opacity', 1);
        this.containerElement.html('');
    };

    NextendSmartSliderBackgroundAnimationAbstract.prototype.getExtraDelay = function () {
        return 0;
    };

    scope.NextendSmartSliderBackgroundAnimationAbstract = NextendSmartSliderBackgroundAnimationAbstract;
})(n2, window);
