setTimeout(function () {
// -------------------------- check if Episode have intro -------------------------
    if($('.chapter').hasClass('intro')){
        $('.adventure').addClass('intro')
    }
// -------------------------- parallax effect for Episode background -----------------------
    $(window).scroll(function () {
        var scrollTop = $(window).scrollTop();
        var parallaxElement = $('.adventure');
        parallaxElement.css('background-position-y', scrollTop / 4 + 'px');
    });
// ---------------------------- color separated list ----------------------------
    var divs = document.querySelectorAll('.chapter, .img');
    var classColors = {};
    var colorIndex = 0;
    for (var i = 0; i < divs.length; i++) {
        var div = divs[i];
        var className = div.classList[1];
        if (!classColors[className]) {
            classColors[className] = 'color-' + colorIndex;
            colorIndex++;
        }
        div.classList.add(classColors[className]);
    }
// ======  curves =================================
    const svgContainer = d3.select("#svgContainer");
    const episodes = d3.selectAll(".adventure");

    function connectDivs() {
        const coordinates = [];

        episodes.each(function () {
            const episodeContainer = d3.select(this);
            const chapters = episodeContainer.selectAll(".chapter, .control-top, .control-bottom");

            const episodeRect = episodeContainer.node().getBoundingClientRect();
            const episodeWidth = 400;
            const episodeHeight = 2000;

            const chapterCoordinates = chapters.nodes().map(node => {
                const rect = node.getBoundingClientRect();
                // Scale the coordinates to fit the episode container
                const scaledX = (rect.left + rect.width - episodeRect.left) * 100 / episodeWidth;
                const scaledY = (rect.top + rect.height - episodeRect.top) * 100 / episodeHeight;
                return {
                    x: scaledX,
                    y: scaledY
                };
            });

            coordinates.push(...chapterCoordinates);
        });

        const lineGenerator = d3.line()
            .curve(d3.curveNatural)
            .x(d => d.x)
            .y(d => d.y);

        const pathData = lineGenerator(coordinates);

        svgContainer.selectAll("path").remove();

        svgContainer
            .append("path")
            .attr("d", pathData)
            .attr("stroke-linecap", "round")
            .attr("fill", "none")
            .attr("stroke", "#fff")
            .attr("stroke-width", "1rem");
    }

    window.addEventListener("resize", connectDivs);
    connectDivs();

// ==================== move elements across Episode
    function mouseMove(selectors) {
        $(selectors).each(function () {
            var point = $(this);
            var container = point.closest(".adventure");
            var containerRect = container[0].getBoundingClientRect();
            var containerWidth = containerRect.width;
            var containerHeight = container[0].clientHeight;

            var offsetXPercent = point.data("offsetXPercent");
            var offsetYPercent = point.data("offsetYPercent");

            if (offsetXPercent !== undefined && offsetYPercent !== undefined) {
                var offsetX = (offsetXPercent / 100) * containerWidth;
                var offsetY = (offsetYPercent / 100) * containerHeight;

                point.css({
                    left: offsetX + "px",
                    top: offsetY + "px"
                });
            }
            if (point.hasClass("control-top") || point.hasClass("control-bottom")) {
                point.mousedown(function (event) {
                    event.preventDefault();
                    var initialX = event.clientX;

                    var pointOffset = point.offset();
                    var pointOffsetX = pointOffset.left - containerRect.left;

                    $(document).mousemove(function (event) {
                        var currentX = event.clientX;

                        var offsetX = currentX - initialX + pointOffsetX;

                        offsetX = Math.max(0, Math.min(offsetX, containerWidth - point.outerWidth()));

                        var offsetXPercent = (offsetX / containerWidth) * 100;

                        point.css({
                            left: offsetXPercent + "%"
                        });

                        point.data("offsetXPercent", offsetXPercent);
                    });

                    $(document).mouseup(function () {
                        $(document).off("mousemove");
                        connectDivs();
                    });
                });
            } else {
                point.mousedown(function (event) {
                    event.preventDefault();
                    var initialX = event.clientX;
                    var initialY = event.clientY;

                    var pointOffset = point.offset();
                    var pointOffsetX = pointOffset.left - containerRect.left;
                    var pointOffsetY = pointOffset.top - containerRect.top;

                    $(document).mousemove(function (event) {
                        var currentX = event.clientX;
                        var currentY = event.clientY;

                        var offsetX = currentX - initialX + pointOffsetX;
                        var offsetY = currentY - initialY + pointOffsetY;

                        offsetX = Math.max(0, Math.min(offsetX, containerWidth - point.outerWidth()));
                        offsetY = Math.max(0, Math.min(offsetY, containerHeight - point.outerHeight()));

                        var offsetXPercent = (offsetX / containerWidth) * 100;
                        var offsetYPercent = (offsetY / containerHeight) * 100;

                        point.css({
                            left: offsetXPercent + "%",
                            top: offsetYPercent + "%"
                        });

                        point.data("offsetXPercent", offsetXPercent);
                        point.data("offsetYPercent", offsetYPercent);
                    });

                    $(document).mouseup(function () {
                        $(document).off("mousemove");
                        connectDivs();
                    });
                });
            }
        });
        return false;
    }

    mouseMove('.chapter, .img, .control-top, .control-bottom');


    $(".img").each(function () {
        var img = $(this);
        var container = img.closest(".adventure");
        var containerRect = container[0].getBoundingClientRect();
        var containerWidth = containerRect.width;
        var containerHeight = containerRect.height;

        var rotation = img.data("rotation");

        if (rotation !== undefined) {
            img.css({
                transform: "rotate(" + rotation + "deg)"
            });
        }

        var initialRotation = rotation || 0;

        img.on("mouseenter", function () {
            var isScrolling = false;

            img.on("wheel", function (event) {
                event.stopPropagation();

                if (!isScrolling) {
                    event.preventDefault();
                }

                var rotationSpeed = 2;

                var delta = event.originalEvent.deltaY || event.originalEvent.wheelDelta;
                var rotationChange = delta > 0 ? rotationSpeed : -rotationSpeed;
                var rotation = initialRotation + rotationChange;

                img.css({
                    transform: "rotate(" + rotation + "deg)"
                });

                img.data("rotation", rotation);
                initialRotation = rotation;

                isScrolling = true;

                setTimeout(function () {
                    isScrolling = false;
                }, 200);
            });
        }).on("mouseleave", function () {
            img.off("wheel");
        });
    });
}, 100)