// Copyright (C) 2015-2022 AIZAWA Hina | MIT License
(function () {
  // Const percentFormat = (value) => (new Intl.NumberFormat(
  //   document.documentElement.getAttribute('lang') || 'en-US',
  //   {
  //     style: 'percent',
  //     minimumFractionDigits: 2,
  //     maximumFractionDigits: 2
  //   }
  // )).format(value);

  const colorList = (num) => {
    const pat = window.pattern;
    const defBgColors = window.colorScheme._bg;
    // const colorList = [defBgColors.red, defBgColors.yellow, defBgColors.blue, defBgColors.purple];
    const colorList = [defBgColors.green, defBgColors.red, defBgColors.blue, defBgColors.purple, defBgColors.yellow];
    const patList = ['diagonal', 'ring', 'zigzag', 'cross', 'dot', 'zigzag-vertical'];
    const result = [];
    for (let i = 0; i < num; ++i) {
      result.push(
        pat.draw(patList[i % patList.length], colorList[i % colorList.length])
      );
    }
    console.log(result);
    return result;
  };

  $('.tide-event-pie-chart').each(function (_, elem) {
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
              values['3'] || 0,
              values['4'] || 0,
              values['5'] || 0,
              values['6'] || 0,
              values['7'] || 0,
              values['8'] || 0,
              values['0'] || 0
            ],
            backgroundColor: colorList(9)
          }
        ],
        labels: [
          labels['1'] || '',
          labels['2'] || '',
          labels['3'] || '',
          labels['4'] || '',
          labels['5'] || '',
          labels['6'] || '',
          labels['7'] || '',
          labels['8'] || '',
          labels['0'] || ''
        ]
      },
      options: {
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
            // 細かくなりすぎるので全部消すことにしたが、
            // 本当はなんとかしたい
            display: false
            // backgroundColor: (ctx) => {
            //   const value = ctx.dataset.data[ctx.dataIndex];
            //   return (typeof value === 'number') ? 'rgba(255, 255, 255, 0.5)' : null;
            // },
            // font: {
            //   weight: 'bold'
            // },
            // formatter: function (value, ctx) {
            //   if (value === null || value === undefined) {
            //     return '';
            //   }

            //   const sum = ctx.dataset.data.reduce(
            //     (acc, cur) => typeof (cur) === 'number' ? Number(acc) + Number(cur) : Number(acc),
            //     0
            //   );
            //   if (sum < 1) {
            //     return '';
            //   }

            //   const label = ctx.chart.legend.legendItems[ctx.dataIndex].text;
            //   return label + '\n' + percentFormat(value / sum);
            // }
          }
        }
      }
    });
    elem.dataset.chart = chart;
  });
})();
