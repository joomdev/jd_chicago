(function ($, scope, undefined) {

    function NextendSmartSliderAdminSidebarSlides(ajaxUrl, contentAjaxUrl, parameters, isUploadDisabled, uploadUrl, uploadDir) {
        this.quickPostModal = null;
        this.quickVideoModal = null;
        this.parameters = parameters;
        this.slides = [];
        this.ajaxUrl = ajaxUrl;
        this.contentAjaxUrl = contentAjaxUrl;
        this.slidesPanel = $('#n2-ss-slides');
        this.slidesContainer = this.slidesPanel.find('.n2-ss-slides-container');

        this.initSlidesOrderable();

        var slides = this.slidesContainer.find('.n2-box-slide');
        for (var i = 0; i < slides.length; i++) {
            this.slides.push(new NextendSmartSliderAdminSlide(this, slides.eq(i)));
        }

        if (this.slides.length > 0) {
            this.slidesPanel.addClass('n2-ss-has-slides');
        }

        $('.n2-add-quick-image').on('click', $.proxy(this.addQuickImage, this));
        $('.n2-box-slide-add').on('click', $.proxy(this.addQuickImage, this));
        $('.n2-add-quick-video').on('click', $.proxy(this.addQuickVideo, this));
        $('.n2-add-quick-post').on('click', $.proxy(this.addQuickPost, this));

        this.initBulk();

        if ($('#n2-ss-slide-editor-main-tab').length == 0) {
            new NextendSmartSliderSidebarSlides();
        }


        if (!isUploadDisabled) {
            var images = [];
            this.slidesContainer.fileupload({
                url: uploadUrl,
                pasteZone: false,
                dropZone: this.slidesContainer,
                dataType: 'json',
                paramName: 'image',

                add: $.proxy(function (e, data) {
                    data.formData = {path: '/' + uploadDir};
                    data.submit();
                }, this),

                done: $.proxy(function (e, data) {
                    var response = data.result;
                    if (response.data && response.data.name) {
                        images.push({
                            title: response.data.name,
                            description: '',
                            image: response.data.url
                        });
                    } else {
                        NextendAjaxHelper.notification(response);
                    }

                }, this),

                fail: $.proxy(function (e, data) {
                    NextendAjaxHelper.notification(data.jqXHR.responseJSON);
                }, this),

                start: function () {
                    NextendAjaxHelper.startLoading();
                },

                stop: $.proxy(function () {
                    if (images.length) {
                        this._addQuickImages(images);
                    } else {
                        setTimeout(function () {
                            NextendAjaxHelper.stopLoading();
                        }, 100);
                    }
                    images = [];
                }, this)
            });

            var timeout = null;
            this.slidesContainer.on('dragover', $.proxy(function (e) {
                if (timeout !== null) {
                    clearTimeout(timeout);
                    timeout = null;
                } else {
                    this.slidesContainer.addClass('n2-drag-over');
                }
                timeout = setTimeout($.proxy(function () {
                    this.slidesContainer.removeClass('n2-drag-over');
                    timeout = null;
                }, this), 400);

            }, this));
        }
    };

    NextendSmartSliderAdminSidebarSlides.prototype.changed = function () {
        if (this.slides.length > 0) {
            this.slidesPanel.addClass('n2-ss-has-slides');
        } else {
            this.slidesPanel.removeClass('n2-ss-has-slides');
        }
    };

    NextendSmartSliderAdminSidebarSlides.prototype.initSlidesOrderable = function () {
        this.slidesContainer.sortable({
            items: ".n2-box-slide",
            tolerance: 'pointer',
            stop: $.proxy(this.saveSlideOrder, this),
            helper: 'clone',
            placeholder: 'n2-box-placeholder n2-box'
        });
    };

    NextendSmartSliderAdminSidebarSlides.prototype.saveSlideOrder = function (e) {
        var slideNodes = this.slidesContainer.find('.n2-box-slide'),
            slides = [],
            ids = [],
            originalIds = [];
        for (var i = 0; i < slideNodes.length; i++) {
            var slide = slideNodes.eq(i).data('slide');
            slides.push(slide);
            ids.push(slide.getId());
        }
        for (var i = 0; i < this.slides.length; i++) {
            originalIds.push(this.slides[i].getId());
        }

        if (JSON.stringify(originalIds) != JSON.stringify(ids)) {
            $(window).triggerHandler('SmartSliderSidebarSlidesOrderChanged');
            var queries = {
                nextendcontroller: 'slides',
                nextendaction: 'order'
            };
            NextendAjaxHelper.ajax({
                type: 'POST',
                url: NextendAjaxHelper.makeAjaxUrl(this.ajaxUrl, queries),
                data: {
                    slideorder: ids
                }
            });
            this.slides = slides;
            this.changed();
        }
    };

    NextendSmartSliderAdminSidebarSlides.prototype.initSlides = function () {
        var previousLength = this.slides.length;
        var slideNodes = this.slidesContainer.find('.n2-box-slide'),
            slides = [];
        for (var i = 0; i < slideNodes.length; i++) {
            var slide = slideNodes.eq(i).data('slide');
            slides.push(slide);
        }
        this.slides = slides;
        this.changed();
        $(window).triggerHandler('SmartSliderSidebarSlidesChanged');
    };

    NextendSmartSliderAdminSidebarSlides.prototype.unsetFirst = function () {
        for (var i = 0; i < this.slides.length; i++) {
            this.slides[i].unsetFirst();
        }
        this.changed();
    };

    NextendSmartSliderAdminSidebarSlides.prototype.addQuickImage = function (e) {
        e.preventDefault();
        nextend.imageHelper.openMultipleLightbox($.proxy(this._addQuickImages, this));
    };

    NextendSmartSliderAdminSidebarSlides.prototype._addQuickImages = function (images) {
        NextendAjaxHelper.ajax({
            type: 'POST',
            url: NextendAjaxHelper.makeAjaxUrl(this.ajaxUrl, {
                nextendaction: 'quickImages'
            }),
            data: {
                images: Base64.encode(JSON.stringify(images))
            }
        }).done($.proxy(function (response) {
            var boxes = $(response.data).insertBefore(this.slidesContainer.find('.n2-clear'));
            boxes.each($.proxy(function (i, el) {
                new NextendSmartSliderAdminSlide(this, $(el));
            }, this));
            this.initSlides();
        }, this));
    };

    NextendSmartSliderAdminSidebarSlides.prototype.addQuickVideo = function (e) {
        e.preventDefault();
        var manager = this;
        if (!this.quickVideoModal) {
            this.quickVideoModal = new NextendModal({
                zero: {
                    size: [
                        500,
                        350
                    ],
                    title: n2_('Add video'),
                    back: false,
                    close: true,
                    content: '<form class="n2-form"></form>',
                    controls: ['<a href="#" class="n2-button n2-button-big n2-button-green n2-uc n2-h4">' + n2_('Add video') + '</a>'],
                    fn: {
                        show: function () {
                            var button = this.controls.find('.n2-button'),
                                form = this.content.find('.n2-form').on('submit', function (e) {
                                    e.preventDefault();
                                    button.trigger('click');
                                }).append(this.createInput(n2_('Video url'), 'n2-slide-video-url', 'width: 446px;')),
                                videoUrlField = this.content.find('#n2-slide-video-url').focus();

                            this.content.append(this.createHeading(n2_('Examples')));
                            this.content.append(this.createTable([['YouTube', 'https://www.youtube.com/watch?v=MKmIwHAFjSU'], ['Vimeo', 'https://vimeo.com/144598279']], ['', '']));

                            button.on('click', $.proxy($.proxy(function (e) {
                                e.preventDefault();
                                var video = videoUrlField.val(),
                                    youtubeRegexp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/,
                                    youtubeMatch = video.match(youtubeRegexp),
                                    vimeoRegexp = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/,
                                    vimeoMatch = video.match(vimeoRegexp);

                                if (youtubeMatch) {
                                    NextendAjaxHelper.getJSON('https://www.googleapis.com/youtube/v3/videos?id=' + encodeURI(youtubeMatch[2]) + '&part=snippet&key=AIzaSyC3AolfvPAPlJs-2FgyPJdEEKS6nbPHdSM').done($.proxy(function (data) {
                                        if (data.items.length) {
                                            var snippet = data.items[0].snippet;

                                            var thumbnails = data.items[0].snippet.thumbnails,
                                                thumbnail = thumbnails.maxres || thumbnails.standard || thumbnails.high || thumbnails.medium || thumbnails.default;

                                            manager._addQuickVideo(this, {
                                                type: 'youtube',
                                                title: snippet.title,
                                                description: snippet.description,
                                                image: thumbnail.url,
                                                video: youtubeMatch[2]
                                            });
                                        }
                                    }, this)).fail(function (data) {
                                        nextend.notificationCenter.error(data.error.errors[0].message);
                                    });
                                } else if (vimeoMatch) {
                                    NextendAjaxHelper.getJSON('https://vimeo.com/api/v2/video/' + vimeoMatch[3] + '.json').done($.proxy(function (data) {
                                        manager._addQuickVideo(this, {
                                            type: 'vimeo',
                                            title: data[0].title,
                                            description: data[0].description,
                                            video: vimeoMatch[3],
                                            image: data[0].thumbnail_large
                                        });
                                    }, this)).fail(function (data) {
                                        nextend.notificationCenter.error(data.responseText);
                                    });

                                } else {
                                    nextend.notificationCenter.error('This video url is not supported!');
                                }
                            }, this)));
                        }
                    }
                }
            });
        }
        this.quickVideoModal.show();
    };

    NextendSmartSliderAdminSidebarSlides.prototype._addQuickVideo = function (modal, video) {
        NextendAjaxHelper.ajax({
            type: 'POST',
            url: NextendAjaxHelper.makeAjaxUrl(this.ajaxUrl, {
                nextendaction: 'quickVideo'
            }),
            data: {
                video: Base64.encode(JSON.stringify(video))
            }
        }).done($.proxy(function (response) {
            var box = $(response.data).insertBefore(this.slidesContainer.find('.n2-clear'));
            new NextendSmartSliderAdminSlide(this, box);

            this.initSlides();
        }, this));
        modal.hide();
    };

    NextendSmartSliderAdminSidebarSlides.prototype.addQuickPost = function (e) {
        e.preventDefault();
        if (!this.quickPostModal) {
            var manager = this,
                cache = {},
                getContent = $.proxy(function (search) {
                    if (typeof cache[search] == 'undefined') {
                        cache[search] = NextendAjaxHelper.ajax({
                            type: "POST",
                            url: NextendAjaxHelper.makeAjaxUrl(this.contentAjaxUrl),
                            data: {
                                keyword: search
                            },
                            dataType: 'json'
                        });
                    }
                    return cache[search];
                }, this);

            this.quickPostModal = new NextendModal({
                zero: {
                    size: [
                        600,
                        430
                    ],
                    title: n2_('Add post'),
                    back: false,
                    close: true,
                    content: '<div class="n2-form"></div>',
                    fn: {
                        show: function () {

                            this.content.find('.n2-form').append(this.createInput(n2_('Keyword'), 'n2-ss-keyword', 'width:546px;'));
                            var search = $('#n2-ss-keyword'),
                                heading = this.createHeading('').appendTo(this.content),
                                result = this.createResult().appendTo(this.content),
                                searchString = '';

                            search.on('keyup', $.proxy(function () {
                                searchString = search.val();
                                getContent(searchString).done($.proxy(function (r) {
                                    if (search.val() == searchString) {
                                        if (searchString == '') {
                                            heading.html(n2_('No search term specified. Showing recent items.'));
                                        } else {
                                            heading.html(n2_printf(n2_('Showing items match for "%s"'), searchString));
                                        }

                                        var rows = r.data,
                                            data = [],
                                            modal = this;
                                        for (var i = 0; i < rows.length; i++) {
                                            data.push([rows[i].title, rows[i].info, $('<div class="n2-button n2-button-green n2-button-x-small n2-uc n2-h5">' + n2_('Select') + '</div>')
                                                .on('click', {post: rows[i]}, function (e) {
                                                    manager._addQuickPost(modal, e.data.post);
                                                })]);
                                        }
                                        result.html('');
                                        this.createTable(data, ['width:100%;', '', '']).appendTo(this.createTableWrap().appendTo(result));
                                    }
                                }, this));
                            }, this))
                                .trigger('keyup').focus();
                        }
                    }
                }
            });
        }
        this.quickPostModal.show();
    };

    NextendSmartSliderAdminSidebarSlides.prototype._addQuickPost = function (modal, post) {
        if (!post.image) {
            post.image = '';
        }
        NextendAjaxHelper.ajax({
            type: 'POST',
            url: NextendAjaxHelper.makeAjaxUrl(this.ajaxUrl, {
                nextendaction: 'quickPost'
            }),
            data: {
                post: post
            }
        }).done($.proxy(function (response) {
            var box = $(response.data).insertBefore(this.slidesContainer.find('.n2-clear'));
            new NextendSmartSliderAdminSlide(this, box);

            this.initSlides();
        }, this));
        modal.hide();
    };

    NextendSmartSliderAdminSidebarSlides.prototype.initBulk = function () {
        $('.n2-slides-bulk').on('click', $.proxy(this.enterBulk, this));
        $('.n2-bulk-cancel').on('click', $.proxy(this.leaveBulk, this));

        var selects = $('.n2-bulk-select').find('a');

        // Invert
        selects.eq(0).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkSelect(function (slide) {
                slide.invertSelection();
            });
        }, this));

        //Select all
        selects.eq(1).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkSelect(function (slide) {
                slide.select();
            });
        }, this));

        //Select none
        selects.eq(2).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkSelect(function (slide) {
                slide.deSelect();
            });
        }, this));

        //Select published
        selects.eq(3).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkSelect(function (slide) {
                if (slide.publishElement.hasClass('n2-active')) {
                    slide.select();
                } else {
                    slide.deSelect();
                }
            });
        }, this));

        //Select unpublished
        selects.eq(4).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkSelect(function (slide) {
                if (slide.publishElement.hasClass('n2-active')) {
                    slide.deSelect();
                } else {
                    slide.select();
                }
            });
        }, this));

        var actions = $('.n2-bulk-action').find('a');

        //Delete
        actions.eq(0).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkAction('deleteSlides');
        }, this));

        //Duplicate
        actions.eq(1).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkAction('duplicateSlides');
        }, this));

        //Publish
        actions.eq(2).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkAction('publishSlides');
        }, this));

        //Unpublish
        actions.eq(3).on('click', $.proxy(function (e) {
            e.preventDefault();
            this.bulkAction('unPublishSlides');
        }, this));
    };

    NextendSmartSliderAdminSidebarSlides.prototype.bulkSelect = function (cb) {
        for (var i = 0; i < this.slides.length; i++) {
            cb(this.slides[i]);
        }
    };

    NextendSmartSliderAdminSidebarSlides.prototype.bulkAction = function (action) {
        var slides = [],
            ids = [];
        this.bulkSelect(function (slide) {
            if (slide.selected) {
                slides.push(slide);
                ids.push(slide.getId());
            }
        });
        if (ids.length) {
            this[action](ids, slides);
        } else {
            nextend.notificationCenter.notice('Please select one or more slides for the action!');
        }
    };

    NextendSmartSliderAdminSidebarSlides.prototype.enterBulk = function () {
        this.slidesContainer.sortable('option', 'disabled', true);
        $('#n2-admin').addClass('n2-slide-bulk-mode');

        for (var i = 0; i < this.slides.length; i++) {
            this.slides[i].selectMode();
        }
    };

    NextendSmartSliderAdminSidebarSlides.prototype.leaveBulk = function () {
        this.slidesContainer.sortable('option', 'disabled', false);
        $('#n2-admin').removeClass('n2-slide-bulk-mode');

        for (var i = 0; i < this.slides.length; i++) {
            this.slides[i].normalMode();
        }
    };

    NextendSmartSliderAdminSidebarSlides.prototype.deleteSlides = function (ids, slides) {
        var title = slides[0].box.find('.n2-box-button a').text();
        if (slides.length > 1) {
            title += ' and ' + (slides.length - 1) + ' more';
        }
        NextendDeleteModal('slide-delete', title, $.proxy(function () {
            NextendAjaxHelper.ajax({
                url: NextendAjaxHelper.makeAjaxUrl(this.ajaxUrl, {
                    nextendaction: 'delete'
                }),
                type: 'POST',
                data: {
                    slides: ids
                }
            }).done($.proxy(function () {
                for (var i = 0; i < slides.length; i++) {
                    slides[i].deleted();
                }
                this.initSlides();
            }, this));
        }, this));
    };

    NextendSmartSliderAdminSidebarSlides.prototype.duplicateSlides = function (ids, slides) {
        for (var i = 0; i < this.slides.length; i++) {
            if (this.slides[i].selected) {
                this.slides[i].duplicate($.Event("click", {
                    currentTarget: this.slides[i].box.find('.n2-slide-duplicate')
                })).done(function (slide) {
                    slide.selectMode();
                });
            }
        }
    };

    NextendSmartSliderAdminSidebarSlides.prototype.publishSlides = function (ids, slides) {
        NextendAjaxHelper.ajax({
            url: NextendAjaxHelper.makeAjaxUrl(this.ajaxUrl, {
                nextendaction: 'publish'
            }),
            type: 'POST',
            data: {
                slides: ids
            }
        }).done($.proxy(function () {
            for (var i = 0; i < slides.length; i++) {
                slides[i].published();
            }
            this.changed();
        }, this));
    };

    NextendSmartSliderAdminSidebarSlides.prototype.unPublishSlides = function (ids, slides) {
        NextendAjaxHelper.ajax({
            url: NextendAjaxHelper.makeAjaxUrl(this.ajaxUrl, {
                nextendaction: 'unpublish'
            }),
            type: 'POST',
            data: {
                slides: ids
            }
        }).done($.proxy(function () {
            for (var i = 0; i < slides.length; i++) {
                slides[i].unPublished();
            }
            this.changed();
        }, this));
    };
    scope.NextendSmartSliderAdminSidebarSlides = NextendSmartSliderAdminSidebarSlides;

    function NextendSmartSliderAdminSlide(manager, box) {
        this.selected = false;
        this.manager = manager;

        this.box = box.data('slide', this)
            .addClass('n2-clickable');
        this.normalMode();
        this.box.find('.n2-slide-first')
            .on('click', $.proxy(this.setFirst, this));
        this.publishElement = this.box.find('.n2-slide-published')
            .on('click', $.proxy(this.switchPublished, this));
        this.box.find('.n2-slide-duplicate')
            .on('click', $.proxy(this.duplicate, this));
        this.box.find('.n2-slide-delete')
            .on('click', $.proxy(this.delete, this));
    };

    NextendSmartSliderAdminSlide.prototype.getId = function () {
        return this.box.data('slideid');
    };
    NextendSmartSliderAdminSlide.prototype.setFirst = function (e) {
        e.stopPropagation();
        e.preventDefault();
        NextendAjaxHelper.ajax({
            url: NextendAjaxHelper.makeAjaxUrl(this.manager.ajaxUrl, {
                nextendaction: 'first'
            }),
            type: 'POST',
            data: {
                id: this.getId()
            }
        }).done($.proxy(function () {
            this.manager.unsetFirst();
            this.box.addClass('n2-first-slide');
        }, this));
    };
    NextendSmartSliderAdminSlide.prototype.unsetFirst = function () {
        this.box.removeClass('n2-first-slide');
    };

    NextendSmartSliderAdminSlide.prototype.switchPublished = function (e) {
        e.stopPropagation();
        e.preventDefault();
        if (this.isPublished()) {
            this.manager.unPublishSlides([this.getId()], [this]);
        } else {
            this.manager.publishSlides([this.getId()], [this]);
        }
    };

    NextendSmartSliderAdminSlide.prototype.isPublished = function () {
        return this.publishElement.hasClass('n2-active');
    };

    NextendSmartSliderAdminSlide.prototype.published = function () {
        this.publishElement.addClass('n2-active');
    };

    NextendSmartSliderAdminSlide.prototype.unPublished = function () {
        this.publishElement.removeClass('n2-active');
    };

    NextendSmartSliderAdminSlide.prototype.goToEdit = function (e) {
        window.location = this.box.data('editurl');
    };

    NextendSmartSliderAdminSlide.prototype.duplicate = function (e) {
        e.stopPropagation();
        e.preventDefault();
        var deferred = $.Deferred();
        NextendAjaxHelper.ajax({
            url: NextendAjaxHelper.makeAjaxUrl($(e.currentTarget).attr('href'), {
                nextendaction: 'duplicate'
            })
        }).done($.proxy(function (response) {
            var box = $(response.data).insertAfter(this.box);
            var newSlide = new NextendSmartSliderAdminSlide(this.manager, box);
            this.manager.initSlides();
            deferred.resolve(newSlide);
        }, this));
        return deferred;
    };

    NextendSmartSliderAdminSlide.prototype.delete = function (e) {
        e.stopPropagation();
        e.preventDefault();
        this.manager.deleteSlides([this.getId()], [this]);
    };
    NextendSmartSliderAdminSlide.prototype.deleted = function () {
        this.box.remove();
    };

    NextendSmartSliderAdminSlide.prototype.selectMode = function () {
        this.box.off('.n2-slide');
        this.box.on('click.n2-slide', $.proxy(this.invertSelection, this));
    };

    NextendSmartSliderAdminSlide.prototype.normalMode = function () {
        this.box.off('.n2-slide');
        this.box.on('click.n2-slide', $.proxy(this.goToEdit, this));
        this.deSelect();
    };

    NextendSmartSliderAdminSlide.prototype.invertSelection = function (e) {
        if (e) {
            e.preventDefault();
        }

        if (!this.selected) {
            this.select();
        } else {
            this.deSelect();
        }
    };

    NextendSmartSliderAdminSlide.prototype.select = function () {
        this.selected = true;
        this.box.addClass('n2-active');
    };

    NextendSmartSliderAdminSlide.prototype.deSelect = function () {
        this.selected = false;
        this.box.removeClass('n2-active');
    };

    scope.NextendSmartSliderAdminSlide = NextendSmartSliderAdminSlide;
})(n2, window);