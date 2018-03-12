(function () {
    var cbs = [],
        ready = false;
    window.n2jQuery = {
        ready: function (cb) {
            ready ? window.n2jQuery.fire(cb) : cbs.push(cb);
        },
        fire: function (cb) {
            cb.call(window.n2 || window.jQuery, window.n2 || window.jQuery);
        }
    }
// Poll to see if jQuery is ready
    function waitForJQuery() {

        if (window.jQuery || window.n2) {
            ready = true;
            for (var i = 0; i < cbs.length; i++) {
                window.n2jQuery.fire(cbs[i]);
            }
        } else {
            setTimeout(waitForJQuery, 20);
        }
    }

    waitForJQuery();
})();

window.n2jQuery.ready(function () {
    if (typeof window.n2 == "undefined") {
        window.n2 = typeof jQuery == "undefined" ? null : jQuery;
    }

    window.nextend.$ = window.n2('');

    var readyDeferred = window.n2.Deferred();
    window.nextend.deferreds.push(readyDeferred);
    window.n2(document).ready(function () {
        readyDeferred.resolve();
    });
});

function NextendThrottle(func, wait) {
    wait || (wait = 250);
    var last,
        deferTimer;
    return function () {
        var context = this,
            now = +new Date,
            args = arguments;
        if (last && now < last + wait) {
            // hold on to it
            clearTimeout(deferTimer);
            deferTimer = setTimeout(function () {
                last = now;
                func.apply(context, args);
            }, wait);
        } else {
            last = now;
            func.apply(context, args);
        }
    };
}

function NextendDeBounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};