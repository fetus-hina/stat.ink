// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $event = $('#event');
    var previous = null;
    var draw = function () {
        $('.rate-graph.rate-graph-whole').each(function () {
            var $area = $(this);
            $area.empty();
            $.plot($area, previous.data, window.fest.getGraphOptions(previous.term, previous.teams));
        });
    };

    $event.on('receiveUpdateData', function (ev, data_) {
        // data.date, data.json, data.summary
        var json = data_.json;
        var $targets = $('.rate-graph.rate-graph-whole');
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
            var tmpA = scale(tmp.alpha, tmp.at);
            var tmpB = scale(tmp.bravo, tmp.at);
            alphaTotal += tmpA;
            bravoTotal += tmpB;
            if (alphaTotal + bravoTotal > 0) {
                alpha.push([
                    tmp.at * 1000,
                    alphaTotal * 100 / (alphaTotal + bravoTotal)
                ]);
                bravo.push([
                    tmp.at * 1000,
                    bravoTotal * 100 / (alphaTotal + bravoTotal)
                ]);
            }
        }
        previous = {
            data: [alpha, bravo],
            term: json.term,
            teams: json.teams,
        };
        draw();
    });

    $event.on('updateConfigGraphType updateConfigGraphInk', function () {
        if (!previous) {
            t$event.trigger('requestUpdateData');
            return;
        }
        draw();
    });
});
