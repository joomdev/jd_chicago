;
(function ($, scope) {

    function NextendImageManager() {
        this.referenceKeys = {};
        NextendVisualManagerCore.prototype.constructor.apply(this, arguments);
    };

    NextendImageManager.prototype = Object.create(NextendVisualManagerCore.prototype);
    NextendImageManager.prototype.constructor = NextendImageManager;

    NextendImageManager.prototype.loadDefaults = function () {
        NextendVisualManagerCore.prototype.loadDefaults.apply(this, arguments);
        this.type = 'image';
        this.labels = {
            visual: n2_('image'),
            visuals: n2_('images')
        };

        this.fontClassName = '';
    };


    NextendImageManager.prototype.initController = function () {
        return new NextendImageEditorController();
    };

    NextendImageManager.prototype.createVisual = function (visual) {
        return new NextendImage(visual, this);
    };

    NextendImageManager.prototype.firstLoadVisuals = function (visuals) {
        for (var i = 0; i < visuals.length; i++) {
            this.referenceKeys[visuals[i].hash] = this.visuals[visuals[i].id] = this.createVisual(visuals[i]);
        }
    };

    NextendImageManager.prototype.getVisual = function (image) {
        if (image == '') {
            nextend.notificationCenter.error('The image is empty', {
                timeout: 3
            });
        } else {
            var referenceKey = md5(image);
            if (typeof this.referenceKeys[referenceKey] !== 'undefined') {
                return this.referenceKeys[referenceKey];
            } else if (typeof this.visualLoadDeferreds[referenceKey] !== 'undefined') {
                return this.visualLoadDeferreds[referenceKey];
            } else {
                var deferred = $.Deferred();
                this.visualLoadDeferreds[referenceKey] = deferred;
                this._loadVisualFromServer(image)
                    .done($.proxy(function () {
                        deferred.resolve(this.referenceKeys[referenceKey]);
                        delete this.visualLoadDeferreds[referenceKey];
                    }, this))
                    .fail($.proxy(function () {
                        // This visual is Empty!!!
                        deferred.resolve({
                            id: -1,
                            name: n2_('Empty')
                        });
                        delete this.visualLoadDeferreds[referenceKey];
                    }, this));
                return deferred;
            }
        }
    };

    NextendImageManager.prototype._loadVisualFromServer = function (image) {
        return NextendAjaxHelper.ajax({
            type: "POST",
            url: NextendAjaxHelper.makeAjaxUrl(this.parameters.ajaxUrl, {
                nextendaction: 'loadVisualForImage'
            }),
            data: {
                image: image
            },
            dataType: 'json'
        })
            .done($.proxy(function (response) {
                var visual = response.data.visual;
                this.referenceKeys[visual.hash] = this.visuals[visual.id] = this.createVisual(visual);
            }, this));
    };

    NextendImageManager.prototype.isVisualData = function (data) {
        return data != '';
    };

    NextendImageManager.prototype.setVisual = function (e) {
        e.preventDefault();
        if (this.controller.isChanged) {
            this.saveActiveVisual(this.activeVisual.name)
                .done($.proxy(function (response) {
                    $(window).trigger(response.data.visual.hash, this.activeVisual.value);
                    this.hide(e);
                }, this));
        } else {
            this.hide(e);
        }
    };

    NextendImageManager.prototype.getBase64 = function () {

        return Base64.encode(JSON.stringify(this.controller.get('set')));
    };

    scope.NextendImageManager = NextendImageManager;

    function NextendImage() {
        NextendVisualCore.prototype.constructor.apply(this, arguments);
    };

    NextendImage.prototype = Object.create(NextendVisualCore.prototype);
    NextendImage.prototype.constructor = NextendImage;

    NextendImage.prototype.setValue = function (value, render) {
        this.base64 = value;
        this.value = JSON.parse(Base64.decode(value));
    };

    NextendImage.prototype.activate = function (e) {
        if (typeof e !== 'undefined') {
            e.preventDefault();
        }
        this.visualManager.changeActiveVisual(this);
        this.visualManager.controller.load(this, false, this.visualManager.showParameters);
    };

    scope.NextendImage = NextendImage;

})(n2, window);
