/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

($ => {
  const colorScheme = window.colorScheme;

  $(() => {
    const $modal = $('#battles-summary-modal');
    const $anchors = $('.summary-box-plot');
    const $plot = $('.box-plot', $modal);
    const $dataFields = $('span[data-key]', $modal);

    let dataStats;
    let dispData;
    $anchors.click(function () {
      const $anchor = $(this);
      dataStats = JSON.parse($anchor.attr('data-stats'));
      dispData = JSON.parse($anchor.attr('data-disp'));
      $modal.modal();
    });

    const redraw = () => {
      let timerId = null;
      if (timerId) {
        clearTimeout(timerId);
        timerId = null;
      }
      timerId = setTimeout(() => {
        timerId = null;

        if ($plot.width() < 1 || $plot.height() < 1) {
          return;
        }

        $dataFields.each((i, el) => {
          const $this = $(el);
          const key = $this.attr('data-key');
          $this.text((dispData && dispData[key]) ? dispData[key] : '');
        });

        const data = (!dispData || !dataStats)
          ? []
          : [
            {
              data: [
                // min
                [1.5 - 0.25, dataStats.min],
                [1.5 + 0.25, dataStats.min],
                [null, null],
                [1.5, dataStats.min],
                [1.5, dataStats.q1],
                [null, null],

                // max
                [1.5 - 0.25, dataStats.max],
                [1.5 + 0.25, dataStats.max],
                [null, null],
                [1.5, dataStats.max],
                [1.5, dataStats.q3],
                [null, null],

                // Q3-Q1 box
                [1, dataStats.q3],
                [2, dataStats.q3],
                [2, dataStats.q1],
                [1, dataStats.q1],
                [1, dataStats.q3],
                [null, null],
              ],
              color: colorScheme.graph1,
              lines: {
                show: true,
              },
            },
            { // median
              data: [
                [1, dataStats.q2],
                [2, dataStats.q2],
                [null, null],
              ],
              color: colorScheme.graph2,
              lines: {
                show: true,
              },
            },
            { // bars
              data: [
                // 5% tile
                [1.5 - 0.15, dataStats.pct5],
                [1.5 + 0.15, dataStats.pct5],
                [null, null],
                // 95% tile
                [1.5 - 0.15, dataStats.pct95],
                [1.5 + 0.15, dataStats.pct95],
                [null, null],
                // 5% - Q1
                [1.5, dataStats.pct5],
                [1.5, dataStats.q1],
                [null, null],
                // 95% - Q3
                [1.5, dataStats.pct95],
                [1.5, dataStats.q3],
                [null, null],
              ],
              color: colorScheme._gray.black,
              lines: {
                show: true,
              },
            },
            { // avg
              data: [
                [1.5, dataStats.avg],
              ],
              points: {
                show: true,
                symbol: 'cross',
                radius: 8,
              },
              color: colorScheme._accent.brown,
            },
          ];

        if (dataStats && dataStats.stddev) {
          data.push({
            // stddev
            data: [
              // vertical line avg±1σ
              [2.4, dataStats.avg - dataStats.stddev],
              [2.4, dataStats.avg + dataStats.stddev],
              [null, null],
              // horizontal line avg - 1σ
              [2.4 - 0.075, dataStats.avg - dataStats.stddev],
              [2.4 + 0.075, dataStats.avg - dataStats.stddev],
              [null, null],
              // horizontal line avg + 1σ
              [2.4 - 0.075, dataStats.avg + dataStats.stddev],
              [2.4 + 0.075, dataStats.avg + dataStats.stddev],
              [null, null],
              // horizontal line avg
              [2.4 - 0.05, dataStats.avg],
              [2.4 + 0.05, dataStats.avg],
              [null, null],
            ],
            color: colorScheme._accent.sky,
            lines: {
              show: true,
            },
          });
        }

        $.plot($plot, data, {
          yaxis: {
            min: -0.5,
            minTickCount: 1,
          },
          xaxis: {
            min: 0,
            max: 3,
            show: false,
          },
          shadowSize: 0,
        });
      }, 10);
    };

    $modal.on('show.bs.modal', () => {
      $('.modal-title', $modal).text(dispData.title);
    });

    $modal.on('shown.bs.modal', () => {
      redraw();
    });

    $modal.on('hide.bs.modal', () => {
      dataStats = null;
      dispData = null;
      redraw();
    });

    $(window).resize(() => {
      redraw();
    });
  });
})(jQuery);
