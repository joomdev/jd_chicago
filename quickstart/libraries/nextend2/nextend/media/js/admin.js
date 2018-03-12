n2.extend(window.nextend, {
    fontManager: null,
    styleManager: null,
    notificationCenter: null,
    animationManager: null,
    browse: null,
    askToSave: true,
    cancel: function (url) {
        nextend.askToSave = false;
        window.location.href = url;
        return false;
    },
    isWordpress: false,
    isJoomla: false,
    isMagento: false,
    isHTML: false
});

window.n2_ = function (text) {
    if (typeof nextend.localization[text] !== 'undefined') {
        return nextend.localization[text];
    }
    return text;
};

window.n2_printf = function (text) {
    var args = arguments;
    var index = 1;
    return text.replace(/%s/g, function () {
        return args[index++];
    });
};

/**
 * Help us to track when the user loaded the page
 */
window.nextendtime = n2.now();

/*
 * Moves an element with the page scroll to be in a special position
 */
(function ($) {

    nextend.rtl = {
        isRtl: false,
        marginLeft: 'marginLeft',
        marginRight: 'marginRight',
        left: 'left',
        right: 'right'
    };

    var isRtl = false,
        initRtl = function () {
            if ($('html').attr('dir') == 'rtl') {
                isRtl = true;
                nextend.rtl = {
                    isRtl: true,
                    marginLeft: 'marginRight',
                    marginRight: 'marginLeft',
                    left: 'right',
                    right: 'left'
                };
            }
        };

    nextend.isRTL = function () {
        return isRtl;
    };

    nextend.ready(initRtl);

    var elems = [],
        sidename = {
            left: 'left',
            right: 'right'
        };

    function rtl() {
        sidename = {
            left: 'right',
            right: 'left'
        };
    }

    function ltr() {
        sidename = {
            left: 'left',
            right: 'right'
        };
    }

    function getOffset($el, side) {
        var offset = 0;
        if (side == sidename.right) {
            offset = ($(window).width() - ($el.offset().left + $el.outerWidth()));
        } else {
            offset = $el.offset().left;
        }
        if (offset < 0)
            return 0;
        return offset;
    }

    $('html').on('changedir', function (e, dir) {
        for (var i = 0; i < elems.length; i++) {
            elems[i][0].css(sidename[elems[i][2]], 'auto');
        }
        if (dir === 'rtl') {
            rtl();
        } else {
            ltr();
        }
        $(document).trigger('scroll');
    });

    var scrollAdjustment = 0;

    nextend.ready(function () {
        var topBarHeight = $('#wpadminbar, .navbar-fixed-top').height();
        if (topBarHeight) {
            scrollAdjustment += topBarHeight;
        }
        $(document).trigger('scroll');
    });

    $(document).on('scroll', function () {
        var scrolltop = $(document).scrollTop() + scrollAdjustment;
        for (var i = 0; i < elems.length; i++) {
            if (elems[i][1] > scrolltop) {
                elems[i][0].removeClass('n2-static');
            } else {
                elems[i][0].addClass('n2-static');
                elems[i][0].css(sidename[elems[i][2]], elems[i][3]);
            }
        }
    });

    $(window).on('resize', function () {
        for (var i = 0; i < elems.length; i++) {
            elems[i][1] = elems[i][0].parent().offset().top;
            elems[i][3] = getOffset(elems[i][0].parent(), elems[i][2]);
        }
        $(document).trigger('scroll');
    });

    $.fn.staticonscroll = function (side) {
        this.each(function () {
            var $el = $(this);
            elems.push([$el, $el.parent().offset().top, side, getOffset($el.parent(), side)]);
        });
        $(document).trigger('scroll');
    };
})(n2);

(function ($) {

    var NextendAjaxHelper = {
            query: {}
        },
        loader = null;

    NextendAjaxHelper.addAjaxLoader = function () {
        loader = $('<div class="n2-loader-overlay"><div class="n2-loader"></div></div>')
            .appendTo('body');
    };

    NextendAjaxHelper.addAjaxArray = function (parts) {
        for (var k in parts) {
            NextendAjaxHelper.query[k] = parts[k];
        }
    };

    NextendAjaxHelper.makeAjaxQuery = function (queryArray, isAjax) {
        if (isAjax) {
            queryArray['mode'] = 'ajax';
            queryArray['nextendajax'] = '1';
        }
        for (var k in NextendAjaxHelper.query) {
            queryArray[k] = NextendAjaxHelper.query[k];
        }
        return N2QueryString.stringify(queryArray);
    };

    NextendAjaxHelper.makeAjaxUrl = function (url, queries) {
        var urlParts = url.split("?");
        if (urlParts.length < 2) {
            urlParts[1] = '';
        }
        var parsed = N2QueryString.parse(urlParts[1]);
        if (typeof queries != 'undefined') {
            for (var k in queries) {
                parsed[k] = queries[k];
            }
        }
        return urlParts[0] + '?' + NextendAjaxHelper.makeAjaxQuery(parsed, true);
    };

    NextendAjaxHelper.makeFallbackUrl = function (url, queries) {
        var urlParts = url.split("?");
        if (urlParts.length < 2) {
            urlParts[1] = '';
        }
        var parsed = N2QueryString.parse(urlParts[1]);
        if (typeof queries != 'undefined') {
            for (var k in queries) {
                parsed[k] = queries[k];
            }
        }
        return urlParts[0] + '?' + NextendAjaxHelper.makeAjaxQuery(parsed, false);
    };

    NextendAjaxHelper.ajax = function (ajax) {
        NextendAjaxHelper.startLoading();
        return $.ajax(ajax).always(function (response, status) {
            NextendAjaxHelper.stopLoading();
            try {

                if (status != 'success') {
                    response = JSON.parse(response.responseText);
                }
                if (typeof response.redirect != 'undefined') {
                    NextendAjaxHelper.startLoading();
                    window.location.href = response.redirect;
                    return;
                }

                NextendAjaxHelper.notification(response);
            } catch (e) {
            }
        });
    };

    NextendAjaxHelper.notification = function (response) {

        if (typeof response.notification !== 'undefined' && response.notification) {
            for (var k in response.notification) {
                for (var i = 0; i < response.notification[k].length; i++) {
                    nextend.notificationCenter[k](response.notification[k][i][0], response.notification[k][i][1]);
                }
            }
        }
    };

    NextendAjaxHelper.getJSON = function (ajax) {
        NextendAjaxHelper.startLoading();
        return $.getJSON(ajax).always(function () {
            NextendAjaxHelper.stopLoading();
        });
    };

    NextendAjaxHelper.startLoading = function () {
        loader.addClass('n2-active');
    };

    NextendAjaxHelper.stopLoading = function () {
        loader.removeClass('n2-active');
    };

    window.NextendAjaxHelper = NextendAjaxHelper;
    nextend.ready(function () {
        NextendAjaxHelper.addAjaxLoader();
    });
})(n2);

