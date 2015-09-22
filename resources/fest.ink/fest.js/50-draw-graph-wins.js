// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $event = $('#event');
    var previous = null;
    var draw = function () {
        var options = window.fest.getGraphOptions(previous.term, previous.teams);
        options.series.stack = false;

        $('.rate-graph.rate-graph-win-count').each(function () {
            var $area = $(this);
            $area.empty();
            $.plot($area, previous.data, options);
        });
    };

    $event.on('receiveUpdateData', function (ev, data_) {
        // data.date, data.json, data.summary
        var json = data_.json;
        var $targets = $('.rate-graph.rate-graph-win-count');
        if ($targets.length < 1) {
            return;
        }

        var wins = json.wins.slice(0);
        wins.sort(function (a, b) {
            return a.at - b.at;
        });

        var scale = window.fest.getTimeBasedScaler();
        var alphaTotal = 0;
        var bravoTotal = 0;
        var alpha = [];
        var bravo = [];
        for (var i = 0; i < wins.length; ++i) {
            var tmp = wins[i];
            alphaTotal += scale(tmp.alpha, tmp.at);
            bravoTotal += scale(tmp.bravo, tmp.at);
            if (alphaTotal + bravoTotal > 0) {
                alpha.push([tmp.at * 1000, alphaTotal]);
                bravo.push([tmp.at * 1000, bravoTotal]);
            }
        }
        var maxTotal = Math.max(alphaTotal, bravoTotal);
        if (maxTotal < 1) {
            maxTotal = 1;
        }

        var toPercentage = function (val) {
            val[1] = val[1] * 100 / maxTotal;
            return val;
        };

        previous = {
            data: [alpha.map(toPercentage), bravo.map(toPercentage)],
            term: json.term,
            teams: json.teams,
        };
        draw();
    });

    $event.on('updateConfigGraphInk', function () {
        if (!previous) {
            $event.trigger('requestUpdateData');
            return;
        }
        draw();
    });
});
