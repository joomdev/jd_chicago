(function ($, scope, undefined) {

    function NextendSmartSliderVimeoItem(slider, id, sliderid, parameters, hasImage) {
        this.readyDeferred = $.Deferred();

        this.slider = slider;
        this.playerId = id;

        this.parameters = $.extend({
            vimeourl: "//vimeo.com/144598279",
            center: 0,
            autoplay: "0",
            reset: "0",
            title: "1",
            byline: "1",
            portrait: "0",
            loop: "0",
            color: "00adef",
            volume: "-1"
        }, parameters);

        if (navigator.userAgent.toLowerCase().indexOf("android") > -1) {
            this.parameters.autoplay = 0;
        }

        if (this.parameters.autoplay == 1 || !hasImage) {
            this.ready($.proxy(this.initVimeoPlayer, this));
        } else {
            $("#" + this.playerId).on('click', $.proxy(function () {
                this.ready($.proxy(function () {
                    this.readyDeferred.done($.proxy(function () {
                        this.play();
                    }, this));
                    this.initVimeoPlayer();
                }, this));
            }, this));
        }
    };

    NextendSmartSliderVimeoItem.vimeoDeferred = null;

    NextendSmartSliderVimeoItem.prototype.ready = function (callback) {
        if (NextendSmartSliderVimeoItem.vimeoDeferred === null) {
            NextendSmartSliderVimeoItem.vimeoDeferred = $.getScript((window.location.protocol == "https:" ? 'https://secure-a.vimeocdn.com/js/froogaloop2.min.js' : 'http://a.vimeocdn.com/js/froogaloop2.min.js'));
        }
        NextendSmartSliderVimeoItem.vimeoDeferred.done(callback);
    };

    NextendSmartSliderVimeoItem.prototype.initVimeoPlayer = function () {
        var playerElement = n2('<iframe id="' + this.playerId + '_video" src="//player.vimeo.com/video/' + this.parameters.vimeocode + '?api=1&autoplay=0&player_id=' + this.playerId +
        '_video&title=' + this.parameters.title + '&byline=' + this.parameters.byline + '&portrait=' + this.parameters.portrait + '&color=' + this.parameters.color +
        '&loop=' + this.parameters.loop + '" style="position: absolute; top:0; left: 0; width: 100%; height: 100%;" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>');
        $("#" + this.playerId).append(playerElement);

        this.player = $f(playerElement[0]);
        this.playerElement = $(this.player.element);
        this.player.addEvent('ready', $.proxy(this.onReady, this));
    };

    NextendSmartSliderVimeoItem.prototype.onReady = function () {
        var volume = parseFloat(this.parameters.volume);
        if (volume >= 0) {
            this.setVolume(volume);
        }

        this.slideIndex = this.slider.findSlideIndexByElement(this.playerElement);

        if (this.parameters.center == 1) {
            this.onResize();

            this.slider.sliderElement.on('SliderResize', $.proxy(this.onResize, this))
        }
        var layer = this.playerElement.parent().parent();

        this.player.addEvent('play', $.proxy(function () {
            this.slider.sliderElement.trigger('mediaStarted', this);
            layer.triggerHandler('n2play');
        }, this));

        this.player.addEvent('pause', $.proxy(function () {
            layer.triggerHandler('n2pause');
        }));

        this.player.addEvent('finish', $.proxy(function () {
            this.slider.sliderElement.trigger('mediaEnded', this);
            layer.triggerHandler('n2stop');
        }, this));

        //pause video when slide changed
        this.slider.sliderElement.on("mainAnimationStart", $.proxy(function (e, mainAnimation, previousSlideIndex, currentSlideIndex, isSystem) {
            if (currentSlideIndex != this.slideIndex) {
                if (parseInt(this.parameters.reset)) {
                    this.reset();
                } else {
                    this.pause();
                }
            }
        }, this));

        if (this.parameters.autoplay == 1) {
            this.slider.visible($.proxy(this.initAutoplay, this));
        }
        this.readyDeferred.resolve();
    };

    NextendSmartSliderVimeoItem.prototype.onResize = function () {
        var controls = 52,
            parent = this.playerElement.parent(),
            width = parent.width() + controls,
            height = parent.height() + controls,
            aspectRatio = 16 / 9,
            css = {
                width: width,
                height: height,
                marginLeft: 0,
                marginTop: 0
            };
        if (width / height > aspectRatio) {
            css.height = width * aspectRatio;
            css.marginTop = (height - css.height) / 2;
        } else {
            css.width = height * aspectRatio;
            css.marginLeft = (width - css.width) / 2;
        }
        this.playerElement.css(css);
    };

    NextendSmartSliderVimeoItem.prototype.initAutoplay = function () {

        //change slide
        this.slider.sliderElement.on("mainAnimationComplete", $.proxy(function (e, mainAnimation, previousSlideIndex, currentSlideIndex, isSystem) {
            if (currentSlideIndex == this.slideIndex) {
                this.play();
            }
        }, this));

        if (this.slider.currentSlideIndex == this.slideIndex) {
            this.play();
        }
    };

    NextendSmartSliderVimeoItem.prototype.play = function () {
        this.slider.sliderElement.trigger('mediaStarted', this);
        this.player.api("play");
    };

    NextendSmartSliderVimeoItem.prototype.pause = function () {
        this.player.api("pause");
    };

    NextendSmartSliderVimeoItem.prototype.reset = function () {
        this.player.api("seekTo", 0);
    };

    NextendSmartSliderVimeoItem.prototype.setVolume = function (volume) {
        this.player.api('setVolume', volume);
    };

    scope.NextendSmartSliderVimeoItem = NextendSmartSliderVimeoItem;

})(n2, window);