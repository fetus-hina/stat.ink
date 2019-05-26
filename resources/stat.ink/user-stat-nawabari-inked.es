/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

($ => {
  const colorScheme = window.colorScheme;
  const thousandSep = window.numberFormat.thousand;
  const avg = data => data.length ? (data.reduce((a, b) => a + b) / data.length) : null;

  $.fn.turfInked = function (rawData, stats, mapArea, texts) {
    const dataValues = rawData.map(v => v[1]);
    const avgInked = avg(dataValues);
    $.plot(
      this,
      [
        {
          label: texts.turfInked,
          data: rawData,
          color: colorScheme.graph1,
          lines: {
            show: true,
            fill: true,
          },
        },
        {
          label: texts.percentile,
          data: (dataValues.length > 1)
            ? [
              [rawData[0][0], stats.pct5],
              [0, stats.pct5],
            ]
            : [],
          color: colorScheme._gray.darkGray,
          lines: {
            lineWidth: 1,
          },
        },
        {
          label: false,
          data: (dataValues.length > 1)
            ? [
              [rawData[0][0], stats.pct95],
              [0, stats.pct95],
            ]
            : [],
          color: colorScheme._gray.darkGray,
          lines: {
            lineWidth: 1,
          }
        },
        {
          label: texts.average,
          data: (dataValues.length > 1)
            ? [
              [rawData[0][0], avgInked],
              [0, avgInked],
            ]
            : [],
          color: colorScheme.graph2,
          lines: {
            lineWidth: 1,
          },
        },
      ],
      {
        xaxis: {
          minTickSize: 1,
          tickFormatter: v => String(Number(v)),
        },
        yaxis: {
          minTickSize: 100,
          min: 0,
          tickFormatter: v => String(Number(v)).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1' + thousandSep) + 'p',
        },
        legend: {
          position: 'nw',
        },
      }
    );
  };
})(jQuery);
