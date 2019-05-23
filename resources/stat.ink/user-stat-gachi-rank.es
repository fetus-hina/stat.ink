/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

(($, window) => {
  const ranks = [
    'C-',
    'C',
    'C+',
    'B-',
    'B',
    'B+',
    'A-',
    'A',
    'A+',
    'S',
    'S+',
  ];

  $.fn.rankHistory = function ($legends, $chkMovingAvgEnabled, rawData, texts) {
    const $elements = this;
    const splitRules = data => { // {{{
      const ret = {
        area: [],
        yagura: [],
        hoko: [],
      };
      let prevIndex = null;
      let prevRule = null;
      let prevValue = null;
      data.forEach((item, i) => {
        if (prevRule !== item.rule && prevRule !== null) {
          ret[prevRule].push([item.index, null]);
          ret[item.rule].push([prevIndex, prevValue]);
        }
        ret[item.rule].push([item.index, item.exp]);
        prevIndex = item.index;
        prevRule = item.rule;
        prevValue = item.exp;
      });
      return ret;
    }; // }}}
    const dataRules = splitRules(rawData);
    const redraw = () => {
      const data = [
        {
          label: texts.area,
          data: dataRules.area,
          color: window.colorScheme.area,
        },
        {
          label: texts.yagura,
          data: dataRules.yagura,
          color: window.colorScheme.yagura,
        },
        {
          label: texts.hoko,
          data: dataRules.hoko,
          color: window.colorScheme.hoko,
        },
      ];
      if ($chkMovingAvgEnabled && $chkMovingAvgEnabled.prop('checked')) {
        data.push({
          label: texts.movingAvg10,
          data: rawData.map(v => [v.index, v.movingAvg]),
          color: window.colorScheme.moving1,
        });
        data.push({
          label: texts.movingAvg50,
          data: rawData.map(v => [v.index, v.movingAvg50]),
          color: window.colorScheme.moving2,
        });
      }

      $elements.each(function () {
        const $this = $(this);
        const limit = Number($this.data('limit') || 0);
        if (limit > 0 && rawData.length <= limit) {
          $this.hide();
          return;
        }

        $.plot($this, data, {
          xaxis: {
            minTickSize: 1,
            min: limit > 0 ? -limit : null,
            tickFormatter: v => String(Number(v)),
          },
          yaxis: {
            minTickSize: 10,
            tickFormatter: v => {
              if (v >= 1100) {
                return v > 1100 ? '' : 'S+ 99';
              }
              if (v < 0) {
                return '';
              }
              const rank = Math.floor(v / 100);
              const exp = v % 100;
              return `${ranks[rank]} ${exp}`;
            }
          },
          legend: {
            container: $legends,
          },
        });
      });
    };

    redraw();
    if ($chkMovingAvgEnabled) {
      $chkMovingAvgEnabled.change(() => {
        redraw();
      });
    }

    return $elements;
  };
})(jQuery, window);
