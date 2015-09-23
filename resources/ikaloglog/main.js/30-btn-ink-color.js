// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $button = $('#btn-ink-color');
    var $event = $('#event');
    var onChange = function () {
        var state = window.fest.conf.useInkColor.get();
        $button.removeClass('btn-primary')
            .removeClass('btn-default')
            .addClass(state ? 'btn-primary' : 'btn-default');
    };
    onChange();

    $button.click(function () {
        var currentEnable = $(this).hasClass('btn-primary');
        window.fest.conf.useInkColor.set(!currentEnable);
    });

    $event.on('updateConfigGraphInk', onChange);
});
