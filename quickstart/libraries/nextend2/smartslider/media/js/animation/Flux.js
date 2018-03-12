(function ($, scope, undefined) {

    function NextendSmartSliderBackgroundAnimationFluxAbstract() {
        this.shiftedPreSetup = false;
        this._clonedCurrent = false;
        this._clonedNext = false;

        NextendSmartSliderBackgroundAnimationAbstract.prototype.constructor.apply(this, arguments);

        this.w = this.original.currentImage.width();
        this.h = this.original.currentImage.height();
    };

    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype = Object.create(NextendSmartSliderBackgroundAnimationAbstract.prototype);
    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.constructor = NextendSmartSliderBackgroundAnimationFluxAbstract;

    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.clonedCurrent = function () {
        if (!this._clonedCurrent) {
            this._clonedCurrent = this.original.currentImage
                .clone()
                .css({
                    width: this.w,
                    height: this.h
                });
        }
        return this._clonedCurrent;
    };

    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.clonedNext = function () {
        if (!this._clonedNext) {
            this._clonedNext = this.original.nextImage
                .clone()
                .css({
                    width: this.w,
                    height: this.h
                });
        }
        return this._clonedNext;
    };

    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.preSetup = function () {
        if (this.shiftedBackgroundAnimation != 0) {
            this.shiftedPreSetup = true;
        } else {
            this._preSetup();
        }
    };

    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype._preSetup = function (skipFadeOut) {
        this.timeline.to(this.original.currentImage.get(0), this.getExtraDelay(), {
            opacity: 0
        }, 0);

        this.original.nextImage.css('opacity', 0);
    };

    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.postSetup = function () {
        this.timeline.to(this.original.nextImage.get(0), this.getExtraDelay(), {
            opacity: 1
        });
    };

    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.getExtraDelay = function () {
        return .2;
    };

    NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.ended = function () {
        this.original.currentImage.css('opacity', 1);
        this.containerElement.html('');
    };

    scope.NextendSmartSliderBackgroundAnimationFluxAbstract = NextendSmartSliderBackgroundAnimationFluxAbstract;


    function NextendSmartSliderBackgroundAnimationTiled() {
        NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.constructor.apply(this, arguments);

        this.setup();
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype = Object.create(NextendSmartSliderBackgroundAnimationFluxAbstract.prototype);
    NextendSmartSliderBackgroundAnimationTiled.prototype.constructor = NextendSmartSliderBackgroundAnimationTiled;

    NextendSmartSliderBackgroundAnimationTiled.prototype.setup = function (animation) {

        var container = $('<div></div>').css({
            position: 'absolute',
            left: 0,
            top: 0,
            width: this.w,
            height: this.h/*,
             overflow: 'hidden'*/
        });
        this.container = container;
        NextendTween.set(container.get(0), {
            force3D: true,
            perspective: 1000
        });

        var animatablesMulti = [],
            animatables = [];

        var columns = animation.columns,
            rows = animation.rows,
            colWidth = Math.floor(this.w / columns),
            rowHeight = Math.floor(this.h / rows);

        var colRemainder = this.w - (columns * colWidth),
            colAddPerLoop = Math.ceil(colRemainder / columns),
            rowRemainder = this.h - (rows * rowHeight),
            rowAddPerLoop = Math.ceil(rowRemainder / rows),
            totalLeft = 0;

        for (var col = 0; col < columns; col++) {
            animatablesMulti[col] = [];
            var thisColWidth = colWidth,
                totalTop = 0;

            if (colRemainder > 0) {
                var add = colRemainder >= colAddPerLoop ? colAddPerLoop : colRemainder;
                thisColWidth += add;
                colRemainder -= add;
            }

            var thisRowRemainder = rowRemainder;

            for (var row = 0; row < rows; row++) {
                var thisRowHeight = rowHeight;

                if (thisRowRemainder > 0) {
                    var add = thisRowRemainder >= rowAddPerLoop ? rowAddPerLoop : thisRowRemainder;
                    thisRowHeight += add;
                    thisRowRemainder -= add;
                }
                var tile = $('<div class="tile tile-' + col + '-' + row + '"></div>').css({
                    position: 'absolute',
                    top: totalTop + 'px',
                    left: totalLeft + 'px',
                    width: thisColWidth + 'px',
                    height: thisRowHeight + 'px',
                    zIndex: -Math.abs(col - parseInt(columns / 2)) + columns - Math.abs(row - parseInt(rows / 2))
                }).appendTo(container);

                var animatable = this.renderTile(tile, thisColWidth, thisRowHeight, animation, totalLeft, totalTop);
                animatables.push(animatable);
                animatablesMulti[col][row] = animatable;

                totalTop += thisRowHeight;
            }
            totalLeft += thisColWidth;
        }

        container.appendTo(this.containerElement);

        this.preSetup();

        this.animate(animation, animatables, animatablesMulti);
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.animate = function (animation, animatables, animatablesMulti) {
        this['sequence' + animation.tiles.sequence]($.proxy(this.transform, this, animation), animatables, animatablesMulti, animation.tiles.delay * this.durationMultiplier);
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.sequenceParallel = function (transform, cuboids) {
        transform(cuboids, null);
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.sequenceRandom = function (transform, cuboids, cuboidsMulti, delay) {
        var total = this.timeline.totalDuration();
        for (var i = 0; i < cuboids.length; i++) {
            transform(cuboids[i], total + Math.random() * delay);
        }
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.sequenceForwardCol = function (transform, cuboids, cuboidsMulti, delay) {
        var total = this.timeline.totalDuration();
        for (var i = 0; i < cuboids.length; i++) {
            transform(cuboids[i], total + delay * i);
        }
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.sequenceBackwardCol = function (transform, cuboids, cuboidsMulti, delay) {
        var total = this.timeline.totalDuration(),
            length = cuboids.length - 1;
        for (var i = 0; i < cuboids.length; i++) {
            transform(cuboids[i], total + delay * (length - i));
        }
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.sequenceForwardRow = function (transform, cuboids, cuboidsMulti, delay) {
        var total = this.timeline.totalDuration(),
            i = 0;
        for (var row = 0; row < cuboidsMulti[0].length; row++) {
            for (var col = 0; col < cuboidsMulti.length; col++) {
                transform(cuboidsMulti[col][row], total + delay * i);
                i++;
            }
        }
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.sequenceBackwardRow = function (transform, cuboids, cuboidsMulti, delay) {
        var total = this.timeline.totalDuration(),
            i = cuboids.length - 1;
        for (var row = 0; row < cuboidsMulti[0].length; row++) {
            for (var col = 0; col < cuboidsMulti.length; col++) {
                transform(cuboidsMulti[col][row], total + delay * i);
                i--;
            }
        }
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.sequenceForwardDiagonal = function (transform, cuboids, cuboidsMulti, delay) {
        var total = this.timeline.totalDuration();
        for (var row = 0; row < cuboidsMulti[0].length; row++) {
            for (var col = 0; col < cuboidsMulti.length; col++) {
                transform(cuboidsMulti[col][row], total + delay * (col + row));
            }
        }
    };

    NextendSmartSliderBackgroundAnimationTiled.prototype.sequenceBackwardDiagonal = function (transform, cuboids, cuboidsMulti, delay) {
        var total = this.timeline.totalDuration(),
            length = cuboidsMulti[0].length + cuboidsMulti.length - 2;
        for (var row = 0; row < cuboidsMulti[0].length; row++) {
            for (var col = 0; col < cuboidsMulti.length; col++) {
                transform(cuboidsMulti[col][row], total + delay * (length - col - row));
            }
        }
    };

    scope.NextendSmartSliderBackgroundAnimationTiled = NextendSmartSliderBackgroundAnimationTiled;


    function NextendSmartSliderBackgroundAnimationFlat() {
        NextendSmartSliderBackgroundAnimationTiled.prototype.constructor.apply(this, arguments);
    };

    NextendSmartSliderBackgroundAnimationFlat.prototype = Object.create(NextendSmartSliderBackgroundAnimationTiled.prototype);
    NextendSmartSliderBackgroundAnimationFlat.prototype.constructor = NextendSmartSliderBackgroundAnimationFlat;

    NextendSmartSliderBackgroundAnimationFlat.prototype.setup = function () {

        var animation = $.extend(true, {
            columns: 1,
            rows: 1,
            tiles: {
                cropOuter: false,
                crop: true,
                delay: 0, // Delay between the starting of the tiles sequence. Ex.: #1 batch start: 0s, #2: .2s, #3: .4s
                sequence: 'Parallel' // Parallel, Random, ForwardCol, BackwardCol, ForwardRow, BackwardRow, ForwardDiagonal, BackwardDiagonal
            },
            main: {
                type: 'next',  // Enable animation on the specified tile: current, next, both
                duration: 0.5,
                real3D: true, // Enable perspective
                zIndex: 1, // z-index of the current image. Change it to 2 to show it over the second image.
                current: { // Animation of the current tile
                    ease: 'easeInOutCubic'
                },
                next: { // Animation of the next tile
                    ease: 'easeInOutCubic'
                }
            }
        }, this.animationProperties);

        if (this.reversed) {
            if (typeof animation.invert !== 'undefined') {
                $.extend(true, animation.main, animation.invert);
            }

            if (typeof animation.invertTiles !== 'undefined') {
                $.extend(animation.tiles, animation.invertTiles);
            }
        }

        NextendSmartSliderBackgroundAnimationTiled.prototype.setup.call(this, animation);

        if (animation.tiles.cropOuter) {
            this.container.css('overflow', 'hidden');
        }
    };

    NextendSmartSliderBackgroundAnimationFlat.prototype.renderTile = function (tile, w, h, animation, totalLeft, totalTop) {

        if (animation.tiles.crop) {
            tile.css('overflow', 'hidden');
        }

        var current = $('<div></div>')
            .css({
                position: 'absolute',
                left: 0,
                top: 0,
                width: w,
                height: h,
                overflow: 'hidden',
                zIndex: animation.main.zIndex
            })
            .append(this.clonedCurrent().clone().css({
                position: 'absolute',
                top: -totalTop + 'px',
                left: -totalLeft + 'px'
            }))
            .appendTo(tile);
        var next = $('<div></div>')
            .css({
                position: 'absolute',
                left: 0,
                top: 0,
                width: w,
                height: h,
                overflow: 'hidden',
                zIndex: 1
            })
            .append(this.clonedNext().clone().css({
                position: 'absolute',
                top: -totalTop + 'px',
                left: -totalLeft + 'px'
            }))
            .appendTo(tile);

        if (animation.main.real3D) {
            NextendTween.set(tile.get(0), {
                transformStyle: "preserve-3d"
            });
            NextendTween.set(current.get(0), {
                transformStyle: "preserve-3d"
            });
            NextendTween.set(next.get(0), {
                transformStyle: "preserve-3d"
            });
        }

        return {
            current: current,
            next: next
        }
    };

    NextendSmartSliderBackgroundAnimationFlat.prototype.transform = function (animation, animatable, total) {

        var main = animation.main;

        if (main.type == 'current' || main.type == 'both') {
            this.timeline.to(animatable.current, main.duration * this.durationMultiplier, main.current, total);
        }

        if (main.type == 'next' || main.type == 'both') {
            this.timeline.from(animatable.next, main.duration * this.durationMultiplier, main.next, total);
        }
    };
    scope.NextendSmartSliderBackgroundAnimationFlat = NextendSmartSliderBackgroundAnimationFlat;


    function NextendSmartSliderBackgroundAnimationCubic() {
        NextendSmartSliderBackgroundAnimationTiled.prototype.constructor.apply(this, arguments);
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype = Object.create(NextendSmartSliderBackgroundAnimationTiled.prototype);
    NextendSmartSliderBackgroundAnimationCubic.prototype.constructor = NextendSmartSliderBackgroundAnimationCubic;


    NextendSmartSliderBackgroundAnimationCubic.prototype.setup = function () {
        var animation = $.extend(true, {
            columns: 1,
            rows: 1,
            fullCube: true,
            tiles: {
                delay: 0.2,  // Delay between the starting of the tiles sequence. Ex.: #1 batch start: 0s, #2: .2s, #3: .4s
                sequence: 'Parallel' // Parallel, Random, ForwardCol, BackwardCol, ForwardRow, BackwardRow, ForwardDiagonal, BackwardDiagonal
            },
            depth: 50, // Used only when side is "Back"
            main: {
                side: 'Left', // Left, Right, Top, Bottom, Back, BackInvert
                duration: 0.5,
                ease: 'easeInOutCubic',
                direction: 'horizontal', // horizontal, vertical // Used when side points to Back
                real3D: true // Enable perspective
            },
            pre: [], // Animations to play on tiles before main
            post: [] // Animations to play on tiles after main
        }, this.animationProperties);
        animation.fullCube = true;

        if (this.reversed) {
            if (typeof animation.invert !== 'undefined') {
                $.extend(true, animation.main, animation.invert);
            }

            if (typeof animation.invertTiles !== 'undefined') {
                $.extend(animation.tiles, animation.invertTiles);
            }
        }

        NextendSmartSliderBackgroundAnimationTiled.prototype.setup.call(this, animation);
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.renderTile = function (tile, w, h, animation, totalLeft, totalTop) {

        var d = animation.depth;

        switch (d) {
            case 'width':
                d = w;
                break;
            case 'height':
                d = h;
                break;
        }
        switch (animation.main.side) {
            case 'Top':
            case 'Bottom':
                d = h;
                break;
            case 'Left':
            case 'Right':
                d = w;
                break;
        }

        if (animation.main.real3D) {
            NextendTween.set(tile.get(0), {
                transformStyle: "preserve-3d"
            });
        }
        var cuboid = $('<div class="cuboid"></div>').css({
            position: 'absolute',
            left: '0',
            top: '0',
            width: '100%',
            height: '100%'
        }).appendTo(tile);
        NextendTween.set(cuboid.get(0), {
            transformStyle: "preserve-3d",
            z: -d / 2
        });

        var backRotationZ = 0;
        if (animation.main.direction == 'horizontal') {
            backRotationZ = 180;
        }
        var back = this.getSide(cuboid, w, h, 0, 0, -d / 2, 180, 0, backRotationZ),
            sides = {
                Back: back,
                BackInvert: back
            };
        if (animation.fullCube || animation.main.direction == 'vertical') {
            sides.Bottom = this.getSide(cuboid, w, d, 0, h - d / 2, 0, -90, 0, 0);
            sides.Top = this.getSide(cuboid, w, d, 0, -d / 2, 0, 90, 0, 0);
        }

        sides.Front = this.getSide(cuboid, w, h, 0, 0, d / 2, 0, 0, 0);
        if (animation.fullCube || animation.main.direction == 'horizontal') {
            sides.Left = this.getSide(cuboid, d, h, -d / 2, 0, 0, 0, -90, 0);
            sides.Right = this.getSide(cuboid, d, h, w - d / 2, 0, 0, 0, 90, 0);
        }

        sides.Front.append(this.clonedCurrent().clone().css({
            position: 'absolute',
            top: -totalTop + 'px',
            left: -totalLeft + 'px'
        }));

        sides[animation.main.side].append(this.clonedNext().clone().css({
            position: 'absolute',
            top: -totalTop + 'px',
            left: -totalLeft + 'px'
        }));

        return cuboid;
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.getSide = function (cuboid, w, h, x, y, z, rX, rY, rZ) {
        var side = $('<div class="n2-3d-side"></div>')
            .css({
                width: w,
                height: h
            })
            .appendTo(cuboid);
        NextendTween.set(side.get(0), {
            x: x,
            y: y,
            z: z,
            rotationX: rX,
            rotationY: rY,
            rotationZ: rZ,
            backfaceVisibility: "hidden"
        });
        return side;
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.addAnimation = function (animation, cuboids) {
        var duration = animation.duration;
        delete animation.duration;
        this.timeline.to(cuboids, duration * this.durationMultiplier, animation);
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.transform = function (animation, cuboid, position) {

        for (var i = 0; i < animation.pre.length; i++) {
            var _a = animation.pre[i];
            var duration = _a.duration * this.durationMultiplier;
            this.timeline.to(cuboid, duration, _a, position);
            position += duration;
        }

        this['transform' + animation.main.side](animation.main, cuboid, position);
        position += animation.main.duration;

        for (var i = 0; i < animation.post.length; i++) {
            var _a = animation.post[i];
            var duration = _a.duration * this.durationMultiplier;
            this.timeline.to(cuboid, duration, _a, position);
            position += duration;
        }
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.transformLeft = function (main, cuboid, total) {
        this._transform(main, cuboid, total, 0, 90, 0);
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.transformRight = function (main, cuboid, total) {
        this._transform(main, cuboid, total, 0, -90, 0);
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.transformTop = function (main, cuboid, total) {
        this._transform(main, cuboid, total, -90, 0, 0);
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.transformBottom = function (main, cuboid, total) {
        this._transform(main, cuboid, total, 90, 0, 0);
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.transformBack = function (main, cuboid, total) {
        if (main.direction == 'horizontal') {
            this._transform(main, cuboid, total, 0, 180, 0);
        } else {
            this._transform(main, cuboid, total, 180, 0, 0);
        }
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype.transformBackInvert = function (main, cuboid, total) {
        if (main.direction == 'horizontal') {
            this._transform(main, cuboid, total, 0, -180, 0);
        } else {
            this._transform(main, cuboid, total, -180, 0, 0);
        }
    };

    NextendSmartSliderBackgroundAnimationCubic.prototype._transform = function (main, cuboid, total, rX, rY, rZ) {
        this.timeline.to(cuboid, main.duration * this.durationMultiplier, {
            rotationX: rX,
            rotationY: rY,
            rotationZ: rZ,
            ease: main.ease
        }, total);
    };

    scope.NextendSmartSliderBackgroundAnimationCubic = NextendSmartSliderBackgroundAnimationCubic;


    function NextendSmartSliderBackgroundAnimationTurn() {
        NextendSmartSliderBackgroundAnimationFluxAbstract.prototype.constructor.apply(this, arguments);

        var animation = $.extend(true, {
            perspective: this.w * 1.5,
            duration: 0.8,
            direction: 'left'
        }, this.animationProperties);

        if (this.reversed) {
            if (animation.direction == 'left') {
                animation.direction = 'right';
            } else {
                animation.direction = 'left';
            }
        }

        var w2 = parseInt(this.w / 2);

        this.clonedCurrent().css({
            'position': 'absolute',
            'top': 0,
            'left': (animation.direction == 'left' ? -1 * (this.w / 2) : 0)
        });

        this.clonedNext().css({
            'position': 'absolute',
            'top': 0,
            'left': (animation.direction == 'left' ? 0 : -1 * (this.w / 2))
        });

        var tab = $('<div class="tab"></div>').css({
            width: w2,
            height: this.h,
            position: 'absolute',
            top: '0px',
            left: animation.direction == 'left' ? w2 : '0',
            'z-index': 101
        });

        NextendTween.set(tab, {
            transformStyle: 'preserve-3d',
            transformOrigin: animation.direction == 'left' ? '0px 0px' : w2 + 'px 0px'
        });

        var front = $('<div class="n2-ff-3d"></div>').append(this.clonedCurrent())
            .css({
                width: w2,
                height: this.h,
                position: 'absolute',
                top: 0,
                left: 0,
                '-webkit-transform': 'translateZ(0.1px)',
                overflow: 'hidden'
            })
            .appendTo(tab);

        NextendTween.set(front, {
            backfaceVisibility: 'hidden',
            transformStyle: 'preserve-3d'
        });


        var back = $('<div class="n2-ff-3d"></div>')
            .append(this.clonedNext())
            .appendTo(tab)
            .css({
                width: w2,
                height: this.h,
                position: 'absolute',
                top: 0,
                left: 0,
                overflow: 'hidden'
            });

        NextendTween.set(back, {
            backfaceVisibility: 'hidden',
            transformStyle: 'preserve-3d',
            rotationY: 180,
            rotationZ: 0
        });

        var current = $('<div></div>')
                .append(this.clonedCurrent().clone().css('left', (animation.direction == 'left' ? 0 : -w2))).css({
                    position: 'absolute',
                    top: 0,
                    left: animation.direction == 'left' ? '0' : w2,
                    width: w2,
                    height: this.h,
                    zIndex: 100,
                    overflow: 'hidden'
                }),
            overlay = $('<div class="overlay"></div>').css({
                position: 'absolute',
                top: 0,
                left: animation.direction == 'left' ? w2 : 0,
                width: w2,
                height: this.h,
                background: '#000',
                opacity: 1,
                overflow: 'hidden'
            }),

            container = $('<div></div>').css({
                width: this.w,
                height: this.h,
                position: 'absolute',
                top: 0,
                left: 0
            }).append(tab).append(current).append(overlay);


        NextendTween.set(container, {
            perspective: animation.perspective,
            perspectiveOrigin: '50% 50%'
        });

        this.placeNextImage();
        this.clonedImages.nextImage.css({
            overflow: 'hidden',
            width: '100%',
            height: '100%'
        });

        this.containerElement.append(container);

        this.preSetup();

        this.timeline.to(tab.get(0), animation.duration * this.durationMultiplier, {
            rotationY: (animation.direction == 'left' ? -180 : 180)
        }, 0);

        this.timeline.to(overlay.get(0), animation.duration * this.durationMultiplier, {
            opacity: 0
        }, 0);
    };

    NextendSmartSliderBackgroundAnimationTurn.prototype = Object.create(NextendSmartSliderBackgroundAnimationFluxAbstract.prototype);
    NextendSmartSliderBackgroundAnimationTurn.prototype.constructor = NextendSmartSliderBackgroundAnimationTurn;


    NextendSmartSliderBackgroundAnimationTurn.prototype.getExtraDelay = function () {
        return 0;
    };

    scope.NextendSmartSliderBackgroundAnimationTurn = NextendSmartSliderBackgroundAnimationTurn;


    function NextendSmartSliderBackgroundAnimationExplode() {
        NextendSmartSliderBackgroundAnimationTiled.prototype.constructor.apply(this, arguments);
    };

    NextendSmartSliderBackgroundAnimationExplode.prototype = Object.create(NextendSmartSliderBackgroundAnimationTiled.prototype);
    NextendSmartSliderBackgroundAnimationExplode.prototype.constructor = NextendSmartSliderBackgroundAnimationExplode;


    NextendSmartSliderBackgroundAnimationExplode.prototype.setup = function () {

        var animation = $.extend(true, {
            columns: 1,
            rows: 1,
            reverse: false,
            tiles: {
                delay: 0, // Delay between the starting of the tiles sequence. Ex.: #1 batch start: 0s, #2: .2s, #3: .4s
                sequence: 'Parallel' // Parallel, Random, ForwardCol, BackwardCol, ForwardRow, BackwardRow, ForwardDiagonal, BackwardDiagonal
            },
            main: {
                duration: 0.5,
                zIndex: 2, // z-index of the current image. Change it to 2 to show it over the second image.
                current: { // Animation of the current tile
                    ease: 'easeInOutCubic'
                }
            }
        }, this.animationProperties);

        this.placeNextImage();
        this.clonedImages.nextImage.css({
            overflow: 'hidden',
            width: '100%',
            height: '100%'
        });

        NextendSmartSliderBackgroundAnimationTiled.prototype.setup.call(this, animation);
    };

    NextendSmartSliderBackgroundAnimationExplode.prototype.renderTile = function (tile, w, h, animation, totalLeft, totalTop) {

        var current = $('<div></div>')
            .css({
                position: 'absolute',
                left: 0,
                top: 0,
                width: w,
                height: h,
                overflow: 'hidden',
                zIndex: animation.main.zIndex
            })
            .append(this.clonedCurrent().clone().css({
                position: 'absolute',
                top: -totalTop + 'px',
                left: -totalLeft + 'px'
            }))
            .appendTo(tile);

        NextendTween.set(tile.get(0), {
            transformPerspective: 1000,
            transformStyle: "preserve-3d"
        });

        return {
            current: current,
            tile: tile
        }
    };

    NextendSmartSliderBackgroundAnimationExplode.prototype.transform = function (animation, animatable, total) {

        var current = $.extend(true, {}, animation.main.current);

        current.rotationX = (Math.random() * 3 - 1) * 90;
        current.rotationY = (Math.random() * 3 - 1) * 90;
        current.rotationZ = (Math.random() * 3 - 1) * 90;
        this.timeline.to(animatable.tile, animation.main.duration * this.durationMultiplier, current, total);
    };

    scope.NextendSmartSliderBackgroundAnimationExplode = NextendSmartSliderBackgroundAnimationExplode;


    function NextendSmartSliderBackgroundAnimationExplodeReversed() {
        NextendSmartSliderBackgroundAnimationTiled.prototype.constructor.apply(this, arguments);
    };

    NextendSmartSliderBackgroundAnimationExplodeReversed.prototype = Object.create(NextendSmartSliderBackgroundAnimationTiled.prototype);
    NextendSmartSliderBackgroundAnimationExplodeReversed.prototype.constructor = NextendSmartSliderBackgroundAnimationExplodeReversed;


    NextendSmartSliderBackgroundAnimationExplodeReversed.prototype.setup = function () {

        var animation = $.extend(true, {
            columns: 1,
            rows: 1,
            reverse: false,
            tiles: {
                delay: 0, // Delay between the starting of the tiles sequence. Ex.: #1 batch start: 0s, #2: .2s, #3: .4s
                sequence: 'Parallel' // Parallel, Random, ForwardCol, BackwardCol, ForwardRow, BackwardRow, ForwardDiagonal, BackwardDiagonal
            },
            main: {
                duration: 0.5,
                zIndex: 2, // z-index of the current image. Change it to 2 to show it over the second image.
                current: { // Animation of the current tile
                    ease: 'easeInOutCubic'
                }
            }
        }, this.animationProperties);

        this.placeCurrentImage();
        this.clonedImages.currentImage.css({
            overflow: 'hidden',
            width: '100%',
            height: '100%'
        });

        NextendSmartSliderBackgroundAnimationTiled.prototype.setup.call(this, animation);
    };

    NextendSmartSliderBackgroundAnimationExplodeReversed.prototype.renderTile = function (tile, w, h, animation, totalLeft, totalTop) {

        var next = $('<div></div>')
            .css({
                position: 'absolute',
                left: 0,
                top: 0,
                width: w,
                height: h,
                overflow: 'hidden',
                zIndex: animation.main.zIndex
            })
            .append(this.clonedNext().clone().css({
                position: 'absolute',
                top: -totalTop + 'px',
                left: -totalLeft + 'px'
            }))
            .appendTo(tile);

        NextendTween.set(tile.get(0), {
            transformPerspective: 1000,
            transformStyle: "preserve-3d"
        });

        return {
            next: next,
            tile: tile
        }
    };

    NextendSmartSliderBackgroundAnimationExplodeReversed.prototype.transform = function (animation, animatable, total) {

        var current = $.extend(true, {}, animation.main.current);

        current.rotationX = (Math.random() * 3 - 1) * 90;
        current.rotationY = (Math.random() * 3 - 1) * 90;
        current.rotationZ = (Math.random() * 3 - 1) * 30;
        this.timeline.from(animatable.tile, animation.main.duration * this.durationMultiplier, current, total);
    };

    scope.NextendSmartSliderBackgroundAnimationExplodeReversed = NextendSmartSliderBackgroundAnimationExplodeReversed;


    function NextendSmartSliderBackgroundAnimationSlixes() {
        NextendSmartSliderBackgroundAnimationTiled.prototype.constructor.apply(this, arguments);
    };

    NextendSmartSliderBackgroundAnimationSlixes.prototype = Object.create(NextendSmartSliderBackgroundAnimationTiled.prototype);
    NextendSmartSliderBackgroundAnimationSlixes.prototype.constructor = NextendSmartSliderBackgroundAnimationSlixes;


    NextendSmartSliderBackgroundAnimationSlixes.prototype.setup = function () {

        var animation = $.extend(true, {
            columns: 2,
            rows: 2,
            main: {
                duration: 2,
                zIndex: 2 // z-index of the current image. Change it to 2 to show it over the second image.
            }
        }, this.animationProperties);

        this.placeNextImage();
        this.clonedImages.nextImage.css({
            overflow: 'hidden',
            width: '100%',
            height: '100%'
        });

        NextendSmartSliderBackgroundAnimationTiled.prototype.setup.call(this, animation);
    };

    NextendSmartSliderBackgroundAnimationSlixes.prototype.renderTile = function (tile, w, h, animation, totalLeft, totalTop) {
        this.container.css('overflow', 'hidden');

        var current = $('<div></div>')
            .css({
                position: 'absolute',
                left: 0,
                top: 0,
                width: w,
                height: h,
                overflow: 'hidden',
                zIndex: animation.main.zIndex
            })
            .append(this.clonedCurrent().clone().css({
                position: 'absolute',
                top: -totalTop + 'px',
                left: -totalLeft + 'px'
            }))
            .appendTo(tile);

        NextendTween.set(tile.get(0), {
            transformPerspective: 1000,
            transformStyle: "preserve-3d"
        });

        return {
            current: current,
            tile: tile
        }
    };

    NextendSmartSliderBackgroundAnimationSlixes.prototype.animate = function (animation, animatables, animatablesMulti) {

        this.timeline.to(animatablesMulti[0][0].tile, animation.main.duration * this.durationMultiplier, {
            left: '-50%',
            ease: 'easeInOutCubic'
        }, 0);
        this.timeline.to(animatablesMulti[0][1].tile, animation.main.duration * this.durationMultiplier, {
            left: '-50%',
            ease: 'easeInOutCubic'
        }, 0.3);

        this.timeline.to(animatablesMulti[1][0].tile, animation.main.duration * this.durationMultiplier, {
            left: '100%',
            ease: 'easeInOutCubic'
        }, 0.15);
        this.timeline.to(animatablesMulti[1][1].tile, animation.main.duration * this.durationMultiplier, {
            left: '100%',
            ease: 'easeInOutCubic'
        }, 0.45);

        $('<div />').css({
            position: 'absolute',
            left: 0,
            top: 0,
            width: '100%',
            height: '100%',
            overflow: 'hidden'
        }).prependTo(this.clonedImages.nextImage.parent()).append(this.clonedImages.nextImage);

        this.timeline.fromTo(this.clonedImages.nextImage, animation.main.duration * this.durationMultiplier, {
            scale: 1.3
        }, {
            scale: 1
        }, 0.45);
    };
    scope.NextendSmartSliderBackgroundAnimationSlixes = NextendSmartSliderBackgroundAnimationSlixes;

})
(n2, window);