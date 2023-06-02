/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

($ => {
  const numberFormat = window.numberFormat;

  $.fn.freshnessHistory = function (freshness) {
    const options = {
      legend: {
        show: false
      },
      xaxis: {
        minTickSize: 1,
        show: false,
        tickSize: 1
      },
      yaxis: {
        min: 0,
        minTickSize: 1,
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

    const makeData = (list, color, lineWidth) => ({ // {{{
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
        show: true
      }
    }); // }}}

    $.plot(this, [makeData(freshness, window.colorScheme.graph1, 3)], options);

    return this;
  };
})(jQuery);
