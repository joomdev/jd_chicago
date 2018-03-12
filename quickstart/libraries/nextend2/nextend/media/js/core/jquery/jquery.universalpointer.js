(function ($) {
    $.event.special.universalclick = {
        add: function (handleObj) {
            var el = $(this),
                _suppress = false,
                _suppressTimeout = null,
                suppress = function () {
                    _suppress = true;
                    if (_suppressTimeout) {
                        clearTimeout(_suppressTimeout);
                    }
                    _suppressTimeout = setTimeout(function () {
                        _suppress = false;
                    }, 400);
                };

            el.on('touchend.universalclick click.universalclick', function (e) {
                if (!_suppress) {
                    suppress();
                    handleObj.handler.apply(this, arguments);
                }

            });
        },

        remove: function (handleObj) {
            $(this).off('.universalclick');
        }
    };

    var touchElements = [];
    var globalTouchWatched = false;
    var watchGlobalTouch = function () {
            if (!globalTouchWatched) {
                globalTouchWatched = true;
                $('body').on('touchstart.universaltouch', function (e) {
                    var target = $(e.target);
                    for (var i = touchElements.length - 1; i >= 0; i--) {
                        if (!touchElements[i].is(target) && touchElements[i].find(target).length == 0) {
                            touchElements[i].trigger('universal_leave');
                        }
                    }
                });
            }
        }, unWatchGlobalTouch = function () {
            if (globalTouchWatched) {
                $('body').off('.universaltouch');
                globalTouchWatched = false;
            }
        },
        addTouchElement = function (el) {
            if ($.inArray(el, touchElements) == -1) {
                touchElements.push(el);
            }
            if (touchElements.length == 1) {
                watchGlobalTouch();
            }
        },
        removeTouchElement = function (el) {
            var i = $.inArray(el, touchElements)
            if (i >= 0) {
                touchElements.splice(i, 1);
                if (touchElements.length == 0) {
                    unWatchGlobalTouch();
                }
            }
        };

    $.event.special.universalenter = {
        add: function (handleObj) {

            var el = $(this),
                _suppress = false,
                _suppressTimeout = null,
                suppress = function () {
                    _suppress = true;
                    if (_suppressTimeout) {
                        clearTimeout(_suppressTimeout);
                        _suppressTimeout = null;
                    }
                    _suppressTimeout = setTimeout(function () {
                        _suppress = false;
                    }, 400);
                };

            var leaveOnSecond = false;
            if (handleObj.data) {
                leaveOnSecond = handleObj.data.leaveOnSecond;
            }

            var touchTimeout = null;

            el.on('universal_leave.universalenter', function (e) {
                e.stopPropagation();
                clearTimeout(touchTimeout);
                touchTimeout = null;
                removeTouchElement(el);
                el.trigger('universalleave');
            }).on('touchstart.universalenter mouseenter.universalenter', function (e) {
                if (!_suppress) {
                    suppress();
                    if (e.type == 'touchstart') {
                        if (leaveOnSecond) {
                            if (touchTimeout) {
                                el.trigger('universal_leave');
                            } else {
                                addTouchElement(el);
                                handleObj.handler.apply(this, arguments);
                                touchTimeout = setTimeout(function () {
                                    el.trigger('universal_leave');
                                }, 5000);
                            }
                        } else {
                            if (touchTimeout) {
                                clearTimeout(touchTimeout);
                                touchTimeout = null;
                            }

                            addTouchElement(el);

                            handleObj.handler.apply(this, arguments);
                            touchTimeout = setTimeout(function () {
                                el.trigger('universal_leave');
                            }, 5000);

                        }
                    } else {
                        handleObj.handler.apply(this, arguments);
                        el.on('mouseleave.universalleave', function () {
                            el.off('.universalleave')
                                .trigger('universalleave');
                        });
                    }
                }
            });
        },

        remove: function (handleObj) {
            $(this).off('.universalenter .universalleave');
        }
    };
})(n2);