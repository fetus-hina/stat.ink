/*
  var colorHigh = $.Color("#3e8ffa"); // H:214, S:75, V:98 / S:95, L:61
  var colorMid  = $.Color("#888888"); // H:  0, S: 0, V:53 / S: 0, L:53
  var colorLow  = $.Color("#fa833e"); // H: 22, S:75, V:98 / S:95, L:61
*/
($ => {
  $('.rule-table').each((i, el) => {
    const $table = $(el);
    const $cells = $('.percent-cell', $table);
    const maxBattle = $cells.toArray()
      .map(el => parseInt(el.getAttribute('data-battle'), 10))
      .reduce((a, b) => Math.max(a, b), 0);
    $cells.each((i, el) => {
      const $cell = $(el);
      const battle = parseInt($cell.attr('data-battle'), 10);
      if (battle < 1) {
        return;
      }
      const battleCountCoefficient = Math.min(1.0, (battle * 2) / maxBattle);
      const percent = parseFloat($cell.attr('data-percent'));

      /* 10%-90% scale to 0%-100% */
      const ratio = Math.min(100, Math.max(0, (percent - 10) * (100 / 80)));

      /* calc background color */
      if (window.colorLock) {
        if (ratio >= 50) {
          $cell.css(
            'background-color',
            $.Color({
              hue: 214,
              saturation: 0.95 * ((ratio - 50) * 2 / 100),
              lightness: 0.53 + 0.08 * ((ratio - 50) * 2 / 100),
            })
              .alpha(battleCountCoefficient)
              .blend($.Color('#ffffff'))
              .toRgbaString()
          );
        } else {
          $cell.css(
            'background-color',
            $.Color({
              hue: 22,
              saturation: 0.95 * ((50 - ratio) * 2 / 100),
              lightness: 0.53 + 0.08 * ((50 - ratio) * 2 / 100)
            })
              .alpha(battleCountCoefficient)
              .blend($.Color('#ffffff'))
              .toRgbaString()
          );
        }
      } else {
        $cell.css(
          'background-color',
          $.Color({
            hue: 120 * ratio / 100,
            saturation: 0.95,
            lightness: 0.53
          })
            .alpha(battleCountCoefficient)
            .blend($.Color('#ffffff'))
            .toRgbaString()
        );
      }

      const c = $.Color($cell.css('background-color'));
      const y = Math.round(c.red() * 0.299 + c.green() * 0.587 + c.blue() * 0.114);
      $cell.css('color', (y > 153) ? '#000' : '#fff');
    });
  });
})(jQuery);
