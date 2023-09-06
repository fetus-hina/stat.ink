// Copyright (C) 2015-2023 AIZAWA Hina | MIT License
(function () {
  const percentFormat = (value) => (new Intl.NumberFormat(
    document.documentElement.getAttribute('lang') || 'en-US',
    {
      style: 'percent',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }
  )).format(value);

  const pat = window.pattern;
  const defBgColors = window.colorScheme._bg;
  const bgColors = [
    pat.draw('dot', defBgColors.blue),
    pat.draw('ring', defBgColors.green),
    pat.draw('zigzag', defBgColors.red)
  ];
  $('.tide-pie-chart').each(function (_, elem) {
    const canvas = elem.appendChild(document.createElement('canvas'));
    const labels = JSON.parse(elem.getAttribute('data-labels'));
    const values = JSON.parse(elem.getAttribute('data-values'));
    const ctx = canvas.getContext('2d');
    const chart = new window.Chart(ctx, {
      plugins: [
        window.ChartDataLabels
      ],
      type: 'pie',
      data: {
        datasets: [
          {
            data: [
              values['1'] || 0,
              values['2'] || 0,
              values['3'] || 0
            ],
            backgroundColor: bgColors
          }
        ],
        labels: [
          labels['1'],
          labels['2'],
          labels['3']
        ]
      },
      options: {
        animation: {
          duration: 0
        },
        aspectRatio: 1,
        legend: {
          onClick: (event, legendItem) => {
            // do nothing, to disable label-click
          }
        },
        plugins: {
          legend: {
            display: false
          },
          datalabels: {
            backgroundColor: (ctx) => {
              const value = ctx.dataset.data[ctx.dataIndex];
              return (typeof value === 'number') ? 'rgba(255, 255, 255, 0.5)' : null;
            },
            font: {
              weight: 'bold'
            },
            formatter: function (value, ctx) {
              if (value === null || value === undefined) {
                return '';
              }

              const sum = ctx.dataset.data.reduce(
                (acc, cur) => typeof (cur) === 'number' ? Number(acc) + Number(cur) : Number(acc),
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
    });
    elem.dataset.chart = chart;
  });
})();
