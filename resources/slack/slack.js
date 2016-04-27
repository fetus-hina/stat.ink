/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function ($) {
    "use strict";
    $('.slack-test').click(function () {
        var $this = $(this);
        $.ajax(
            '/user/slack-test',
            {
                data: {
                    id: $this.attr('data-id'),
                },
                type: 'POST'
            }
        );
    }).prop('disabled', false);
})(jQuery);
