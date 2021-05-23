/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
($ => {
  const $graph = $('#graph');

  const drawGraph = () => {
    // {{{
    const dateToUnixTime = d => {
      return (new Date(d + 'T00:00:00Z')).getTime();
    };
    const formatDate = date => {
      const zeroPad = n => {
        n = String(n);
        return (n.length === 1) ? `0${n}` : n;
      };
      return date.getUTCFullYear() + '-' + zeroPad(date.getUTCMonth() + 1) + '-' + zeroPad(date.getUTCDate());
    };

    const json = JSON.parse($graph.attr('data-data'));
    const data = [
      {
        label: $graph.attr('data-label-battle'),
        data: json.map(v => [dateToUnixTime(v.date), v.battle]),
        bars: {
          show: true,
          align: 'center',
          barWidth: 86400 * 1000 * 0.8,
          lineWidth: 1
        },
        color: window.colorScheme.graph1
      },
      {
        label: $graph.attr('data-label-user'),
        data: json.map(v => [dateToUnixTime(v.date), v.user]),
        yaxis: 2,
        color: window.colorScheme.graph2
      }
    ];
    $.plot($graph, data, {
      xaxis: {
        mode: 'time',
        minTickSize: [1, 'day'],
        tickFormatter: v => formatDate(new Date(v))
      },
      yaxis: {
        min: 0,
        minTickSize: 1,
        tickFormatter: v => parseInt(String(v), 10)
      },
      y2axis: {
        min: 0,
        minTickSize: 1,
        tickFormatter: v => parseInt(String(v), 10),
        position: 'right'
      },
      legend: {
        position: 'nw'
      }
    });
    // }}}
  };

  let timerId = null;
  $(window).resize(() => {
    if (timerId !== null) {
      clearTimeout(timerId);
    }
    timerId = setTimeout(() => {
      timerId = null;
      $graph.height($graph.width() * 10 / 16);
      drawGraph();
    }, 33);
  }).resize();
})(jQuery);
