/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
((global, $) => {
  const colors = {
    graph1: window.colorScheme._accent.red,
    graph2: window.colorScheme._accent.blue,
    graph3: window.colorScheme._accent.altGreen,
    graph4: window.colorScheme._accent.orange,
  };
  const convertToStatsData = json => {
    // {{{
    const strings = JSON.parse($('#json-strings').text());
    const filtered = json.filter(v => (v.k !== null && v.d !== null));
    return [
      {
        label: strings.stats.avgKill,
        data: (() => {
          let total = 0;
          let battles = 0;
          return filtered
            .map((v, i, json) => {
              const index = i - (json.length - 1);
              total += v.k;
              ++battles;
              return [
                index,
                total / battles,
              ];
            });
        })(),
        color: colors.graph1,
        yaxis: 2,
      },
      {
        label: strings.stats.avgDeath,
        data: (() => {
          let total = 0;
          let battles = 0;
          return filtered
            .map((v, i, json) => {
              const index = i - (json.length - 1);
              total += v.d;
              ++battles;
              return [
                index,
                total / battles,
              ];
            });
        })(),
        color: colors.graph2,
        yaxis: 2,
      },
      {
        label: strings.stats.avgSpecial,
        data: (() => {
          let total = 0;
          let battles = 0;
          return filtered
            .map((v, i, json) => {
              const index = i - (json.length - 1);
              if (v.sp !== null) {
                total += v.sp;
                ++battles;
              }
              return [
                index,
                total / battles,
              ];
            });
        })(),
        color: colors.graph3,
        yaxis: 2,
      },
      {
        label: strings.stats.killRatio,
        data: (() => {
          let totalK = 0;
          let totalD = 0;
          return filtered
            .map((v, i, json) => {
              const index = i - (json.length - 1);
              totalK += v.k;
              totalD += v.d;
              return [
                index,
                totalD === null ? null : (totalK / totalD),
              ];
            });
        })(),
        color: colors.graph4,
        yaxis: 1,
      },
    ];
    // }}}
  };
  const drawStatsGraph = ($containers, dataIn) => {
    // {{{
    const strings = JSON.parse($('#json-strings').text());
    $containers.each((i, el) => {
      const $graph = $(el);
      const limit = parseInt($graph.attr('data-limit'), 10);
      const data = (() => {
        if (limit > 0) { 
          if (dataIn[0].data.length <= limit) {
            return false;
          }

          return dataIn.map(arr => {
            arr.data = arr.data.slice(-limit);
            return arr;
          });
        }
        
        return dataIn;
      })();

      if (data === false) {
        $graph.hide();
        return;
      }

      $.plot($graph, data, {
        xaxis: {
          minTickSize: 1,
          tickFormatter: v => parseInt(v, 10),
        },
        yaxis: {
          min: 0,
          minTickSize: 0.5,
          tickFormatter: v => (`${strings.stats.KR} ${v}`),
        },
        y2axis: {
          min: 0,
          minTickSize: 1,
          tickFormatter: v => (String(v) + 'x'),
        },
        legend: {
          container: $('#stat-stats-legend')
        }
      });
    });
    // }}}
  };

  global.convertToStatsData = convertToStatsData;
  global.drawStatsGraph = drawStatsGraph;
})(window, jQuery);
