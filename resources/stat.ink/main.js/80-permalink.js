// Copyright (C) AIZAWA Hina | MIT License
(function(window) {
    "use strict";
    var $ = window.jQuery;
    $.fn.permaLink = function () {
        var $this = this;
        var href = (function() {
            var $link = $('link[rel="canonical"]');
            if ($link.length > 0) {
                return $link.attr('href');
            }
            var $twitter = $('meta[name="twitter:url"]');
            if ($twitter.length > 0) {
                return $twitter.attr('content');
            }
            return window.location.href;
        })();
        $this.attr('data-clipboard-text', href);
        var clipboard = new Clipboard($this.get(0));
        return $this;
    };
})(window);
