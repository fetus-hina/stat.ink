/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

(($, window) => {
  $.fn.wp = function ($legend, rawData, texts) {
    this.each(function () {
      const $this = $(this);
      const limit = Number($this.data('limit'));
      if (limit > 0 && rawData.length <= limit) {
        $this.hide();
        return;
      }

      $.plot(
        $this,
        [
          {
            label: texts.wp,
            data: rawData.map(v => [v.index, v.totalWP]),
            color: window.colorScheme.graph1
          },
          {
            label: texts.wp20,
            data: rawData.map(v => [v.index, v.movingWP]),
            color: window.colorScheme.moving1
          },
          {
            label: texts.wp50,
            data: rawData.map(v => [v.index, v.movingWP50]),
            color: window.colorScheme.moving2
          }
        ],
        {
          xaxis: {
            min: limit > 0 ? -limit : null,
            minTickSize: 1,
            tickFormatter: v => String(Number(v))
          },
          yaxis: {
            min: 0,
            max: 100,
            minTickSize: 1,
            tickFormatter: v => String(Number(v)) + '%'
          },
          legend: {
            container: $legend
          }
        }
      );
    });
    return this;
  };
})(jQuery, window);
