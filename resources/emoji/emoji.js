(function ($) {
    "use strict";

    emojify.setConfig({
        ignore_emoticons: true,
    });

    $(function () {
        $('img.emoji-str').each(function () {
            var $img = $(this);
            var match = ($img.attr('data-emoji') + '').match(/^:([a-z0-9+_-]+):$/);
            if (match) {
                $img.addClass('emoji-' + match[1]);
            }
        });
    });
})(jQuery);
