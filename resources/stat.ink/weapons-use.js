/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function($, undefined) {
    "use strict";

    var stack = false;
    function update() {
        var formatDate = function(date) {
            var zero = function (n) {
                n = n + "";
                return n.length == 1 ? "0" + n : n;
            };
            return date.getUTCFullYear() + "-" + zero(date.getUTCMonth() + 1) + "-" + zero(date.getUTCDate());
        };

        var date2unixTime = function(d) {
            return new Date(d + "T00:00:00Z").getTime();
        };

        var $graphs = $("#graph-trends");
        $graphs.height($graphs.width() * 9 / 16);
        $graphs.each(function() {
            var $graph = $(this);
            var $legends = $graph.attr('data-legends')
                    ? $('#' + $graph.attr('data-legends'))
                    : null;
            var legendColumns = (function () {
                var width = $(window).width();
                if (!$legends) {
                    return 1;
                }
                if (width < 768) { // xs
                    return 1;
                } else if (width < 992) { // sm
                    return 2;
                } else if (width < 1200) { // md
                    return 4;
                } else { // lg
                    return 5;
                }
            })();
            var json = JSON.parse($("#" + $graph.attr('data-refs')).text());
            var data = [];
            $.each(json, function () {
                data.push({
                    label: this.legend,
                    data: this.data.map(function (row) {
                        return [
                            date2unixTime(row[0]),
                            row[1],
                        ];
                    }),
                });
            });
            $.plot($graph, data, {
                xaxis: {
                    mode: "time",
                    minTickSize: [ 7, "day" ],
                    tickFormatter: function(v) {
                        return formatDate(new Date(v));
                    }
                },
                yaxis: {
                    min: 0,
                    tickFormatter: function(v) {
                        return v.toFixed(1) + "%";
                    }
                },
                series: {
                    stack: stack,
                    points: {
                        show: !stack
                    },
                    lines: {
                        show: true,
                        fill: stack,
                        steps: false
                    }
                },
                legend: {
                    sorted: stack ? "reverse" : false,
                    position: "nw",
                    container: $legends,
                    noColumns: legendColumns,
                }
            });

            if ($legends) {
                window.setTimeout(function () {
                    var $labels = $('td.legendLabel', $legends);
                    $labels.width(
                        Math.max.apply(null, $labels.map(function () {
                            return $(this).width('').width();
                        })) + 12
                    );
                }, 1);
            }
        });
    }
    var timerId = null;
    $(window).resize(function() {
        if (timerId !== null) {
            window.clearTimeout(timerId);
        }
        timerId = window.setTimeout(function() {
            update();
        }, 33);
    }).resize();
    $("#stack-trends").click(function() {
        stack = !!$(this).prop("checked");
        $(window).resize();
    });
})(jQuery);
