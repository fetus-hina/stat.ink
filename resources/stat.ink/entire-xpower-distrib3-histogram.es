// Copyright (C) 2015-2022 AIZAWA Hina | MIT License

jQuery($ => {
  $('.xpower-distrib-chart').each(function () {
    const element = this;
    const translates = JSON.parse(element.dataset.translates);
    const canvas = element.appendChild(document.createElement('canvas'));
    const chart = new window.Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        datasets: [
          {
            label: translates.Users,
            data: JSON.parse(element.dataset.dataset),
            backgroundColor: [
              window.colorScheme.graph2
            ],
            borderColor: [
              window.colorScheme.graph2
            ],
            borderWidth: 1
          }
        ]
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
              offset: false,
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
            display: false
          }
        }
      }
    });
    element.dataset.chartjs = chart;
  });
});
