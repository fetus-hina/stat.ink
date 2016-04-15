/*! Copyright (C) 2016 AIZAWA Hina | MIT License */ 
(function ($) {
    "use strict";
    $('.dseg-counter').each(function () {
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
})(jQuery);
