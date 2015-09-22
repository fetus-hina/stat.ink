// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $event = $('#event');
    $event.on('updateConfigGraphScale', function () {
        $event.trigger('requestRetriggerUpdateEvent');
    });
});
