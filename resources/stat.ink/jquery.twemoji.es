/*! Copyright (C) 2015-2022 AIZAWA Hina | MIT License */

($ => {
  const twemoji = window.twemoji;

  $.fn.twemoji = function () {
    this.each(function () {
      twemoji.parse(this, {
        base: '/static-assets/twemoji/',
        className: 'emoji twemoji',
        ext: '.svg',
        folder: 'svg'
      });
    });

    return this;
  };
})(jQuery);
