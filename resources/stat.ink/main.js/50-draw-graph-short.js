// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $event = $('#event');
    var previous = null;
    var draw = function () {
        $('.rate-graph.rate-graph-short').each(function () {
            var $area = $(this);
            $area.empty();
            $.plot($area, previous.data, window.fest.getGraphOptions(previous.term, previous.teams));
        });
    };

    $event.on('receiveUpdateData', function (ev, data_) {
        var json = data_.json;
        var $targets = $('.rate-graph.rate-graph-short');
        if ($targets.length < 1) {
            return;
        }

        var scale = window.fest.getTimeBasedScaler();
        var alpha = [];
        var bravo = [];
        for (var i = 0; i < json.wins.length; ++i) {
            var tmp = json.wins[i];
            var tmpA = scale(tmp.alpha, tmp.at);
            var tmpB = scale(tmp.bravo, tmp.at);
            var sum = tmpA + tmpB;
            if (sum > 0) {
                alpha.push([tmp.at * 1000, tmpA * 100 / sum]);
                bravo.push([tmp.at * 1000, tmpB * 100 / sum]);
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
            $event.trigger('requestUpdateData');
            return;
        }
        draw();
    });
});
