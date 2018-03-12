(function ($, scope, undefined) {

    function ItemParserVimeo() {
        NextendSmartSliderItemParser.apply(this, arguments);
    };

    ItemParserVimeo.prototype = Object.create(NextendSmartSliderItemParser.prototype);
    ItemParserVimeo.prototype.constructor = ItemParserVimeo;

    ItemParserVimeo.prototype.added = function () {
        this.needFill = ['vimeourl'];

        nextend.smartSlider.generator.registerField($('#item_vimeovimeourl'));
    };

    ItemParserVimeo.prototype.getName = function (data) {
        return data.vimeourl;
    };

    ItemParserVimeo.prototype.parseAll = function (data, item) {
        var vimeoChanged = item.values.vimeourl != data.vimeourl;

        NextendSmartSliderItemParser.prototype.parseAll.apply(this, arguments);

        if (data.image == '') {
            data.image = '$system$/images/placeholder/video.png';
        }

        data.image = nextend.imageHelper.fixed(data.image);

        if (vimeoChanged && data.vimeourl != '') {
            var vimeoRegexp = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/,
                vimeoMatch = data.vimeourl.match(vimeoRegexp);

            var videoCode = false;
            if (vimeoMatch) {
                videoCode = vimeoMatch[3];
            } else if (data.vimeourl.match(/^[0-9]+$/)) {
                videoCode = data.vimeourl;
            }

            if (videoCode) {
                NextendAjaxHelper.getJSON('https://vimeo.com/api/v2/video/' + encodeURI(videoCode) + '.json').done($.proxy(function (data) {
                    $('#item_vimeoimage').val(data[0].thumbnail_large).trigger('change');
                }, this)).fail(function (data) {
                    nextend.notificationCenter.error(data.responseText);
                });
            } else {
                nextend.notificationCenter.error('The provided URL does not match any known Vimeo url or code!');
            }
        }
    };

    ItemParserVimeo.prototype.fitLayer = function (item) {
        return true;
    };

    scope.NextendSmartSliderItemParser_vimeo = ItemParserVimeo;
})(n2, window);