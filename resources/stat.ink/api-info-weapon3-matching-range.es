'use strict';

(function ($) {
  const MAX_RANGE = 33.0;
  const MULTIPLIER = 10.0;
  const HSL = {
    hue: 214,
    saturation: 0.57,
    lightness: 0.47
  };

  $.fn.matchingRange = function () {
    this.each(function () {
      const $this = $(this);
      const bgColor = $.Color(HSL)
        .alpha(parseFloat($this.attr('data-sort-value')) / (MULTIPLIER * MAX_RANGE))
        .blend($.Color('#ffffff'));

      const y = Math.round(bgColor.red() * 0.299 + bgColor.green() * 0.587 + bgColor.blue() * 0.114);
      $this.css({
        'background-color': bgColor.toRgbaString(),
        color: y > 153 ? '#000' : '#fff'
      });
    });

    return this;
  };
})(window.jQuery);
