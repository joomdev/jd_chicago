(function (smartSlider, $, scope, undefined) {
    "use strict";
    function Generator() {
        this._refreshTimeout = null;
        this.modal = false;
        this.group = 0;
        smartSlider.generator = this;
        var variables = smartSlider.$currentSlideElement.data('variables');
        if (variables) {
            this.variables = variables;

            for (var i in this.variables) {
                if (!isNaN(parseFloat(i)) && isFinite(i)) {
                    this.group = Math.max(this.group, parseInt(i) + 1);
                }
            }

            this.fill = this.generatorFill;
            if (this.group > 0) {
                this.registerField = this.generatorRegisterField;

                this.button = $('<a href="#" class="n2-form-element-button n2-form-element-button-inverted n2-h5 n2-uc" style="position:absolute; left: -26px; top:50%;margin-top: -14px;font-size: 14px; padding:0; width: 28px;text-align: center;">$</a>')
                    .on('click', $.proxy(function (e) {
                        e.preventDefault();
                        this.showModal();
                    }, this));
                this.registerField($('#slidetitle'));
                this.registerField($('#slidedescription'));
                this.registerField($('#slidethumbnail'));
                this.registerField($('#slidebackgroundImage'));
                this.registerField($('#slidebackgroundAlt'));
                this.registerField($('#slidebackgroundVideoMp4'));
                this.registerField($('#slidebackgroundVideoWebm'));
                this.registerField($('#slidebackgroundVideoOgg'));
                this.registerField($('#linkslidelink_0'));

                //this.showModal();
            }

            this.initSlideDataRefresh();
        } else {
            this.variables = null;
        }
    };

    Generator.prototype.fill = function (value) {
        return value;
    };

    Generator.prototype.generatorFill = function (value) {
        return value.replace(/{((([a-z]+)\(([0-9a-zA-Z_,\/\(\)]+)\))|([a-zA-Z0-9_\/]+))}/g, $.proxy(this.parseFunction, this));
    };

    Generator.prototype.parseFunction = function (s, s2, s3, functionName, argumentString, variable) {
        if (typeof variable == 'undefined') {
            var args = argumentString.split(/,(?!.*\))/);
            for (var i = 0; i < args.length; i++) {
                args[i] = this.parseVariable(args[i]);
            }
            return this[functionName].apply(this, args);
        } else {
            return this.parseVariable(variable);
        }
    };

    Generator.prototype.parseVariable = function (variable) {

        var functionMatch = variable.match(/((([a-z]+)\(([0-9a-zA-Z_,\/\(\)]+)\)))/);
        if (functionMatch) {
            return this.parseFunction.apply(this, functionMatch);
        } else {
            var variableMatch = variable.match(/([a-zA-Z][0-9a-zA-Z_]*)(\/([0-9a-z]+))?/);
            if (variableMatch) {
                var index = variableMatch[3];
                if (typeof index == 'undefined') {
                    index = 0;
                } else {
                    var i = parseInt(index);
                    if (!isNaN(i)) {
                        index = Math.max(index, 1) - 1;
                    }
                }
                if (typeof this.variables[index] != 'undefined' && typeof this.variables[index][variableMatch[1]] != 'undefined') {
                    return this.variables[index][variableMatch[1]];
                }
                return '';
            }
            return variable;
        }
    };

    Generator.prototype.cleanhtml = function (variable) {
        return strip_tags(variable, '<p><a><b><br /><br/><i>');
    };

    Generator.prototype.removehtml = function (variable) {
        return $('<div>' + variable + '</div>').text();
    };

    Generator.prototype.splitbychars = function (s, start, length) {
        return s.substr(start, length);
    };

    Generator.prototype.splitbywords = function (variable, start, length) {
        var s = variable,
            len = s.length,
            posStart = Math.max(0, start == 0 ? 0 : s.indexOf(' ', start)),
            posEnd = Math.max(0, length > len ? len : s.indexOf(' ', length));
        return s.substr(posStart, posEnd);
    };

    Generator.prototype.findimage = function (variable, index) {
        var s = variable,
            re = /(<img.*?src=[\'"](.*?)[\'"][^>]*>)|(background(-image)??\s*?:.*?url\((["|\']?)?(.+?)(["|\']?)?\))/gi,
            r = [],
            tmp = null;

        index = typeof index != 'undefined' ? parseInt(index) - 1 : 0;

        while (tmp = re.exec(s)) {        
            if (typeof tmp[2] != 'undefined') {
                r.push(tmp[2]);
            } else if (typeof tmp[6] != 'undefined') {
                r.push(tmp[6]);
            }
        }

        if (r.length) {
            if (r.length > index) {
                return r[index];
            } else {
                return r[r.length - 1];
            }
        } else {
            return '';
        }
    };
    
    Generator.prototype.findlink = function (variable, index) {
        var s = variable,
            re = /href=["\']?([^"\'>]+)["\']?/gi,
            r = [],
            tmp = null;

        index = typeof index != 'undefined' ? parseInt(index) - 1 : 0;
        
        while (tmp = re.exec(s)) {
            if (typeof tmp[1] != 'undefined') {
                r.push(tmp[1]);
            }
        }

        if (r.length) {
            if (r.length > index) {
                return r[index];
            } else {
                return r[r.length - 1];
            }
        } else {
            return '';
        }
    };

    Generator.prototype.registerField = function (field) {
    };

    Generator.prototype.generatorRegisterField = function (field) {
        var parent = field.parent();
        parent.on({
            mouseenter: $.proxy(function () {
                this.activeField = field;
                this.button.prependTo(parent);
            }, this)
        });
    };

    Generator.prototype.getModal = function () {
        var that = this;
        if (!this.modal) {
            var active = {
                    key: '',
                    group: 1,
                    filter: 'no',
                    split: 'no',
                    splitStart: 0,
                    splitLength: 300,
                    findImage: 0,
                    findImageIndex: 1,
                    findLink: 0,
                    findLinkIndex: 1
                },
                getVariableString = function () {
                    var variable = active.key + '/' + active.group;
                    if (active.findImage) {
                        variable = 'findimage(' + variable + ',' + Math.max(1, active.findImageIndex) + ')';
                    }
                    if (active.findLink) {
                        variable = 'findlink(' + variable + ',' + Math.max(1, active.findLinkIndex) + ')';
                    }
                    if (active.filter != 'no') {
                        variable = active.filter + '(' + variable + ')';
                    }
                    if (active.split != 'no' && active.splitStart >= 0 && active.splitLength > 0) {
                        variable = active.split + '(' + variable + ',' + active.splitStart + ',' + active.splitLength + ')';
                    }
                    return '{' + variable + '}';
                },
                resultContainer = $('<div class="n2-generator-result-container" />'),
                updateResult = function () {
                    resultContainer.html($('<div/>').text(that.fill(getVariableString())).html());
                };

            var group = that.group,
                variables = null,
                groups = null,
                content = $('<div class="n2-generator-insert-variable"/>');


            var groupHeader = NextendModal.prototype.createHeading(n2_('Choose the group')).appendTo(content);
            var groupContainer = $('<div class="n2-group-container" />').appendTo(content);


            content.append(NextendModal.prototype.createHeading(n2_('Choose the variable')));
            var variableContainer = $('<div class="n2-variable-container" />').appendTo(content);

            //content.append(NextendModal.prototype.createHeading('Functions'));
            var functionsContainer = $('<div class="n2-generator-functions-container n2-form-element-mixed" />')
                .appendTo($('<div class="n2-form" />').appendTo(content));

            content.append(NextendModal.prototype.createHeading(n2_('Result')));
            resultContainer.appendTo(content);


            $('<div class="n2-mixed-group"><div class="n2-mixed-label"><label>' + n2_('Filter') + '</label></div><div class="n2-mixed-element"><div class="n2-form-element-list"><select autocomplete="off" name="filter" id="n2-generator-function-filter"><option selected="selected" value="no">' + n2_('No') + '</option><option value="cleanhtml">' + n2_('Clean HTML') + '</option><option value="removehtml">' + n2_('Remove HTML') + '</option></select></div></div></div>')
                .appendTo(functionsContainer);
            var filter = functionsContainer.find('#n2-generator-function-filter');
            filter.on('change', $.proxy(function () {
                active.filter = filter.val();
                updateResult();
            }, this));


            $('<div class="n2-mixed-group"><div class="n2-mixed-label"><label>' + n2_('Split by chars') + '</label></div><div class="n2-mixed-element"><div class="n2-form-element-list"><select autocomplete="off" name="split" id="n2-generator-function-split"><option selected="selected" value="no">' + n2_('No') + '</option><option value="splitbychars">' + n2_('Strict') + '</option><option value="splitbywords">' + n2_('Respect words') + '</option></select></div><div class="n2-form-element-text n2-text-has-unit n2-border-radius"><div class="n2-text-sub-label n2-h5 n2-uc">' + n2_('Start') + '</div><input type="text" autocomplete="off" style="width: 22px;" class="n2-h5" value="0" id="n2-generator-function-split-start"></div><div class="n2-form-element-text n2-text-has-unit n2-border-radius"><div class="n2-text-sub-label n2-h5 n2-uc">' + n2_('Length') + '</div><input type="text" autocomplete="off" style="width: 22px;" class="n2-h5" value="300" id="n2-generator-function-split-length"></div></div></div>')
                .appendTo(functionsContainer);
            var split = functionsContainer.find('#n2-generator-function-split');
            split.on('change', $.proxy(function () {
                active.split = split.val();
                updateResult();
            }, this));
            var splitStart = functionsContainer.find('#n2-generator-function-split-start');
            splitStart.on('change', $.proxy(function () {
                active.splitStart = parseInt(splitStart.val());
                updateResult();
            }, this));
            var splitLength = functionsContainer.find('#n2-generator-function-split-length');
            splitLength.on('change', $.proxy(function () {
                active.splitLength = parseInt(splitLength.val());
                updateResult();
            }, this));


            $('<div class="n2-mixed-group"><div class="n2-mixed-label"><label>' + n2_('Find image') + '</label></div><div class="n2-mixed-element"><div class="n2-form-element-onoff"><div class="n2-onoff-slider"><div class="n2-onoff-no"><i class="n2-i n2-i-close"></i></div><div class="n2-onoff-round"></div><div class="n2-onoff-yes"><i class="n2-i n2-i-tick"></i></div></div><input type="hidden" autocomplete="off" value="0" id="n2-generator-function-findimage"></div><div class="n2-form-element-text n2-text-has-unit n2-border-radius"><div class="n2-text-sub-label n2-h5 n2-uc">' + n2_('Index') + '</div><input type="text" autocomplete="off" style="width: 22px;" class="n2-h5" value="1" id="n2-generator-function-findimage-index"></div></div></div>')
                .appendTo(functionsContainer);

            var findImage = functionsContainer.find('#n2-generator-function-findimage');
            findImage.on('nextendChange', $.proxy(function () {
                active.findImage = parseInt(findImage.val());
                updateResult();
            }, this));
            var findImageIndex = functionsContainer.find('#n2-generator-function-findimage-index');
            findImageIndex.on('change', $.proxy(function () {
                active.findImageIndex = parseInt(findImageIndex.val());
                updateResult();
            }, this));


            $('<div class="n2-mixed-group"><div class="n2-mixed-label"><label>' + n2_('Find link') + '</label></div><div class="n2-mixed-element"><div class="n2-form-element-onoff"><div class="n2-onoff-slider"><div class="n2-onoff-no"><i class="n2-i n2-i-close"></i></div><div class="n2-onoff-round"></div><div class="n2-onoff-yes"><i class="n2-i n2-i-tick"></i></div></div><input type="hidden" autocomplete="off" value="0" id="n2-generator-function-findlink"></div><div class="n2-form-element-text n2-text-has-unit n2-border-radius"><div class="n2-text-sub-label n2-h5 n2-uc">' + n2_('Index') + '</div><input type="text" autocomplete="off" style="width: 22px;" class="n2-h5" value="1" id="n2-generator-function-findlink-index"></div></div></div>')
                .appendTo(functionsContainer);

            var findLink = functionsContainer.find('#n2-generator-function-findlink');
            findLink.on('nextendChange', $.proxy(function () {
                active.findLink = parseInt(findLink.val());
                updateResult();
            }, this));
            var findLinkIndex = functionsContainer.find('#n2-generator-function-findlink-index');
            findLinkIndex.on('change', $.proxy(function () {
                active.findLinkIndex = parseInt(findLinkIndex.val());
                updateResult();
            }, this));

            for (var k in this.variables[0]) {
                $('<a href="#" class="n2-button n2-button-small n2-button-grey">' + k + '</a>')
                    .on('click', $.proxy(function (key, e) {
                        e.preventDefault();
                        variables.removeClass('n2-active');
                        $(e.currentTarget).addClass('n2-active');
                        active.key = key;
                        updateResult();
                    }, this, k))
                    .appendTo(variableContainer);
            }

            variables = variableContainer.find('a');
            variables.eq(0).trigger('click');

            if (group == 1) {
                groupHeader.css('display', 'none');
                groupContainer.css('display', 'none');
            }
            for (var i = 0; i < group; i++) {
                $('<a href="#" class="n2-button n2-button-small n2-button-grey">' + (i + 1) + '</a>')
                    .on('click', $.proxy(function (groupIndex, e) {
                        e.preventDefault();
                        groups.removeClass('n2-active');
                        $(e.currentTarget).addClass('n2-active');
                        active.group = groupIndex + 1;
                        updateResult();
                    }, this, i))
                    .appendTo(groupContainer);
            }
            groups = groupContainer.find('a');
            groups.eq(0).trigger('click');

            var inited = false;

            this.modal = new NextendModal({
                zero: {
                    size: [
                        1000,
                        group > 1 ? 560 : 490
                    ],
                    title: n2_('Insert variable'),
                    back: false,
                    close: true,
                    content: content,
                    controls: ['<a href="#" class="n2-button n2-button-big n2-button-green">' + n2_('Insert') + '</a>'],
                    fn: {
                        show: function () {
                            if (!inited) {
                                new NextendElementOnoff("n2-generator-function-findimage");
                                new NextendElementOnoff("n2-generator-function-findlink");
                                inited = true;
                            }
                            this.controls.find('.n2-button').on('click', $.proxy(function (e) {
                                e.preventDefault();
                                that.insert(getVariableString());
                                this.hide(e);
                            }, this));
                        }
                    }
                }
            }, false);

            this.modal.setCustomClass('n2-ss-generator-modal');
        }
        return this.modal;
    };

    Generator.prototype.showModal = function () {

        this.getModal().show();
    };

    Generator.prototype.insert = function (value) {
        this.activeField.val(value).trigger('change');
    };

    Generator.prototype.initSlideDataRefresh = function () {

        var name = $('#slidetitle').on('nextendChange', $.proxy(function () {
            this.variables.slide.name = name.val();
            this.refresh();
        }, this));

        var description = $('#slidedescription').on('nextendChange', $.proxy(function () {
            this.variables.slide.description = description.val();
            this.refresh();
        }, this));

    };


    Generator.prototype.refresh = function () {
        if (this._refreshTimeout) {
            clearTimeout(this._refreshTimeout);
            this._refreshTimeout = null;
        }
        this._refreshTimeout = setTimeout($.proxy(this._refresh, this), 100);
    };

    Generator.prototype._refresh = function () {
        var layers = smartSlider.layerManager.layerList;
        for (var j = 0; j < layers.length; j++) {
            var items = layers[j].items;
            for (var i = 0; i < items.length; i++) {
                items[i].reRender();
            }
        }
    };


    scope.SmartSliderAdminGenerator = Generator;

})(nextend.smartSlider, n2, window);