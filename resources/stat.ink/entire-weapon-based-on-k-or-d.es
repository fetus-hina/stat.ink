jQuery($ => {
  function percentFormat(value, digit) {
    const locales = [
      $('html').attr('lang'),
      'en-US',
    ];
    const formatter = new Intl.NumberFormat(locales, {
      style: 'percent',
      minimumFractionDigits: digit,
      maximumFractionDigits: digit,
    });
    return formatter.format(value);
  }

  function update() {
    const $graphs = $('.graph.stat-wp');
    $graphs.height($graphs.width() * 9 / 16);
    $graphs.each(function () {
      const $graph = $(this);
      const kdKey = $graph.attr('data-base');
      const scale = $graph.attr('data-scale') === 'yes';
      const json = window.kddata;
      const maxKD = 30;
      const win = [];
      const lose = [];
      for (let i = 0; i <= maxKD; ++i) {
        win.push(0);
        lose.push(0);
      }
      $.each(json, function () {
        if (maxKD >= this[kdKey]) {
          win[this[kdKey]] += this.win;
          lose[this[kdKey]] += this.battle - this.win;
        }
      });
      if (scale) {
        for (let i = 0; i <= maxKD; ++i) {
          const t = win[i] + lose[i];
          if (t > 0) {
            win[i] = win[i] * 100 / t;
            lose[i] = lose[i] * 100 / t;
          } else {
            win[i] = lose[i] = 0;
          }
        }
      }
      const data = [
        {
          label: $graph.attr('data-legends-win'),
          data: win.map((v, i) => [i - 0.5, v]),
          color: window.colorScheme.win,
        },
        {
          label: $graph.attr('data-legends-lose'),
          data: lose.map((v, i) => [i - 0.5, v]),
          color: window.colorScheme.lose,
        }
      ];
      $.plot($graph, data, {
        xaxis: {
          min: -0.5,
          minTickSize: 1,
          tickFormatter: v => v + (kdKey === 'kill' ? ' K' : ' D'),
        },
        yaxis: {
          min: 0,
          max: scale ? 100 : undefined,
          tickFormatter: v => percentFormat(v / 100, 1),
          show: scale,
        },
        series: {
          stack: !!scale,
          lines: {
            show: true,
            fill: true,
            steps: true,
          },
        },
      });
    });
  }

  let timerId = null;
  $(window).resize(() => {
    if (timerId !== null) {
      clearTimeout(timerId);
      timerId = null;
    }

    timerId = setTimeout(() => {
      timerId = null;
      update();
    }, 10);
  });
});
