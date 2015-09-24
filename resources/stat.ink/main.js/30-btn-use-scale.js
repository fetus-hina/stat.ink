// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $button = $('#btn-scale');
    var $event = $('#event');
    var onChange = function () {
        var state = window.fest.conf.useGraphScale.get();
        $button.removeClass('btn-primary')
            .removeClass('btn-default')
            .addClass(state ? 'btn-primary' : 'btn-default');
    };
    onChange();

    $button.click(function () {
        var currentEnable = $(this).hasClass('btn-primary');
        window.fest.conf.useGraphScale.set(!currentEnable);
    });

    $event.on('updateConfigGraphScale', onChange);
});
