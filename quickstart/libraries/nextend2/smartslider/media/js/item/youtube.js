(function ($, scope, undefined) {

    function NextendSmartSliderYouTubeItem(slider, id, parameters, hasImage) {
        this.readyDeferred = $.Deferred();
        this.slider = slider;
        this.playerId = id;

        this.parameters = $.extend({
            youtubeurl: "//www.youtube.com/watch?v=MKmIwHAFjSU",
            youtubecode: "MKmIwHAFjSU",
            center: 0,
            autoplay: "1",
            theme: "dark",
            related: "1",
            vq: "default",
            volume: "-1",
            loop: 0
        }, parameters);

        if (navigator.userAgent.toLowerCase().indexOf("android") > -1) {
            this.parameters.autoplay = 0;
        }

        if (this.parameters.autoplay == 1 || !hasImage) {
            this.ready($.proxy(this.initYoutubePlayer, this));
        } else {
            $("#" + this.playerId).on('click', $.proxy(function () {
                this.ready($.proxy(function () {
                    this.readyDeferred.done($.proxy(function () {
                        this.play();
                    }, this));
                    this.initYoutubePlayer();
                }, this));
            }, this));
        }
    }

    NextendSmartSliderYouTubeItem.YTDeferred = null;
    NextendSmartSliderYouTubeItem.prototype.ready = function (callback) {
        if (NextendSmartSliderYouTubeItem.YTDeferred === null) {
            NextendSmartSliderYouTubeItem.YTDeferred = $.Deferred();
            window.onYouTubeIframeAPIReady = $.proxy(NextendSmartSliderYouTubeItem.YTDeferred.resolve, NextendSmartSliderYouTubeItem.YTDeferred);
            $.getScript("//www.youtube.com/iframe_api");
        }
        NextendSmartSliderYouTubeItem.YTDeferred.done(callback);
    };


    NextendSmartSliderYouTubeItem.prototype.initYoutubePlayer = function () {
        var player = $("#" + this.playerId);
        var layer = player.closest(".n2-ss-layer");

        var vars = {
            enablejsapi: 1,
            origin: window.location.protocol + "//" + window.location.host,
            theme: this.parameters.theme,
            modestbranding: 1,
            wmode: "opaque",
            rel: this.parameters.related,
            vq: this.parameters.vq,
            start: this.parameters.start
        };

        if (this.parameters.center == 1) {
            vars.controls = 0;
            vars.showinfo = 0;
        }
        if (this.parameters.controls != 1) {
            vars.autohide = 1;
            vars.controls = 0;
            vars.showinfo = 0;
        }

        if (+(navigator.platform.toUpperCase().indexOf('MAC') >= 0 && navigator.userAgent.search("Firefox") > -1))
            vars.html5 = 1;

        this.player = new YT.Player(this.playerId, {
            videoId: this.parameters.youtubecode,
            wmode: 'opaque',
            playerVars: vars,
            events: {
                onReady: $.proxy(this.onReady, this),
                onStateChange: $.proxy(function (state) {
                    switch (state.data) {
                        case YT.PlayerState.PLAYING:
                            this.slider.sliderElement.trigger('mediaStarted', this);
                            layer.triggerHandler('n2play');
                            break;
                        case YT.PlayerState.PAUSED:
                            layer.triggerHandler('n2pause');
                            break;
                        case YT.PlayerState.ENDED:
                            if (this.parameters.loop == 1) {
                                this.player.seekTo(0);
                                this.player.playVideo();
                            } else {
                                this.slider.sliderElement.trigger('mediaEnded', this);
                                layer.triggerHandler('n2stop');
                            }
                            break;

                    }
                }, this)
            }
        });

        this.playerElement = $("#" + this.playerId);

        this.slideIndex = this.slider.findSlideIndexByElement(this.playerElement);
        if (this.parameters.center == 1) {
            this.onResize();

            this.slider.sliderElement.on('SliderResize', $.proxy(this.onResize, this))
        }

    };

    NextendSmartSliderYouTubeItem.prototype.onReady = function (state) {

        var volume = parseFloat(this.parameters.volume);
        if (volume >= 0) {
            this.setVolume(volume);
        }

        if (this.parameters.autoplay == 1) {
            this.slider.visible($.proxy(this.initAutoplay, this));
        }

        //pause video when slide changed
        this.slider.sliderElement.on("mainAnimationStart", $.proxy(function (e, mainAnimation, previousSlideIndex, currentSlideIndex, isSystem) {
            if (currentSlideIndex != this.slideIndex) {
                this.pause();
            }
        }, this));
        this.readyDeferred.resolve();
    };

    NextendSmartSliderYouTubeItem.prototype.onResize = function () {
        var controls = 100,
            parent = this.playerElement.parent(),
            width = parent.width(),
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

    NextendSmartSliderYouTubeItem.prototype.initAutoplay = function () {

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

    NextendSmartSliderYouTubeItem.prototype.play = function () {
        if (this.isStopped()) {
            this.slider.sliderElement.trigger('mediaStarted', this);
            this.player.playVideo();
        }
    };

    NextendSmartSliderYouTubeItem.prototype.pause = function () {
        if (!this.isStopped()) {
            this.player.pauseVideo();
        }
    };

    NextendSmartSliderYouTubeItem.prototype.stop = function () {
        this.player.stopVideo();
    };

    NextendSmartSliderYouTubeItem.prototype.isStopped = function () {
        var state = this.player.getPlayerState();
        switch (state) {
            case -1:
            case 0:
            case 2:
            case 5:
                return true;
                break;
            default:
                return false;
                break;
        }
    };

    NextendSmartSliderYouTubeItem.prototype.setVolume = function (volume) {
        this.player.setVolume(volume * 100);
    };

    scope.NextendSmartSliderYouTubeItem = NextendSmartSliderYouTubeItem;

})(n2, window);