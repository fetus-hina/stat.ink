/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
((global, $) => {
  const colors = {
    graph1: window.colorLock ? window.colorScheme.graph1 : window.colorScheme._accent.orange,
    moving1: window.colorLock ? window.colorScheme.moving1 : 'rgba(64,237,64,.5)',
    moving2: window.colorLock ? window.colorScheme.moving2 : 'rgba(148,64,237,.5)'
  };
  let rankPeak = 10;
  const convertToWPData = (bigJson, isRanked) => {
    // {{{
    const strings = JSON.parse($('#json-strings').text());
    const icons = {
      left1: '<span class="bi bi-chevron-left"></span>',
      left2: '<span class="bi bi-chevron-double-left"></span>',
      right1: '<span class="bi bi-chevron-right"></span>',
      right2: '<span class="bi bi-chevron-double-right"></span>',
    };
    const data = [];
    if (isRanked) {
      const x = bigJson.filter(v => (v.win !== null && v.x !== null)).length;
      if (x > 0) {
        data.push({
          label: `${icon.right2} ${strings.rank.xpower}`,
          data: bigJson
            .filter(v => (v.win !== null))
            .map((v, i, json) => {
              const index = i - (json.length - 1);
              return [index, v.x];
            }),
          color: window.colorScheme._gray.lightGray,
          yaxis: 3
        });
      }
      data.push({
        label: `${icons.right1} ${strings.rank.rank}`,
        data: bigJson
          .filter(v => (v.win !== null))
          .map((v, i, json) => {
            const index = i - (json.length - 1);
            if (rankPeak < v.r) {
              rankPeak = v.r;
            }
            return [index, v.r];
          }),
        color: window.colorScheme._gray.darkGray,
        yaxis: 2,
        lines: {
          steps: true
        }
      });
    }
    data.push({
      label: (isRanked ? icons.left1 : '') + ' ' + strings.wp.last50,
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
      color: colors.moving2
    });
    data.push({
      label: (isRanked ? icons.left1 : '') + ' ' + strings.wp.last20,
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
      color: colors.moving1
    });
    data.push({
      label: (isRanked ? icons.left1 : '') + ' ' + strings.wp.entire,
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
    });
    return data;
    // }}}
  };
  const drawWPGraph = ($containers, data) => {
    // {{{
    // const strings = JSON.parse($('#json-strings').text());
    const ranks = [
      'C-', 'C', 'C+', 'B-', 'B', 'B+', 'A-', 'A', 'A+', 'S', 'S+'
    ];
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
          tickFormatter: v => parseInt(v, 10)
        },
        yaxes: [
          {
            min: 0,
            max: 100,
            minTickSize: 25,
            tickFormatter: v => (v + '%')
          },
          {
            min: 0,
            minTickSize: 1,
            position: 'right',
            ticks: [
              1,
              4,
              7,
              10,
              rankPeak >= 10 ? 15 : null,
              rankPeak >= 15 ? 20 : null,
              rankPeak >= 20 ? 25 : null,
              rankPeak >= 25 ? 30 : null,
              rankPeak >= 30 ? 35 : null,
              rankPeak >= 35 ? 40 : null,
              rankPeak >= 40 ? 45 : null,
              rankPeak >= 45 ? 50 : null,
              rankPeak >= 50 ? 55 : null,
              rankPeak >= 55 ? 60 : null
            ].filter(v => v !== null),
            tickFormatter: v => {
              if (v < 10) {
                return ranks[v];
              } else if (v === 20) {
                return 'X';
              } else {
                return 'S+ ' + (v - 10);
              }
            }
          },
          {
            minTickSize: 1,
            position: 'right',
            tickFormatter: v => Number(v).toFixed(1)
          }
        ],
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
