(function ($, scope, undefined) {

    function ItemParserHeading() {
        NextendSmartSliderItemParser.apply(this, arguments);
    };

    ItemParserHeading.prototype = Object.create(NextendSmartSliderItemParser.prototype);
    ItemParserHeading.prototype.constructor = ItemParserHeading;

    ItemParserHeading.prototype.getDefault = function () {
        return {
            link: '#|*|_self',
            font: '',
            style: ''
        }
    };

    ItemParserHeading.prototype.added = function () {
        this.needFill = ['heading', 'url'];

        this.addedFont('hover', 'font');
        this.addedStyle('heading', 'style');

        nextend.smartSlider.generator.registerField($('#item_headingheading'));
        nextend.smartSlider.generator.registerField($('#linkitem_headinglink_0'));

    };

    ItemParserHeading.prototype.getName = function (data) {
        return data.heading;
    };

    ItemParserHeading.prototype.parseAll = function (data) {

        data.uid = $.fn.uid();

        var link = data.link.split('|*|');
        data.url = link[0];
        data.target = link[1];
        delete data.link;


        if (data.fullwidth | 0) {
            data.display = 'block;';
        } else {
            data.display = 'inline-block;';
        }

        data.extrastyle = data.nowrap | 0 ? 'white-space: nowrap;' : '';

        data.heading = $('<div>' + data.heading + '</div>').text().replace(/\n/g, '<br />');
        data.priority = 2;
        data.class = '';
    

        NextendSmartSliderItemParser.prototype.parseAll.apply(this, arguments);

        if (data['url'] == '#') {
            data['afontclass'] = '';
        } else {
            data['afontclass'] = data['fontclass'];
            data['fontclass'] = '';
        }
    };

    ItemParserHeading.prototype.render = function (node, data) {
        if (data['url'] == '#') {
            var a = node.find('a');
            a.parent().html(a.html());
        }
        return node;
    }

    scope.NextendSmartSliderItemParser_heading = ItemParserHeading;
})(n2, window);