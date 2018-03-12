(function ($, scope, undefined) {

    var cache = {};

    function NextendBrowse(url, uploadAllowed) {
        this.url = url;
        this.uploadAllowed = parseInt(uploadAllowed);
        this.currentPath = $.jStorage.get('browsePath', '');
        var timeout = null;
        this.node = $('<div class="n2-browse-container"/>').on('dragover', function (e) {
            if (timeout !== null) {
                clearTimeout(timeout);
                timeout = null;
            } else {
                $(e.currentTarget).addClass('n2-drag-over');
            }
            timeout = setTimeout(function () {
                $(e.currentTarget).removeClass('n2-drag-over');
                timeout = null;
            }, 400);

        });
        nextend.browse = this;
    };

    NextendBrowse.prototype.clear = function () {
        if (this.uploadAllowed) {
            this.node.find('#n2-browse-upload').fileupload('destroy');
        }
        this.node.empty();
    };

    NextendBrowse.prototype.getNode = function (mode, callback) {
        this.clear();
        this.mode = mode;
        if (mode == 'multiple') {
            this.selected = [];
        }
        this.callback = callback;
        this._loadPath(this.getCurrentFolder(), $.proxy(this._renderBoxes, this));
        return this.node;
    };

    NextendBrowse.prototype._renderBoxes = function (data) {
        this.clear();

        if (this.uploadAllowed) {
            this.node.append($('<div class="n2-browse-box n2-browse-upload"><div class="n2-h4">' + n2_('Drop files anywhere to upload or') + ' <a class="n2-button n2-button-medium n2-button-grey n2-uc n2-h4" href="#">' + n2_('Select files') + '</a></div><input id="n2-browse-upload" type="file" name="image" multiple></div>'));

            this.node.find('#n2-browse-upload').fileupload({
                url: NextendAjaxHelper.makeAjaxUrl(this.url, {
                    nextendaction: 'upload'
                }),
                sequentialUploads: true,
                dropZone: this.node,
                pasteZone: false,
                dataType: 'json',
                paramName: 'image',
                add: $.proxy(function (e, data) {

                    var box = $('<div class="n2-browse-box n2-browse-image"><div class="n2-button n2-button-small n2-button-blue"><i class="n2-i n2-it n2-i-tick"></i></div><div class="n2-browse-title">0%</div></div>');

                    var images = this.node.find('.n2-browse-image');
                    if (images.length > 0) {
                        box.insertBefore(images.eq(0));
                    } else {
                        box.appendTo(this.node);
                    }
                    data.box = box;


                    data.formData = {path: this.currentPath};
                    data.submit();
                }, this),
                progress: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    data.box.find('.n2-browse-title').html(progress + '%');
                },
                done: $.proxy(function (e, data) {
                    var response = data.result;

                    if (response.data && response.data.name) {
                        cache[response.data.path].data.files[response.data.name] = response.data.url;

                        data.box.css('background-image', 'url(' + encodeURI(nextend.imageHelper.fixed(response.data.url)) + ')')
                            .on('click', $.proxy(this.clickImage, this, response.data.url))
                            .find('.n2-browse-title').html(response.data.name);
                        if (this.mode == 'multiple') {
                            this.selected.push(response.data.url);
                            data.box.addClass('n2-active');
                        }
                    } else {
                        data.box.destroy();
                    }

                    NextendAjaxHelper.notification(response);

                }, this),
                fail: $.proxy(function (e, data) {
                    data.box.remove();
                    NextendAjaxHelper.notification(data.jqXHR.responseJSON);
                }, this)
            });

            $.jStorage.set('browsePath', this.getCurrentFolder());
        }

        if (data.path != '') {
            this.node.append($('<div class="n2-browse-box n2-browse-directory"><i class="n2-i n2-it n2-i-up"></i></div>').on('click', $.proxy(function (directory) {
                this._loadPath(directory, $.proxy(this._renderBoxes, this))
            }, this, data.path + '/..')));
        }
        for (var k in data.directories) {
            if (data.directories.hasOwnProperty(k)) {
                this.node.append($('<div class="n2-browse-box n2-browse-directory"><i class="n2-i n2-it n2-i-folder"></i><div class="n2-browse-title">' + k + '</div></div>').on('click', $.proxy(function (directory) {
                    this._loadPath(directory, $.proxy(this._renderBoxes, this))
                }, this, data.directories[k])));
            }
        }
        for (var k in data.files) {
            if (data.files.hasOwnProperty(k)) {
                var box = $('<div class="n2-browse-box n2-browse-image"><div class="n2-button n2-button-small n2-button-blue"><i class="n2-i n2-it n2-i-tick"></i></div><div class="n2-browse-title">' + k + '</div></div>')
                    .css('background-image', 'url(' + encodeURI(nextend.imageHelper.fixed(data.files[k])) + ')')
                    .on('click', $.proxy(this.clickImage, this, data.files[k]));
                this.node.append(box);

                if (this.mode == 'multiple') {
                    if ($.inArray(data.files[k], this.selected) != -1) {
                        box.addClass('n2-active');
                    }
                }
            }
        }
    };


    NextendBrowse.prototype._loadPath = function (path, callback) {
        if (typeof cache[path] === 'undefined') {
            cache[path] = NextendAjaxHelper.ajax({
                type: "POST",
                url: NextendAjaxHelper.makeAjaxUrl(this.url),
                data: {
                    path: path
                },
                dataType: 'json'
            });
        }
        $.when(cache[path]).done($.proxy(function (response) {
            this.currentPath = response.data.path;
            cache[response.data.path] = response;
            cache[path] = response;
            callback(response.data);
        }, this));

    };

    NextendBrowse.prototype.clickImage = function (image, e) {
        if (this.mode == 'single') {
            this.callback(image);
        } else if (this.mode == 'multiple') {
            var i = $.inArray(image, this.selected);
            if (i == -1) {
                $(e.currentTarget).addClass('n2-active');
                this.selected.push(image);
            } else {
                $(e.currentTarget).removeClass('n2-active');
                this.selected.splice(i, 1);
            }
        }
    };

    NextendBrowse.prototype.getSelected = function () {
        return this.selected;
    };

    NextendBrowse.prototype.getCurrentFolder = function () {
        return this.currentPath;
    };


    scope.NextendBrowse = NextendBrowse;

})(n2, window);