/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function ($) {
    "use strict";
    $('.slack-toggle-enable').change(function () {
        var $this = $(this);
        $.ajax(
            '/user/slack-suspend',
            {
                type: 'POST',
                data: {
                    id: $this.attr('data-id'),
                    suspend: $this.prop('checked') ? 'no' : 'yes',
                },
                complete: function () {
                    $this.prop('disabled', false);
                }
            }
        );
        $this.prop('disabled', true);
    }).prop('disabled', false);

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
