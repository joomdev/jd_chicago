var NextendSmartSliderAdminStorage = function () {
    /** @type {NextendSmartSliderAdminTimelineManager} */
    this.timelineManager = null;
    /** @type {NextendSmartSliderAdminTimelineControl} */
    this.timelineControl = null;
    /** @type {SmartSliderAdminSlide} */
    this.slide = null;
    /** @type {NextendSmartSliderAbstract} */
    this.frontend = null;
    /** @type {SmartSliderAdminGenerator} */
    this.generator = null;
    /** @type {NextendSmartSliderAdminSlideLayerManager} */
    this.layerManager = null;
    /** @type {NextendSmartSliderAdminLayoutHistory} */
    this.history = null;


    this.oneSecWidth = 200;
    this.oneSecMs = 1000;
    this.fps = 20;
    this.pxToFrame = this.oneSecWidth / this.fps;

    this.$currentSlideElement = null;
};

NextendSmartSliderAdminStorage.prototype.durationToOffsetX = function (sec) {
    return sec * this.oneSecWidth;
};

NextendSmartSliderAdminStorage.prototype.offsetXToDuration = function (px) {
    return px / this.oneSecWidth;
};

NextendSmartSliderAdminStorage.prototype.normalizeOffsetX = function (offsetX) {
    return Math.round(offsetX / this.pxToFrame) * this.pxToFrame;
};


NextendSmartSliderAdminStorage.prototype.startEditor = function (sliderElementID, slideContentElementID, isUploadDisabled, uploadUrl, uploadDir) {
    if (this.slide === null) {
        new SmartSliderAdminSlide(sliderElementID, slideContentElementID, isUploadDisabled, uploadUrl, uploadDir);
    }
    return this.slide;
};

window.nextend.pre = 'div#n2-ss-0 ';
window.nextend.smartSlider = new NextendSmartSliderAdminStorage();