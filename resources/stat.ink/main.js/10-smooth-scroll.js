// Copyright (C) 2015 AIZAWA Hina / MIT License
$(document).ready(function () {
    $('a[href^="#"]').not('[data-toggle="tab"]').smoothScroll({
        offset: -60,
    });
});
