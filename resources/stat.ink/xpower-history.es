/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

($ => {
  $.fn.xPowerHistory = function (xPowers, estimateXPowers) {
    const options = {
      legend: {
        show: false,
      },
      xaxis: {
        tickSize: 1,
        minTickSize: 1,
        show: false,
      },
      yaxis: {
        minTickSize: 10,
        tickFormatter: function (value, obj) {
          return Number(value).toFixed(1);
        },
      },
    };

    const makeData = (list, color, lineWidth) => ({
      color: color,
      data: list.map((value, index, list) => ([
        -1 * (list.length - 1) + index,
        value,
      ])),
      lines: {
        show: true,
        lineWidth: lineWidth,
        step: true,
      },
      points: {
        show: false,
      },
    });

    $.plot(
      this,
      [
        makeData(estimateXPowers, window.colorScheme._gray.darkGray, 1),
        makeData(xPowers, window.colorScheme.graph1, 3),
      ],
      options
    );

    return this;
  };
})(jQuery);