(function ($, scope) {

    function NextendHeadingPane(headings, contents, identifier) {
        this.headings = headings;
        this.contents = contents;
        this.identifier = identifier;

        this._active = headings.index(headings.filter('.n2-active'));

        for (var i = 0; i < headings.length; i++) {
            headings.eq(i).on('click', $.proxy(this.switchToPane, this, i));
        }

        if (identifier) {
            var saved = $.jStorage.get(this.identifier + "-pane", -1);
            if (saved != -1) {
                this.switchToPane(saved);
                return;
            }
        }
        this.hideAndShow();
    };


    NextendHeadingPane.prototype.switchToPane = function (i, e) {
        if (e) {
            e.preventDefault();
        }
        this.headings.eq(this._active).removeClass('n2-active');
        this.headings.eq(i).addClass('n2-active');
        this._active = i;

        this.hideAndShow();
        this.store(this._active);
    };

    NextendHeadingPane.prototype.hideAndShow = function () {
        this.contents[this._active].css('display', 'block').trigger('activate');
        for (var i = 0; i < this.contents.length; i++) {
            if (i != this._active) {
                this.contents[i].css('display', 'none');
            }
        }
    };

    NextendHeadingPane.prototype.store = function (i) {
        if (this.identifier) {
            $.jStorage.set(this.identifier + "-pane", i);
        }
    };
    scope.NextendHeadingPane = NextendHeadingPane;


    function NextendHeadingScrollToPane(headings, contents, identifier) {
        this.headings = headings;
        this.contents = contents;
        this.identifier = identifier;

        for (var i = 0; i < headings.length; i++) {
            headings.eq(i).on('click', $.proxy(this.scrollToPane, this, i));
        }
    }

    NextendHeadingScrollToPane.prototype.scrollToPane = function (i, e) {
        if (e) {
            e.preventDefault();
        }
        $('html, body').animate({
            scrollTop: this.contents[i].offset().top - $('.n2-main-top-bar').height() - $('#wpadminbar, .navbar-fixed-top').height() - 10
        }, 1000);
    };

    scope.NextendHeadingScrollToPane = NextendHeadingScrollToPane;

})(n2, window);

(function ($, scope) {
    var FiLo = [],
        doc = $(document),
        isListening = false;
    scope.NextendEsc = {
        _listen: function () {
            if (!isListening) {
                doc.on('keydown.n2-esc', function (e) {
                    if ((e.keyCode == 27 || e.keyCode == 8)) {
                        if (!$(e.target).is("input, textarea")) {
                            e.preventDefault();
                            var ret = FiLo[FiLo.length - 1]();
                            if (ret) {
                                scope.NextendEsc.pop();
                            }
                        } else if (e.keyCode == 27) {
                            e.preventDefault();
                            $(e.target).blur();
                        }
                    }
                });
                isListening = true;
            }
        },
        _stopListen: function () {
            doc.off('keydown.n2-esc');
            isListening = false;
        },
        add: function (callback) {
            FiLo.push(callback);
            scope.NextendEsc._listen();
        },
        pop: function () {
            FiLo.pop();
            if (FiLo.length === 0) {
                scope.NextendEsc._stopListen();
            }
        }
    };
})(n2, window);


(function ($, scope) {
    $.fn.n2opener = function () {
        return this.each(function () {
            var opener = $(this).on("click", function (e) {
                opener.toggleClass("n2-active");
            });

            opener.parent().on("mouseleave", function () {
                opener.removeClass("n2-active");
            })
            opener.find(".n2-button-menu").on("click", function (e) {
                e.stopPropagation();
                opener.removeClass("n2-active");
            });
        });
    };
})(n2, window);

if (typeof jQuery !== 'undefined') {
    jQuery(document).on('wp-collapse-menu', function () {
        n2(window).trigger('resize');
    });
}