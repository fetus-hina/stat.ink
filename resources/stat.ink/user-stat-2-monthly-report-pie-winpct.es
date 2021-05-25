/*! Copyright (C) 2015-2021 AIZAWA Hina | MIT License */

jQuery($ => {
  const percentFormat = (value) => (
    (new Intl.NumberFormat(
      document.documentElement.getAttribute('lang') || 'en-US',
      {
        style: 'percent',
        minimumFractionDigits: 1,
        maximumFractionDigits: 1
      }
    ))
      .format(value)
  );

  const pat = window.pattern;
  const defBgColors = window.colorScheme._bg;
  const bgColors = [
    pat.draw('dot', defBgColors.blue),
    pat.draw('cross', defBgColors.red)
  ];

  $('.pie-chart.win-pct').each(function (_, elem) {
    const canvas = elem.appendChild(document.createElement('canvas'));
    const labels = JSON.parse(elem.getAttribute('data-labels'));
    const values = JSON.parse(elem.getAttribute('data-values'));
    const ctx = canvas.getContext('2d');
    // eslint-disable-next-line
    const chart = new window.Chart(
      ctx,
      {
        // {{{
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
          aspectRatio: 1,
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
                weight: 'bold'
              },
              formatter: function (value, ctx) {
                if (value === null || value === undefined) {
                  return '';
                }

                const sum = ctx.dataset.data.reduce(
                  function (acc, cur) {
                    return (typeof (cur) === 'number')
                      ? Number(acc) + Number(cur)
                      : Number(acc);
                  },
                  0
                );
                if (sum < 1) {
                  return '';
                }

                const label = ctx.chart.legend.legendItems[ctx.dataIndex].text;
                return label + '\n' + percentFormat(value / sum);
              }
            }
          }
        }
        // }}}
      }
    );
  });
});
