/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

($ => {
  const colorScheme = window.colorScheme;

  $.fn.festpowerDiffWinPct = function (rawData, legends) {
    this.each(function () {
      const $this = $(this);
      let timerId = null;
      $(window).resize(function () {
        if (timerId) {
          clearTimeout(timerId);
          timerId = null;
        }
        timerId = setTimeout(
          function () {
            timerId = null;

            if ($this.width() < 1) {
              return;
            }

            $this.height($this.width() * 9 / 16);
            if ($this.height() < 1) {
              return;
            }

            $.plot(
              $this,
              [
                {
                  label: legends.normal_battles,
                  data: rawData.normal_battles,
                  color: colorScheme.graph1,
                  yaxis: 2,
                  bars: {
                    show: true,
                    align: 'center',
                    barWidth: 8
                  }
                },
                {
                  label: legends.normal_pct,
                  data: rawData.normal_pct,
                  color: colorScheme.graph2,
                  yaxis: 1,
                  points: {
                    errorbars: 'y',
                    yerr: {
                      show: true,
                      asymmetric: false,
                      upperCap: '-',
                      lowerCap: '-',
                      color: colorScheme._gray.darkGray
                    }
                  }
                }
              ],
              {
                xaxis: {
                  minTickSize: 10,
                  tickFormatter: v => String(Number(v))
                },
                yaxis: {
                  min: 0,
                  max: 100,
                  minTickSize: 10,
                  tickFormatter: v => String(Number(v)) + '%'
                },
                y2axis: {
                  min: 0,
                  minTickSize: 100,
                  tickFormatter: v => String(Number(v))
                },
                legend: {
                  position: 'sw'
                }
              }
            );
          },
          20
        );
      }).resize();
    });
    return this;
  };
})(jQuery);
