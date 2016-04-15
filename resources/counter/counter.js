/*! Copyright (C) 2016 AIZAWA Hina | MIT License */ 
(function ($) {
    "use strict";
    var interval_short = 2 * 60 * 1000;
    var interval_long = 10 * 60 * 1000;
    var $counters = $('.dseg-counter');
    var createBackgroud = function () {
        $counters.each(function () {
            var $this = $(this);
            $this.prepend(
                $('<span>').addClass('dseg-counter-bg').text(
                    (function (len) {
                        var ret = '';
                        for (var i = 0; i < len; ++i) {
                            ret += '~';
                        }
                        return ret;
                    })($this.text().length)
                )
            );
        });
    };
    var update = function () {
        $.ajax('/api/internal/counter', {
            cache: false,
            dataType: 'json',
            method: 'GET',
            error: function () {
                window.setTimeout(update, interval_long);
            },
            success: function (json) {
                window.setTimeout(update, interval_short);
                $counters.each(function() {
                    var $this = $(this);
                    $this.empty().text(
                        ~~(json[$this.attr('data-type')]) + ""
                    );
                });
                createBackgroud();
            },
        });
    };

    $(function() {
        createBackgroud();
        window.setTimeout(update, interval_short);
    });
})(jQuery);
