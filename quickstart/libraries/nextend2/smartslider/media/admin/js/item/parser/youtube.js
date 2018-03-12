(function ($, scope, undefined) {

    function ItemParserYouTube() {
        NextendSmartSliderItemParser.apply(this, arguments);
    };

    ItemParserYouTube.prototype = Object.create(NextendSmartSliderItemParser.prototype);
    ItemParserYouTube.prototype.constructor = ItemParserYouTube;

    ItemParserYouTube.prototype.added = function () {
        this.needFill = ['youtubeurl'];

        nextend.smartSlider.generator.registerField($('#item_youtubeyoutubeurl'));
    };

    ItemParserYouTube.prototype.getName = function (data) {
        return data.youtubeurl;
    };

    ItemParserYouTube.prototype.parseAll = function (data, item) {

        var youTubeChanged = item.values.youtubeurl != data.youtubeurl;

        NextendSmartSliderItemParser.prototype.parseAll.apply(this, arguments);

        if (data.image == '') {
            data.image = '$system$/images/placeholder/video.png';
        }

        data.image = nextend.imageHelper.fixed(data.image);

        if (youTubeChanged) {
            var youtubeRegexp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/,
                youtubeMatch = data.youtubeurl.match(youtubeRegexp);

            if (youtubeMatch) {
                NextendAjaxHelper.getJSON('https://www.googleapis.com/youtube/v3/videos?id=' + encodeURI(youtubeMatch[2]) + '&part=snippet&key=AIzaSyC3AolfvPAPlJs-2FgyPJdEEKS6nbPHdSM').done($.proxy(function (data) {
                    if (data.items.length) {

                        var thumbnails = data.items[0].snippet.thumbnails,
                            thumbnail = thumbnails.maxres || thumbnails.standard || thumbnails.high || thumbnails.medium || thumbnails.default;

                        $('#item_youtubeimage').val(thumbnail.url).trigger('change');
                    }
                }, this)).fail(function (data) {
                    nextend.notificationCenter.error(data.error.errors[0].message);
                });
            } else {
                nextend.notificationCenter.error('The provided URL does not match any known YouTube url or code!');
            }
        }
    };

    ItemParserYouTube.prototype.fitLayer = function (item) {
        return true;
    };

    scope.NextendSmartSliderItemParser_youtube = ItemParserYouTube;
})(n2, window);