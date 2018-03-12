(function ($, scope) {

    function NextendElementRichText(id) {

        NextendElementText.prototype.constructor.apply(this, arguments);

        this.parent.find('.n2-textarea-rich-bold').on('click', $.proxy(this.bold, this));
        this.parent.find('.n2-textarea-rich-italic').on('click', $.proxy(this.italic, this));
        this.parent.find('.n2-textarea-rich-link').on('click', $.proxy(this.link, this));

    };


    NextendElementRichText.prototype = Object.create(NextendElementText.prototype);
    NextendElementRichText.prototype.constructor = NextendElementRichText;


    NextendElementRichText.prototype.bold = function () {
        this.wrapText('<b>', '</b>');
    };

    NextendElementRichText.prototype.italic = function () {
        this.wrapText('<i>', '</i>');
    };

    NextendElementRichText.prototype.link = function () {
        this.wrapText('<a href="">', '</a>');
    };

    NextendElementRichText.prototype.list = function () {
        this.wrapText('', "\n<ul>\n<li>#1 Item</li>\n<li>#2 Item</li>\n</ul>\n");
    };


    NextendElementRichText.prototype.wrapText = function (openTag, closeTag) {
        var textArea = this.element;
        var len = textArea.val().length;
        var start = textArea[0].selectionStart;
        var end = textArea[0].selectionEnd;
        var selectedText = textArea.val().substring(start, end);
        var replacement = openTag + selectedText + closeTag;
        textArea.val(textArea.val().substring(0, start) + replacement + textArea.val().substring(end, len));
        this.triggerOutsideChange();
        this.element.focus();
    };

    scope.NextendElementRichText = NextendElementRichText;
})(n2, window);