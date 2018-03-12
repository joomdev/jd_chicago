(function (smartSlider, $, scope, undefined) {
    "use strict";
    function LayerAnimation(animations, group, data) {
        this.$ = $(this);
        this.animations = animations;
        this.group = group;
        this.data = data;

        this.row = $('<li class="n2-ss-animation-row"></li>')
            .data('animation', this);

        var handle = $('<div class="n2-ss-animation-title"></div>')
            .appendTo(this.row);

        this.label = $('<span>' + this.data.name + '</span>')
            .appendTo(handle);

        var actions = $('<div class="n2-actions"></div>')
            .append($('<a onclick="return false;" href="#"><i class="n2-i n2-i-delete n2-i-grey-opacity"></i></a>')
                .on('click', $.proxy(this.delete, this)))
            .appendTo(handle);

    };

    LayerAnimation.prototype.getRow = function () {
        return this.row;
    };

    LayerAnimation.prototype.edit = function () {
        this.animations.edit(this.group, this.animations[this.group + 'Rows'].index(this.row));
    };

    LayerAnimation.prototype.save = function (data) {
        if (data !== false) {
            this.data = data;
            this.label.html(data.name);

            this.$.trigger('animationChanged');
        }
    };

    LayerAnimation.prototype.delete = function (e) {
        if (e) {
            e.stopPropagation();
        }
        this.row.remove();
        this.animations.removeAnimation(this);
        smartSlider.layerAnimationManager.update(this.group);

        this.$.trigger('animationDeleted');
    };

    LayerAnimation.prototype.setDelay = function (newDelay) {
        this.data.delay = newDelay;
    };

    LayerAnimation.prototype.setDuration = function (newDuration) {
        this.data.duration = newDuration;
    };

    scope.NextendSmartSliderLayerAnimation = LayerAnimation;

})(nextend.smartSlider, n2, window);