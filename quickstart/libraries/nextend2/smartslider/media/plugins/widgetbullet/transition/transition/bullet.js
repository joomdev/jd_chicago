(function ($, scope, undefined) {
    function NextendSmartSliderWidgetBulletTransition(id, parameters) {

        this.slider = window[id];

        this.slider.started($.proxy(this.start, this, id, parameters));
    };

    NextendSmartSliderWidgetBulletTransition.prototype.start = function (id, parameters) {

        if (this.slider.sliderElement.data('bullet')) {
            return false;
        }
        this.slider.sliderElement.data('bullet', this);

        this.axis = 'horizontal';
        this.offset = 0;
        this.parameters = parameters;

        this.bar = this.slider.sliderElement.find('.nextend-bullet-bar');

        var event = 'universalclick';
        if (parameters.action == 'mouseenter') {
            event = 'mouseenter';
        }
        this.originalDots = this.dots = this.bar.find('div').on(event, $.proxy(this.onDotClick, this));
        this.slider.sliderElement
            .on('slideCountChanged', $.proxy(this.onSlideCountChanged, this))
            .on('sliderSwitchTo', $.proxy(this.onSlideSwitch, this));

        if (parameters.overlay == 0) {
            var side = false;
            switch (parameters.area) {
                case 1:
                    side = 'Top';
                    break;
                case 12:
                    side = 'Bottom';
                    break;
                case 5:
                    side = 'Left';
                    this.axis = 'vertical';
                    break;
                case 8:
                    side = 'Right';
                    this.axis = 'vertical';
                    break;
            }
            if (side) {
                this.offset = parseFloat(this.bar.data('offset'));
                this.slider.responsive.addStaticMargin(side, this);
            }
        }

        this.initThumbnails();
    };

    NextendSmartSliderWidgetBulletTransition.prototype.onDotClick = function (e) {
        this.slider.directionalChangeToReal(this.originalDots.index(e.currentTarget));
    };

    NextendSmartSliderWidgetBulletTransition.prototype.onSlideSwitch = function (e, targetSlideIndex) {
        this.dots.filter('.n2-active').removeClass('n2-active');
        this.dots.eq(targetSlideIndex).addClass('n2-active');
    };

    NextendSmartSliderWidgetBulletTransition.prototype.isVisible = function () {
        return this.bar.is(':visible');
    };

    NextendSmartSliderWidgetBulletTransition.prototype.getSize = function () {
        if (this.axis == 'horizontal') {
            return this.bar.height() + this.offset;
        }
        return this.bar.width() + this.offset;
    };

    NextendSmartSliderWidgetBulletTransition.prototype.initThumbnails = function () {
        if (this.parameters.thumbnails.length > 0) {
            this.dots.each($.proxy(function (i, el) {
                if (this.parameters.thumbnails[i] != '') {
                    $(el).on({
                        universalenter: $.proxy(this.showThumbnail, this, i)/*,
                         universalleave: $.proxy(this.hideThumbnail, this, i)*/
                    }, {
                        leaveOnSecond: true
                    })
                }
            }, this));
        }
    };

    NextendSmartSliderWidgetBulletTransition.prototype.showThumbnail = function (i, e) {
        var thumbnail = this.getThumbnail(i);

        NextendTween.to(thumbnail, 0.3, {
            opacity: 1
        }).play();

        this.originalDots.eq(i).on('universalleave', $.proxy(this.hideThumbnail, this, thumbnail));
    };

    NextendSmartSliderWidgetBulletTransition.prototype.hideThumbnail = function (thumbnail, e) {
        e.stopPropagation();
        NextendTween.to(thumbnail, 0.3, {
            opacity: 0,
            onComplete: function () {
                thumbnail.remove();
            }
        }).play();
    };

    NextendSmartSliderWidgetBulletTransition.prototype.getThumbnail = function (i) {
        var target = this.originalDots.eq(i);
        var targetOffset = target.offset(),
            targetW = target.outerWidth(),
            targetH = target.outerHeight();

        var thumbnail = $('<div/>').append($('<div/>').css({
            width: this.parameters.thumbnailWidth,
            height: this.parameters.thumbnailHeight,
            backgroundImage: 'url("' + this.parameters.thumbnails[i] + '")',
            backgroundSize: 'cover'
        }))
            .addClass(this.parameters.thumbnailStyle)
            .css({
                position: 'absolute',
                opacity: 0,
                zIndex: 10000000
            }).appendTo('body');

        switch (this.parameters.thumbnailPosition) {
            case 'right':
                thumbnail.css({
                    left: targetOffset.left + targetW,
                    top: targetOffset.top + targetH / 2 - thumbnail.outerHeight(true) / 2
                });
                break;
            case 'left':
                thumbnail.css({
                    left: targetOffset.left - thumbnail.outerWidth(true),
                    top: targetOffset.top + targetH / 2 - thumbnail.outerHeight(true) / 2
                });
                break;
            case 'top':
                thumbnail.css({
                    left: targetOffset.left + targetW / 2 - thumbnail.outerWidth(true) / 2,
                    top: targetOffset.top - thumbnail.outerHeight(true)
                });
                break;
            case 'bottom':
                thumbnail.css({
                    left: targetOffset.left + targetW / 2 - thumbnail.outerWidth(true) / 2,
                    top: targetOffset.top + targetH
                });
                break;
        }

        target.data('thumbnail', thumbnail);
        return thumbnail;
    };

    NextendSmartSliderWidgetBulletTransition.prototype.onSlideCountChanged = function (e, newCount, slidesInGroup) {
        this.dots = $();
        for (var i = 0; i < this.originalDots.length; i++) {
            if (i % slidesInGroup == 0) {
                this.dots = this.dots.add(this.originalDots.eq(i).css("display", ""));
            } else {
                this.originalDots.eq(i).css("display", "none");
            }
        }
        if (this.parameters.numeric) {
            this.dots.each(function (i, el) {
                el.innerHTML = i + 1;
            });
        }
    };

    scope.NextendSmartSliderWidgetBulletTransition = NextendSmartSliderWidgetBulletTransition;
})(n2, window);