(function ($, scope) {

    function NextendCSS() {
        this.style = '';
    };

    NextendCSS.prototype.add = function (css) {
        var head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        head.appendChild(style);

        style.type = 'text/css';
        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }
    };

    NextendCSS.prototype.deleteRule = function (selectorText) {
        var selectorText1 = selectorText.toLowerCase();
        var selectorText2 = selectorText1.replace('.', '\\.');
        for (var j = document.styleSheets.length - 1; j >= 0; j--) {
            var rules = this._getRulesArray(j);
            for (var i = 0; rules && i < rules.length; i++) {
                if (rules[i].selectorText) {
                    var lo = rules[i].selectorText.toLowerCase();
                    if ((lo == selectorText1) || (lo == selectorText2)) {
                        if (document.styleSheets[j].cssRules) {
                            document.styleSheets[j].deleteRule(i);
                        } else {
                            document.styleSheets[j].removeRule(i);
                        }
                    }
                }
            }
        }
        return (null);
    };

    NextendCSS.prototype._getRulesArray = function (i) {
        var crossrule = null;
        try {
            if (document.styleSheets[i].cssRules)
                crossrule = document.styleSheets[i].cssRules;
            else if (document.styleSheets[i].rules)
                crossrule = document.styleSheets[i].rules;
        } catch (e) {
        }
        return (crossrule);
    };

    window.nextend.css = new NextendCSS();

})(n2, window);
