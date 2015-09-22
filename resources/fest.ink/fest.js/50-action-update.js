// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var festId = window.fest.getFestId();
    var $event = $('#event');
    var isUpdating = false;
    var previous = null;
    var makeSummary = function (json) { // {{{
        var NaN = Number.NaN;
        var totalAlpha = 0;
        var totalBravo = 0;
        var totalAlphaRaw = 0;
        var totalBravoRaw = 0;
        var scale = window.fest.getTimeBasedScaler();
        for (var i = 0; i < json.wins.length; ++i) {
            totalAlpha += scale(json.wins[i].alpha, json.wins[i].at);
            totalBravo += scale(json.wins[i].bravo, json.wins[i].at);
            totalAlphaRaw += json.wins[i].alpha;
            totalBravoRaw += json.wins[i].bravo;
        }
        var totalCount = totalAlpha + totalBravo;
        return {
            'a':        (totalCount > 0) ? totalAlpha / totalCount : NaN,
            'b':        (totalCount > 0) ? totalBravo / totalCount : NaN,
            'aSum':     (totalCount > 0) ? totalAlpha : NaN,
            'bSum':     (totalCount > 0) ? totalBravo : NaN,
            'aSumRaw':  (totalCount > 0) ? totalAlphaRaw: NaN,
            'bSumRaw':  (totalCount > 0) ? totalBravoRaw: NaN
        };
    }; // }}}
    $event.on('requestUpdateData', function () {
        if (isUpdating) {
            return;
        }
        $event.trigger('startUpdateData');
    });
    $event.on('requestRetriggerUpdateEvent', function () {
        if (!previous) {
            $event.trigger('requestUpdateData');
            return;
        }
        if (isUpdating) {
            return;
        }
        $event.trigger('beginUpdateData');
        $event.trigger('receiveUpdateData', {
            date: previous.date,
            json: previous.json,
            summary: makeSummary(previous.json),
        });
        $event.trigger('afterUpdateData');
    });
    $event.on('startUpdateData', function () {
        $event.trigger('beginUpdateData');
        var date = new Date();
        $.getJSON(
            '/' + encodeURIComponent(festId) + '.json',
            { _: Math.floor(date / 1000) },
            function (json) {
                $event.trigger('receiveUpdateData', {
                    date: date,
                    json: json,
                    summary: makeSummary(json),
                });
                $event.trigger('afterUpdateData');
                previous = {
                    date: date,
                    json: json,
                };
                isUpdating = false;
            }
        );
    });
});
