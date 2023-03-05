// Copyright (C) 2015-2023 AIZAWA Hina | MIT License

jQuery($ => {
  $('.xpower-distrib-chart').each(function () {
    const element = this;
    const translates = JSON.parse(element.dataset.translates);
    const canvas = element.appendChild(document.createElement('canvas'));
    const normalDistribution = JSON.parse(element.dataset.normalDistribution);
    const chart = new window.Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        datasets: [
          normalDistribution
            ? {
                backgroundColor: [window.colorScheme.graph1],
                borderColor: [window.colorScheme.graph1],
                borderWidth: 2,
                data: normalDistribution,
                label: translates['Normal Distribution'],
                pointRadius: 0,
                type: 'line'
              }
            : null,
          {
            backgroundColor: [window.colorScheme.graph2],
            borderColor: [window.colorScheme.graph2],
            borderWidth: 1,
            data: JSON.parse(element.dataset.dataset),
            label: translates.Users
          }
        ].filter(v => v !== null)
      },
      options: {
        aspectRatio: 16 / 9,
        layout: {
          padding: 0
        },
        legend: {
          display: false
        },
        scales: {
          y: {
            beginAtZero: true
          },
          x: {
            grid: {
              offset: false
            },
            offset: true,
            type: 'linear',
            ticks: {
              stepSize: 200
            }
          }
        },
        plugins: {
          legend: {
            display: true,
            reverse: true
          },
          tooltip: {
            enabled: false
          }
        }
      }
    });
    element.dataset.chartjs = chart;
  });
});
