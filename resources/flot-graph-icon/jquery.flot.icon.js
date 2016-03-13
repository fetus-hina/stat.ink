/*! Copyright (C) 2015 AIZAWA Hina | MIT License */
(function ($) {
    var options = {
        series: {
            icons: {
                show: false,
                marginX: 5,
                marginY: 5,
                tooltip: false,
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
                var iconSlots = [];
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
                            var userData = item.length >= 5 ? item[4] : undefined;
                            var decorator = item.length >= 6 ? item[5] : undefined;

                            var pos = plot.p2c({x:item[1], y:0});
                            var posLeft = plotOffset.left + (pos.left - itemWidth / 2);
                            var posRight = posLeft + itemWidth + series.icons.marginX;
                            var posTop = (function() {
                                for (var slot = 0; ; ++slot) {
                                    while (iconSlots.length <= slot) {
                                        iconSlots.push(-2147483648);
                                    }
                                    if (iconSlots[slot] <= posLeft) {
                                        iconSlots[slot] = posRight;
                                        var posFromBottom = (itemHeight + series.icons.marginY) * (slot + 1) - (series.icons.marginY / 2);
                                        return ($target.height() - plotOffset.bottom) - posFromBottom;
                                    }
                                }
                            })();
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
                                    'left': posLeft + 'px',
                                    'top': posTop + 'px',
                                    'z-index': 1000
                                });
                            if (series.icons.tooltip !== false) {
                                var x = item[1];
                                if (typeof series.icons.tooltip === 'function') {
                                    (function () {
                                        var v = series.icons.tooltip(x, $img, userData);
                                        if (v !== undefined) {
                                            $img.attr('title', v);
                                        }
                                    })();
                                } else {
                                    $img.attr('title', x);
                                }
                            }
                            if (typeof decorator === 'function') {
                                decorator.call(item, $img);
                            }
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
