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
    const $graphs = $('.graph.stat-kill-death');
    $graphs.height($graphs.width() * 9 / 16);
    $graphs.each(function () {
      const $graph = $(this);
      const json = window.kddata;
      const maxKD = 30;
      let total = 0;
      const kills = [];
      const deaths = [];
      for (let i = 0; i <= maxKD; ++i) {
        kills.push(0);
        deaths.push(0);
      }
      $.each(json, function () {
        total += this.battle;

        if (maxKD >= this.kill) {
          kills[this.kill] += this.battle;
        }

        if (maxKD >= this.death) {
          deaths[this.death] += this.battle;
        }
      });

      const data = [
        {
          label: $graph.attr('data-legends-kill'),
          data: kills.map((v, i) => [i - 0.5, v * 100 / total]),
          color: window.colorScheme.win,
        },
        {
          label: $graph.attr('data-legends-death'),
          data: deaths.map((v, i) => [i - 0.5, v * 100 / total]),
          color: window.colorScheme.lose,
        },
      ];
      $.plot($graph, data, {
        xaxis: {
          min: -0.5,
          minTickSize: 1,
          tickFormatter: v => `${v} K, D`,
        },
        yaxis: {
          min: 0,
          tickFormatter: v => percentFormat(v / 100, 1),
        },
        series: {
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
