/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

($ => {
  const numberFormat = window.numberFormat;

  $.fn.festPowerHistory = function (
    $legend,
    translations,
    festPowers,
    estimateGoodGuysFestPowers,
    estimateBadGuysFestPowers,
    results
  ) {
    const options = {
      legend: {
        container: $legend,
        labelFormatter: label => `<span class="mr-2">${label}</span>`,
        noColumns: 3,
        show: true,
        sorted: 'reverse'
      },
      xaxis: {
        minTickSize: 1,
        show: false,
        tickSize: 1
      },
      yaxis: {
        minTickSize: 10,
        tickFormatter: value => {
          return Number(value).toFixed(1).replace(
            /^(\d+)\.(\d+)$/,
            (_, i, f) => {
              return i.replace(
                /(\d)(?=(\d\d\d)+(?!\d))/g,
                '$1' + numberFormat.thousand
              ) + numberFormat.decimal + f;
            }
          );
        }
      }
    };

    const unknownFestPowerValue = (() => {
      const powers = festPowers.filter(v => v > 0);
      return powers.length
        ? powers.reduce((a, b) => a + b) / powers.length
        : null;
    })();

    const makeData = (legend, list, color, lineWidth) => ({ // {{{
      label: legend,
      color,
      data: list.map((value, index, list) => ([
        -1 * (list.length - 1) + index,
        value
      ])),
      lines: {
        show: true,
        lineWidth
      },
      points: {
        show: false
      }
    }); // }}}

    const makeWinLose = (legend, list, festPowers, onlyThisValue, color) => ({ // {{{
      label: legend,
      color,
      data: list.map((value, index, list) => ([
        -1 * (list.length - 1) + index,
        list[index] === onlyThisValue
          ? festPowers[index]
            ? festPowers[index]
            : unknownFestPowerValue || (estimateGoodGuysFestPowers[index]
              ? estimateGoodGuysFestPowers[index]
              : 2000)
          : null
      ])),
      lines: {
        show: false
      },
      points: {
        show: true
      }
    }); // }}}

    $.plot(
      this,
      [
        makeData(
          translations.estimateBad,
          estimateBadGuysFestPowers,
          window.colorScheme._accent.pink,
          2
        ),
        makeData(
          translations.estimateGood,
          estimateGoodGuysFestPowers,
          window.colorScheme._accent.sky,
          2
        ),
        makeData(
          translations.festPower,
          festPowers,
          window.colorScheme.graph1,
          3
        ),
        makeWinLose(
          translations.lose,
          results,
          festPowers,
          false,
          window.colorScheme.lose
        ),
        makeWinLose(
          translations.win,
          results,
          festPowers,
          true,
          window.colorScheme.win
        )
      ],
      options
    );

    return this;
  };
})(jQuery);
