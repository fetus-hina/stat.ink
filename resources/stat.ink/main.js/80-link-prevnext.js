// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    var $next = $('link[rel="next"]');
    var $prev = $('link[rel="prev"]');
    $(window).keydown(function(ev) {
        // do nothing when Swipebox is opened
        if ($.swipebox && $.swipebox.isOpen) {
            return false;
        }

        // 37: left
        // 39: right
        switch (ev.keyCode) {
            case 37:
                if ($prev.length) {
                    window.location.href = $prev.attr('href');
                    return false;
                }
                break;

            case 39:
                if ($next.length) {
                    window.location.href = $next.attr('href');
                    return false;
                }
                break;
        }
    });
});
