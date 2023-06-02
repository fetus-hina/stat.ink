/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

(($, numberFormat) => {
  $.fn.xPowerHistory = function ($legend, translations, xPowers, estimateXPowers, results) {
    const options = {
      legend: {
        container: $legend,
        labelFormatter: label => `<span class="mr-2">${label}</span>`,
        noColumns: 4,
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

    const makeWinLose = (legend, list, xPowers, onlyThisValue, color) => ({ // {{{
      label: legend,
      color,
      data: list.map((value, index, list) => ([
        -1 * (list.length - 1) + index,
        list[index] === onlyThisValue
          ? (xPowers[index] ? xPowers[index] : 2000)
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
          translations.estimate,
          estimateXPowers,
          window.colorScheme._gray.darkGray,
          1
        ),
        makeData(
          translations.xPower,
          xPowers,
          window.colorScheme.graph1,
          3
        ),
        makeWinLose(
          translations.lose,
          results,
          xPowers,
          false,
          window.colorScheme.lose
        ),
        makeWinLose(
          translations.win,
          results,
          xPowers,
          true,
          window.colorScheme.win
        )
      ],
      options
    );

    return this;
  };
})(jQuery, window.numberFormat);
