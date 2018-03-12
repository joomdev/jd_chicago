(function ($, scope, undefined) {

    function GeneratorRecords(ajaxUrl) {
        this.ajaxUrl = ajaxUrl;

        $("#generatorrecord-viewer").on("click", $.proxy(this.showRecords, this));
    };

    GeneratorRecords.prototype.showRecords = function (e) {
        e.preventDefault();
        NextendAjaxHelper.ajax({
            type: "POST",
            url: this.ajaxUrl,
            data: $("#smartslider-form").serialize(),
            dataType: "json"
        }).done(function (response) {
            var modal = new NextendModal({
                zero: {
                    size: [
                        1300,
                        700
                    ],
                    title: "Records",
                    content: response.data.html
                }
            }, true);
            modal.content.css('overflow', 'auto');
        }).error(function (response) {
            if (response.status == 200) {
                var modal = new NextendModal({
                    zero: {
                        size: [
                            1300,
                            700
                        ],
                        title: "Response",
                        content: response.responseText
                    }
                }, true);
                modal.content.css('overflow', 'auto');
            }
        });
    };

    scope.NextendSmartSliderGeneratorRecords = GeneratorRecords;
})(n2, window);