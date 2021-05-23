/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

(($, window) => {
  $.fn.wp = function ($legends, rawData, texts) {
    const $elements = this;
    const filter = (rawData, filterFunc) => {
      const results = [];
      rawData.forEach(row => {
        if (!filterFunc(row)) {
          return;
        }
        results.push(row);
      });

      // index 詰めなおし
      results.forEach((row, i) => {
        results[i].index = -1 * (results.length - 1) + i;
      });

      // 勝率再計算
      results.forEach((row, i) => {
        const winCount = results.slice(0, i + 1).filter(row => row.is_win).length;
        results[i].totalWP = 100 * winCount / (i + 1);
        results[i].movingWP = null;
        results[i].movingWP50 = null;

        if (i >= 19) {
          const winCount20 = results.slice(i - 19, i + 1).filter(row => row.is_win).length;
          results[i].movingWP = 100 * winCount20 / 20;
        }

        if (i >= 49) {
          const winCount50 = results.slice(i - 49, i + 1).filter(row => row.is_win).length;
          results[i].movingWP50 = 100 * winCount50 / 50;
        }
      });

      return results;
    };
    const splitRules = json => { // {{{
      const ret = {
        area: [],
        yagura: [],
        hoko: []
      };
      let prevIndex = null;
      let prevRule = null;
      let prevValue = null;
      json.forEach(data => {
        if (prevRule !== data.rule && prevRule !== null) {
          ret[prevRule].push([data.index, null]);
          ret[data.rule].push([prevIndex, prevValue]);
        }
        ret[data.rule].push([data.index, data.totalWP]);
        prevIndex = data.index;
        prevRule = data.rule;
        prevValue = data.totalWP;
      });
      return ret;
    }; // }}}

    $elements.each(function () {
      const $graph = $(this);
      const map = $graph.data('map');
      const list = map
        ? filter(rawData, row => row.map === map)
        : filter(rawData, () => true);

      const limit = Number($graph.data('limit'));
      if (limit > 0 && list.length <= limit) {
        $graph.hide();
        return;
      }

      const rules = splitRules(list);
      const data = [
        {
          label: texts.area,
          data: rules.area,
          color: window.colorScheme.area
        },
        {
          label: texts.yagura,
          data: rules.yagura,
          color: window.colorScheme.yagura
        },
        {
          label: texts.hoko,
          data: rules.hoko,
          color: window.colorScheme.hoko
        },
        {
          label: texts.moving20,
          data: list.map(v => [v.index, v.movingWP]),
          color: window.colorScheme.moving1
        },
        {
          label: texts.moving50,
          data: list.map(v => [v.index, v.movingWP50]),
          color: window.colorScheme.moving2
        }
      ];

      $.plot($graph, data, {
        xaxis: {
          min: limit > 0 ? -limit : null,
          minTickSize: 1,
          tickFormatter: v => String(Number(v))
        },
        yaxis: {
          min: 0,
          max: 100,
          minTickSize: 5,
          tickFormatter: v => String(v) + '%'
        },
        legend: {
          container: $legends
        }
      });
    });
    return $elements;
  };
})(jQuery, window);
