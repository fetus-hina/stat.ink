/*! Copyright (C) 2015 AIZAWA Hina | MIT License */
(function ($) {
    var options = {
        series: {
            icons: {
                show: false,
                marginX: 5,
                marginY: 5,
            }
        }
    };

    $.plot.plugins.push({
        init: function (plot) {
            plot.hooks.processRawData.push(function (plot, series, data, datapoints) {
                if (!series.icons.show) {
                    return;
                }

                datapoints.format = [
                    // src
                    { required: true },
                    // x
                    { x: true, number: true, required: true },
                    // w, h
                    { required: true },
                    { required: true },
                ];
            });

            plot.hooks.draw.push(function (plot, canvasctx) {
                var $target = $(plot.getCanvas()).parent();
                var lastPosRight = -2147483648;
                var lastPosTop;
                var plotOffset = plot.getPlotOffset();
                $.each(
                    plot.getData().filter(
                        function (o) {
                            return !!o.icons.show;
                        }
                    ),
                    function () {
                        function convertToImageDimension(v, item) {
                            switch (typeof v) {
                                case 'number':
                                    return v;
                                case 'function':
                                    return v(plot, canvasctx, item, $target.width(), $target.height());
                                default:
                                    return parseFloat(v);
                            }
                        }

                        var series = this;
                        for (var i = 0 ; i < series.data.length; ++i) {
                            var item = series.data[i];
                            var itemWidth = convertToImageDimension(item[2]);
                            var itemHeight = convertToImageDimension(item[3]);

                            var pos = plot.p2c({x:item[1], y:0});
                            var posLeft = pos.left - itemWidth / 2;
                            var posTop = (function() {
                                    if (posLeft - lastPosRight < series.icons.marginX) {
                                        return lastPosTop - (itemHeight + series.icons.marginY);
                                    } else {
                                        return ($target.height() - plotOffset.bottom) - (itemHeight + series.icons.marginY);
                                    }
                            })();
                            lastPosRight = posLeft + itemWidth;
                            lastPosTop = posTop;
                            var $img = (function() {
                                switch(typeof item[0]) {
                                    case 'function':
                                        return (item[0])(plot, canvasctx, item);
                                    default:
                                        return $('<img>', {src: item[0]});
                                }
                            })();
                            $img.width(itemWidth)
                                .height(itemHeight)
                                .css({
                                    'position': 'absolute',
                                    'left': (plotOffset.left + posLeft) + 'px',
                                    'top': (plotOffset.top + posTop) + 'px',
                                    'z-index': 1000
                                });
                            $target.append($img);
                        }
                    }
                );
            });
        },
        options: options,
        name: 'icon',
        version: '0.1.0-dev'
    });
})(jQuery);
