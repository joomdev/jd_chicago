(function ($) {

    var versionParts = $.ui.version.match(/([0-9]+)\.([0-9]+)/),
        isOld = false;
    if (versionParts && versionParts[1] <= 1 && versionParts[2] <= 10) {
        isOld = true;
    }

    $.widget("ui.nextendAutocomplete", $.ui.autocomplete, {
        _renderMenu: function (ul, items) {
            ul.removeAttr('tabindex');
            var that = this;
            $.each(items, function (index, item) {
                that._renderItemData(ul, item);
            });
        }
    });


    $.widget("ui.nextenddraggable", $.ui.draggable, {
        _create: function () {
            if (isOld) {
                this.element.data('ui-draggable', this);
            }
            return this._superApply(arguments);
        },
        _setContainment: function () {
            var isUserScrollable, c, ce,
                o = this.options,
                document = this.document[0];

            this.relativeContainer = null;

            if (!o.containment) {
                this.containment = null;
                return;
            }

            if (o.containment === "window") {
                this.containment = [
                    $(window).scrollLeft() - this.offset.relative.left - this.offset.parent.left,
                    $(window).scrollTop() - this.offset.relative.top - this.offset.parent.top,
                    $(window).scrollLeft() + $(window).width() - this.helperProportions.width - this.margins.left,
                    $(window).scrollTop() + ( $(window).height() || document.body.parentNode.scrollHeight ) - this.helperProportions.height - this.margins.top
                ];
                return;
            }

            if (o.containment === "document") {
                this.containment = [
                    0,
                    0,
                    $(document).width() - this.helperProportions.width - this.margins.left,
                    ( $(document).height() || document.body.parentNode.scrollHeight ) - this.helperProportions.height - this.margins.top
                ];
                return;
            }

            if (o.containment.constructor === Array) {
                this.containment = o.containment;
                return;
            }

            if (o.containment === "parent") {
                o.containment = this.helper[0].parentNode;
            }

            c = $(o.containment);
            ce = c[0];

            if (!ce) {
                return;
            }

            this.containment = [
                ( parseInt(c.css("borderLeftWidth"), 10) || 0 ) + ( parseInt(c.css("paddingLeft"), 10) || 0 ),
                ( parseInt(c.css("borderTopWidth"), 10) || 0 ) + ( parseInt(c.css("paddingTop"), 10) || 0 ),
                ce.offsetWidth -
                ( parseInt(c.css("borderRightWidth"), 10) || 0 ) -
                ( parseInt(c.css("paddingRight"), 10) || 0 ) -
                this.helperProportions.width -
                this.margins.left -
                this.margins.right,
                ce.offsetHeight -
                ( parseInt(c.css("borderBottomWidth"), 10) || 0 ) -
                ( parseInt(c.css("paddingBottom"), 10) || 0 ) -
                this.helperProportions.height -
                this.margins.top -
                this.margins.bottom
            ];
            this.relativeContainer = c;
        }
    });


    $.ui.plugin.add("nextenddraggable", "smartguides", {
        start: function (event, ui) {
            var i = $(this).data("uiNextenddraggable"), o = i.options;
            i.gridH = $('<div class="n2-grid n2-grid-h"></div>').appendTo(o._containment);
            i.gridV = $('<div class="n2-grid n2-grid-v"></div>').appendTo(o._containment);
            i.elements = [];
            if (typeof o.smartguides == 'function') {
                var guides = o.smartguides();
                if (guides) {
                    guides.each(function () {
                        var $t = $(this);
                        var $o = $t.offset();
                        if (this != i.element[0]) i.elements.push({
                            item: this,
                            width: $t.outerWidth(), height: $t.outerHeight(),
                            top: $o.top, left: $o.left,
                            backgroundColor: ''
                        });
                    });
                    var $o = o._containment.offset();
                    i.elements.push({
                        width: o._containment.width(), height: o._containment.height(),
                        top: $o.top, left: $o.left,
                        backgroundColor: '#ff4aff'
                    });
                }
            }
        },
        stop: function (event, ui) {
            var i = $(this).data("uiNextenddraggable");
            i.gridH.remove();
            i.gridV.remove();
        },
        drag: function (event, ui) {
            var vElement = false,
                hElement = false;
            var inst = $(this).data("uiNextenddraggable"), o = inst.options;
            var d = o.tolerance;
            inst.gridH.css({"display": "none"});
            inst.gridV.css({"display": "none"});

            var container = inst.elements[inst.elements.length - 1];

            function setGridV(left) {
                inst.gridV.css({left: Math.min(left, container.width - 1), display: "block"});
            };
            function setGridH(top) {
                inst.gridH.css({top: Math.min(top, container.height - 1), display: "block"});
            };

            var ctrlKey = event.ctrlKey || event.metaKey,
                altKey = event.altKey;
            if (ctrlKey && altKey) {
                return;
            } else if (ctrlKey) {
                vElement = true;
            } else if (altKey) {
                hElement = true;
            }


            var x1 = ui.offset.left, x2 = x1 + inst.helperProportions.width,
                y1 = ui.offset.top, y2 = y1 + inst.helperProportions.height,
                xc = (x1 + x2) / 2,
                yc = (y1 + y2) / 2;
            for (var i = inst.elements.length - 1; i >= 0; i--) {

                if (!vElement) {
                    var l = inst.elements[i].left,
                        r = l + inst.elements[i].width,
                        hc = (l + r) / 2;

                    var v = true;
                    if (Math.abs(l - x2) <= d) {
                        ui.position.left = inst._convertPositionTo("relative", {
                            top: 0,
                            left: l - inst.helperProportions.width
                        }).left - inst.margins.left;
                        setGridV(ui.position.left + inst.helperProportions.width);
                    } else if (Math.abs(l - x1) <= d) {
                        ui.position.left = inst._convertPositionTo("relative", {
                            top: 0,
                            left: l
                        }).left - inst.margins.left;
                        setGridV(ui.position.left);
                    } else if (Math.abs(r - x1) <= d) {
                        ui.position.left = inst._convertPositionTo("relative", {
                            top: 0,
                            left: r
                        }).left - inst.margins.left;
                        setGridV(ui.position.left);
                    } else if (Math.abs(r - x2) <= d) {
                        ui.position.left = inst._convertPositionTo("relative", {
                            top: 0,
                            left: r - inst.helperProportions.width
                        }).left - inst.margins.left;
                        setGridV(ui.position.left + inst.helperProportions.width);
                    } else if (Math.abs(hc - x2) <= d) {
                        ui.position.left = inst._convertPositionTo("relative", {
                            top: 0,
                            left: hc - inst.helperProportions.width
                        }).left - inst.margins.left;
                        setGridV(ui.position.left + inst.helperProportions.width);
                    } else if (Math.abs(hc - x1) <= d) {
                        ui.position.left = inst._convertPositionTo("relative", {
                            top: 0,
                            left: hc
                        }).left - inst.margins.left;
                        setGridV(ui.position.left);
                    } else if (Math.abs(hc - xc) <= d) {
                        ui.position.left = inst._convertPositionTo("relative", {
                            top: 0,
                            left: hc - inst.helperProportions.width / 2
                        }).left - inst.margins.left;
                        setGridV(ui.position.left + inst.helperProportions.width / 2);
                    } else {
                        v = false;
                    }

                    if (v) {
                        vElement = inst.elements[i];
                    }
                }

                if (!hElement) {
                    var t = inst.elements[i].top,
                        b = t + inst.elements[i].height,
                        vc = (t + b) / 2;

                    var h = true;
                    if (Math.abs(t - y2) <= d) {
                        ui.position.top = inst._convertPositionTo("relative", {
                            top: t - inst.helperProportions.height,
                            left: 0
                        }).top - inst.margins.top;
                        setGridH(ui.position.top + inst.helperProportions.height);
                    } else if (Math.abs(t - y1) <= d) {
                        ui.position.top = inst._convertPositionTo("relative", {
                            top: t,
                            left: 0
                        }).top - inst.margins.top;
                        setGridH(ui.position.top);
                    } else if (Math.abs(b - y1) <= d) {
                        ui.position.top = inst._convertPositionTo("relative", {top: b, left: 0}).top - inst.margins.top;
                        setGridH(ui.position.top);
                    } else if (Math.abs(b - y2) <= d) {
                        ui.position.top = inst._convertPositionTo("relative", {
                            top: b - inst.helperProportions.height,
                            left: 0
                        }).top - inst.margins.top;
                        setGridH(ui.position.top + inst.helperProportions.height);
                    } else if (Math.abs(vc - y2) <= d) {
                        ui.position.top = inst._convertPositionTo("relative", {
                            top: vc - inst.helperProportions.height,
                            left: 0
                        }).top - inst.margins.top;
                        setGridH(ui.position.top + inst.helperProportions.height);
                    } else if (Math.abs(vc - y1) <= d) {
                        ui.position.top = inst._convertPositionTo("relative", {
                            top: vc,
                            left: 0
                        }).top - inst.margins.top;
                        setGridH(ui.position.top);
                    } else if (Math.abs(vc - yc) <= d) {
                        ui.position.top = inst._convertPositionTo("relative", {
                            top: vc - inst.helperProportions.height / 2,
                            left: 0
                        }).top - inst.margins.top;
                        setGridH(ui.position.top + inst.helperProportions.height / 2);
                    } else {
                        h = false;
                    }

                    if (h) {
                        hElement = inst.elements[i];
                    }
                }
            }
            if (vElement && vElement !== true) {
                inst.gridV.css('backgroundColor', vElement.backgroundColor);
            }
            if (hElement && hElement !== true) {
                inst.gridH.css('backgroundColor', hElement.backgroundColor);
            }
        }
    });


    $.widget("ui.nextendResizable", $.ui.resizable, {
        _mouseStart: function (t) {
            var position = this.element.position();
            this.element.css({
                left: position.left,
                top: position.top,
                right: 'auto',
                bottom: 'auto'
            });
            return this._superApply(arguments);
        }
    });

    $.ui.plugin.add("nextendResizable", "smartguides", {
        start: function (event, ui) {
            var i = $(this).data("uiNextendResizable"), o = i.options;
            i.gridH = $('<div class="n2-grid n2-grid-h"></div>').appendTo(o._containment);
            i.gridV = $('<div class="n2-grid n2-grid-v"></div>').appendTo(o._containment);
            i.gridH2 = $('<div class="n2-grid n2-grid-h"></div>').appendTo(o._containment);
            i.gridV2 = $('<div class="n2-grid n2-grid-v"></div>').appendTo(o._containment);
            i.elements = [];
            if (typeof o.smartguides == 'function') {
                var guides = o.smartguides();
                if (guides) {
                    guides.each(function () {
                        var $t = $(this);
                        var $o = $t.position();
                        if (this != i.element[0]) i.elements.push({
                            item: this,
                            width: $t.outerWidth(), height: $t.outerHeight(),
                            top: $o.top, left: $o.left
                        });
                    });
                    var $o = o._containment.offset();
                    i.elements.push({
                        item: o._containment,
                        width: o._containment.width(), height: o._containment.height(),
                        top: 0, left: 0
                    });
                }
            }
        },
        stop: function (event, ui) {
            var i = $(this).data("uiNextendResizable");
            i.gridH.remove();
            i.gridV.remove();
            i.gridH2.remove();
            i.gridV2.remove();
        },
        resize: function (event, ui) {
            var inst = $(this).data("uiNextendResizable"), o = inst.options;
            var d = o.tolerance;
            inst.gridV.css({"display": "none"});
            inst.gridH.css({"display": "none"});
            inst.gridV2.css({"display": "none"});
            inst.gridH2.css({"display": "none"});


            var container = inst.elements[inst.elements.length - 1];

            function setGridV(left) {
                inst.gridV.css({left: Math.min(left, container.width - 1), display: "block"});
            };
            function setGridV2(left) {
                inst.gridV2.css({left: Math.min(left, container.width - 1), display: "block"});
            };
            function setGridH(top) {
                inst.gridH.css({top: Math.min(top, container.height - 1), display: "block"});
            };
            function setGridH2(top) {
                inst.gridH2.css({top: Math.min(top, container.height - 1), display: "block"});
            };

            var ctrlKey = event.ctrlKey || event.metaKey,
                altKey = event.altKey;
            if (ctrlKey && altKey) {
                return;
            }

            var x1 = ui.position.left, x2 = x1 + ui.size.width,
                y1 = ui.position.top, y2 = y1 + ui.size.height;
            for (var i = inst.elements.length - 1; i >= 0; i--) {
                var l = inst.elements[i].left, r = l + inst.elements[i].width,
                    t = inst.elements[i].top, b = t + inst.elements[i].height;

                if (!ctrlKey) {
                    var hc = (l + r) / 2;

                    if (Math.abs(l - x2) <= d) {
                        ui.size.width = l - ui.position.left;
                        setGridV(ui.position.left + ui.size.width);
                    } else if (Math.abs(l - x1) <= d) {
                        var diff = ui.position.left - l;
                        ui.position.left = l;
                        ui.size.width += diff;
                        setGridV(ui.position.left);
                    } else if (Math.abs(hc - x1) <= d) {
                        var diff = ui.position.left - hc;
                        ui.position.left = hc;
                        ui.size.width += diff;
                        setGridV(ui.position.left);
                    }

                    if (Math.abs(r - x1) <= d) {
                        var diff = ui.position.left - r;
                        ui.position.left = r;
                        ui.size.width += diff;
                        setGridV2(ui.position.left);
                    } else if (Math.abs(r - x2) <= d) {
                        ui.size.width = r - ui.position.left;
                        setGridV2(ui.position.left + ui.size.width);
                    } else if (Math.abs(hc - x2) <= d) {
                        ui.size.width = hc - ui.position.left;
                        setGridV2(ui.position.left + ui.size.width);
                    }
                }

                if (!altKey) {
                    var vc = (t + b) / 2;

                    if (Math.abs(t - y2) <= d) {
                        ui.size.height = t - ui.position.top;
                        setGridH(t);
                    } else if (Math.abs(t - y1) <= d) {
                        var diff = ui.position.top - t;
                        ui.position.top = t;
                        ui.size.height += diff;
                        setGridH(ui.position.top);
                    } else if (Math.abs(vc - y1) <= d) {
                        var diff = ui.position.top - vc;
                        ui.position.top = vc;
                        ui.size.height += diff;
                        setGridH(ui.position.top);
                    }

                    if (Math.abs(b - y1) <= d) {
                        var diff = ui.position.top - b;
                        ui.position.top = b;
                        ui.size.height += diff;
                        setGridH2(ui.position.top);
                    } else if (Math.abs(b - y2) <= d) {
                        ui.size.height = b - ui.position.top;
                        setGridH2(ui.position.top + ui.size.height);
                    } else if (Math.abs(vc - y2) <= d) {
                        ui.size.height = vc - ui.position.top;
                        setGridH2(ui.position.top + ui.size.height);
                    }
                }
            }
        }
    });
})(n2);