/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */
($ => {
  const calcBgColor = ratio => {
    const ratio2 = (() => {
      if (ratio >= 4.0) {
        return 1.0;
      } else if (ratio <= 0.25) {
        return 0.0;
      } else if (ratio >= 1.0) {
        return (ratio - 1.0) / 3.0 * 0.5 + 0.5;
      } else {
        return (ratio - 0.25) / 0.75 * 0.5;
      }
    })() * 100;

    if (window.colorLock) {
      if (ratio2 >= 50) {
        return $.Color({
          hue: 214,
          saturation: 0.95 * ((ratio2 - 50) * 2 / 100),
          lightness: 0.61,
        }).toRgbaString();
      } else {
        return $.Color({
          hue: 22,
          saturation: 0.95 * ((50 - ratio2) * 2 / 100),
          lightness: 0.61,
        }).toRgbaString();
      }
    } else {
      return $.Color({
        hue: 22 + (76 * ratio2 / 100),
        saturation: 0.80,
        lightness: 0.55,
      }).toRgbaString();
    }
  };

  const calcFgColor = c => {
    const color = $.Color(c);
    const y = Math.round(
      color.red() * 0.299 + color.green() * 0.587 + color.blue() * 0.114
    );
    return y > 153 ? '#000' : '#fff';
  };

  $.fn.killRatioColumn = function () {
    this.each(function () {
      const $this = $(this);
      const kr = $this.data('killRatio');
      const bgColor = calcBgColor(kr);
      const fgColor = calcFgColor(bgColor);
      $this.css({
        'background-color': bgColor,
        'color': fgColor,
      });
    });
    return this;
  };
})(jQuery);
