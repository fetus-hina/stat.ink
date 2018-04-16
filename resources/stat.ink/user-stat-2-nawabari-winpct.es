/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
((global, $) => {
  const colors = {
    graph1: window.colorLock ? window.colorScheme.graph1 : window.colorScheme._accent.orange,
    moving1: window.colorLock ? window.colorScheme.moving1 : 'rgba(64,237,64,.5)',
    moving2: window.colorLock ? window.colorScheme.moving2 : 'rgba(148,64,237,.5)',
  };
  const convertToWPData = bigJson => {
    // {{{
    const strings = JSON.parse($('#json-strings').text());
    return [
      {
        label: strings.wp.last50,
        data: (() => {
          let list = [];
          return bigJson
            .filter(v => (v.win !== null))
            .map((v, i, json) => {
              const index = i - (json.length - 1);
              list.push(v.win ? 1 : 0);
              if (list.length > 50) {
                list = list.slice(-50);
              }
              const wins = list.reduce((v1, v2) => (v1 + v2), 0);
              return [index, 100.0 * wins / list.length];
            });
        })(),
        color: colors.moving2,
      },
      {
        label: strings.wp.last20,
        data: (() => {
          let list = [];
          return bigJson
            .filter(v => (v.win !== null))
            .map((v, i, json) => {
              const index = i - (json.length - 1);
              list.push(v.win ? 1 : 0);
              if (list.length > 20) {
                list = list.slice(-20);
              }
              const wins = list.reduce((v1, v2) => (v1 + v2), 0);
              return [index, 100.0 * wins / list.length];
            });
        })(),
        color: colors.moving1,
      },
      {
        label: strings.wp.entire,
        data: (() => {
          let totalBattles = 0;
          let totalWins = 0;
          return bigJson
            .filter(v => (v.win !== null))
            .map((v, i, json) => {
              const index = i - (json.length - 1);
              ++totalBattles;
              if (v.win) {
                ++totalWins;
              }
              return [index, 100.0 * totalWins / totalBattles];
            });
        })(),
        color: colors.graph1
      },
    ];
    // }}}
  };
  const drawWPGraph = ($containers, data) => {
    // {{{
    // const strings = JSON.parse($('#json-strings').text());
    $containers.each((i, el) => {
      const $graph = $(el);
      const limit = parseInt($graph.attr('data-limit'), 10);
      if (limit > 0 && data[0].data.length <= limit) {
        $graph.hide();
        return;
      }
      $.plot($graph, data, {
        xaxis: {
          min: limit > 0 ? -limit : null,
          minTickSize: 1,
          tickFormatter: v => parseInt(v, 10),
        },
        yaxis: {
          min: 0,
          max: 100,
          minTickSize: 25,
          tickFormatter: v => (v + '%'),
        },
        legend: {
          container: $('#stat-wp-legend')
        }
      });
    });
    // }}}
  };

  global.convertToWPData = convertToWPData;
  global.drawWPGraph = drawWPGraph;
})(window, jQuery);
