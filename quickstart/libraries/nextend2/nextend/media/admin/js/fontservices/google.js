;
(function ($, scope) {

    function NextendFontServiceGoogle(style, subset, fonts) {
        this.style = style;
        this.subset = subset;
        this.fonts = fonts;
        $(window).on('n2Family', $.proxy(this.loadFamily, this));
    }

    NextendFontServiceGoogle.prototype.loadFamily = function (e, family) {

        if ($.inArray(family, this.fonts) != -1) {
            $('<link />').attr({
                rel: 'stylesheet',
                type: 'text/css',
                href: '//fonts.googleapis.com/css?family=' + encodeURIComponent(family + ':' + this.style) + '&subset=' + encodeURIComponent(this.subset)
            }).appendTo($('head'));
        }
    };

    scope.NextendFontServiceGoogle = NextendFontServiceGoogle;
})(n2, window);