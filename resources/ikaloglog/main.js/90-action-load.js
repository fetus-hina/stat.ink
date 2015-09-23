// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    window.setTimeout(function () {
        $('#event').trigger('requestUpdateData');
    }, 1);
});
