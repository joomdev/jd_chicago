(function ($, scope, undefined) {

    function NextendElementImage(id, parameters) {
        this.element = $('#' + id);

        this.field = this.element.data('field');

        this.parameters = parameters;

        this.preview = $('#' + id + '_preview')
            .on('click', $.proxy(this.open, this));

        this.element.on('nextendChange', $.proxy(this.makePreview, this));

        this.button = $('#' + id + '_button').on('click', $.proxy(this.open, this));

        this.element.siblings('.n2-form-element-clear')
            .on('click', $.proxy(this.clear, this));
    };


    NextendElementImage.prototype = Object.create(NextendElement.prototype);
    NextendElementImage.prototype.constructor = NextendElementImage;

    NextendElementImage.prototype.clear = function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.val('');
    };

    NextendElementImage.prototype.val = function (value) {
        this.element.val(value);
        this.triggerOutsideChange();
    };

    NextendElementImage.prototype.makePreview = function () {
        var image = this.element.val();
        if (image.substr(0, 1) == '{') {
            this.preview.css('background-image', '');
        } else {
            this.preview.css('background-image', 'url(' + nextend.imageHelper.fixed(image) + ')');
        }
    };

    NextendElementImage.prototype.open = function (e) {
        e.preventDefault();
        nextend.imageHelper.openLightbox($.proxy(this.val, this));
    };

    NextendElementImage.prototype.edit = function (e) {
        e.preventDefault();
        e.stopPropagation();
        var imageSrc = nextend.imageHelper.fixed(this.element.val()),
            image = $('<img src="' + imageSrc + '" />');

        window.nextend.getFeatherEditor().done($.proxy(function () {
            nextend.featherEditor.launch({
                image: image.get(0),
                hiresUrl: imageSrc,
                onSave: $.proxy(this.aviarySave, this),
                onSaveHiRes: $.proxy(this.aviarySave, this)
            });
        }, this));
    };

    NextendElementImage.prototype.aviarySave = function (id, src) {

        NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(window.nextend.featherEditor.ajaxUrl, {
                nextendaction: 'saveImage'
            }),
            data: {
                aviaryUrl: src
            },
            dataType: 'json'
        })
            .done($.proxy(function (response) {
                this.val(nextend.imageHelper.make(response.data.image));
                nextend.featherEditor.close();
            }, this));
    };

    scope.NextendElementImage = NextendElementImage;
})(n2, window);