/*! Copyright (C) 2015-2023 AIZAWA Hina | MIT License */

($ => {
  const percentFormat = function (value) {
    return (
      new Intl.NumberFormat(document.documentElement.getAttribute('lang') || 'en-US', {
        style: 'percent',
        minimumFractionDigits: 1,
        maximumFractionDigits: 1
      })
    ).format(value);
  };

  const defBgColors = window.colorScheme._bg;
  const bgColors = [
    defBgColors.blue, // win
    defBgColors.red // lose
  ];

  $.fn.simpleWinLosePie = function () {
    this.each(function (_, elem) {
      const canvas = elem.appendChild(document.createElement('canvas'));
      const labels = JSON.parse(elem.getAttribute('data-labels'));
      const values = JSON.parse(elem.getAttribute('data-values'));
      const ctx = canvas.getContext('2d');
      elem.dataset.chart = new window.Chart(ctx, {
        plugins: [
          window.ChartDataLabels
        ],
        type: 'pie',
        data: {
          datasets: [
            {
              data: [
                values.win,
                values.lose
              ],
              backgroundColor: bgColors
            }
          ],
          labels: [
            labels.win,
            labels.lose
          ]
        },
        options: {
          animation: {
            duration: 0
          },
          aspectRatio: 1,
          legend: {
            onClick: () => {} // do nothing, to disable label-click
          },
          plugins: {
            legend: {
              display: false
            },
            datalabels: {
              backgroundColor: function (ctx) {
                const value = ctx.dataset.data[ctx.dataIndex];
                return (typeof value === 'number')
                  ? 'rgba(255, 255, 255, 0.5)'
                  : null;
              },
              font: {
                size: 9,
                weight: 'bold'
              },
              formatter: function (value, ctx) {
                if (value === null || value === undefined || value < 1) {
                  return null;
                }

                const sum = ctx.dataset.data.reduce(
                  (acc, cur) => Number(acc) + (typeof (cur) === 'number' ? Number(cur) : 0),
                  0
                );

                if (sum < 1) {
                  return null;
                }

                const label = ctx.chart.legend.legendItems[ctx.dataIndex].text;
                return label + '\n' + percentFormat(value / sum);
              }
            }
          }
        }
      });
    });
    return this;
  };
})(jQuery);
