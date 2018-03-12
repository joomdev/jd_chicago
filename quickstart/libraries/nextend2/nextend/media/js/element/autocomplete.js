(function ($, scope) {

    function NextendElementAutocomplete(id, tags) {
        this.tags = tags;
        this.element = $('#' + id).data('autocomplete', this);
        this.element.on("keydown", function (event) {
            if (event.keyCode === $.ui.keyCode.TAB && $(this).nextendAutocomplete("instance").menu.active) {
                event.preventDefault();
            }
        }).nextendAutocomplete({
            minLength: 0,
            position: {
                my: "left top-2",
                of: this.element.parent(),
                collision: 'flip'
            },
            source: $.proxy(function (request, response) {
                var terms = request.term.split(/,/),
                    filtered = [];

                $.each(this.tags, function (key, value) {
                    if (-1 === terms.indexOf(value)) {
                        filtered.push(value);
                    }
                });
                response(filtered);
            }, this),
            focus: function () {
                // prevent value inserted on focus
                return false;
            },
            select: function (event, ui) {
                var terms = this.value.split(/,/);
                terms.pop();
                terms.push(ui.item.value);
                terms.push("");
                this.value = terms.join(",");
                $(this).trigger('change').nextendAutocomplete("search");
                return false;
            },
            open: function () {
                console.log(arguments);
            }
        }).click(function () {
            $(this).nextendAutocomplete("search");
        });

        this.element.siblings('.n2-form-element-clear')
            .on('click', $.proxy(this.clear, this));
    };

    NextendElementAutocomplete.prototype.clear = function (e) {
        e.preventDefault();
        e.stopPropagation();
        this.element.val('').trigger('change');
    };

    NextendElementAutocomplete.prototype.setTags = function (tags) {
        this.tags = tags;
    };

    scope.NextendElementAutocomplete = NextendElementAutocomplete;

    function NextendElementAutocompleteSimple(id, values) {
        this.element = $('#' + id).data('autocomplete', this);
        this.element.nextendAutocomplete({
            appendTo: this.element.parent(),
            minLength: 0,
            position: {
                my: "left top-2",
                of: this.element.parent(),
                collision: 'flip'
            },
            source: function (request, response) {
                response(values);
            },
            select: function (event, ui) {
                $(this).val(ui.item.value).trigger('change');
                return false;
            }
        }).click(function () {
            $(this).nextendAutocomplete("search", "");
        });
    };

    scope.NextendElementAutocompleteSimple = NextendElementAutocompleteSimple;

})(n2, window);