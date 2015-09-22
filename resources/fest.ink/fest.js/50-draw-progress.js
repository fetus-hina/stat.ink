// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var NaN = Number.NaN;
    var $event = $('#event');
    var colors = null;
    var updateColor = function () {
        $('.total-progressbar').each(function () {
            var $this = $(this);
            var color = colors[$this.attr('data-team') === 'alpha' ? 0 : 1];
            $this.css(
                'background-color',
                (window.fest.conf.useInkColor.get() && color !== null)
                    ? ('#' + color)
                    : ''
            );
        });
    };

    $event.on('receiveUpdateData', function (ev, data_) {
        var json = data_.json;
        var summary = data_.summary;
        colors = [
            json.teams.alpha.ink,
            json.teams.bravo.ink,
        ];
        $('.total-progressbar').each(function () {
            var $this = $(this);
            var teamId = ($this.attr('data-team') + "").substr(0, 1);
            var rate = summary[teamId] ? summary[teamId] : NaN;
            $this.width(
                (rate === undefined || isNaN(rate))
                    ? '0%'
                    : ((rate * 100) + "%")
            );
        });
        updateColor();
    });
    $event.on('updateConfigGraphInk', function () {
        if (!colors) {
            $event.trigger('requestUpdateData');
            return;
        }
        updateColor();
    });
});
