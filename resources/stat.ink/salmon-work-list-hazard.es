($ => {
  $(() => {
    const calcFgColor = c => {
      // {{{
      const color = $.Color(c);
      const y = Math.round(
        color.red() * 0.299 +
        color.green() * 0.587 +
        color.blue() * 0.114
      );
      return y > 153 ? '#000' : '#fff';
      // }}}
    };

    const calcBgColor = rate => {
      const rate2 = 1 - (rate / 200.0); // 0.0(danger) - 1.0(easy)
      return $.Color({
        hue: 10,
        saturation: 0.9,
        lightness: 0.5 + rate2 / 2,
      }).toRgbaString();
    };

    $('.danger-rate-bg').each((i, el) => {
      const $this = $(el);
      const dangerRate = parseFloat($this.attr('data-danger-rate'));
      const bgColor = calcBgColor(dangerRate);
      $this.css({
        'background-color': bgColor,
        'color': calcFgColor(bgColor),
      });
    });
  });
})(jQuery);
