// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $button = $('#btn-update');
    var $event = $('#event');

    $button.click(function () {
        $event.trigger('requestUpdateData');
    });

    $event.on('beginUpdateData', function () {
        $button.attr('disabled', 'disabled');
    });

    $event.on('afterUpdateData', function () {
        $button.removeAttr('disabled');
    });
});
