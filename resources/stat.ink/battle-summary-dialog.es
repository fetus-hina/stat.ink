($ => {
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

        $dataFields.each((i, el) => {
          const $this = $(el);
          const key = $this.attr('data-key');
          $this.text((dispData && dispData[key]) ? dispData[key] : '');
        });

        const data = (!dispData)
          ? []
          : [
            {
              data: [
                // max
                [1, dataStats.max],
                [2, dataStats.max],
                [null, null],
                [1.5, dataStats.max],
                [1.5, dataStats.q3],
                [null, null],
                // Q3
                [1, dataStats.q3],
                [2, dataStats.q3],
                [null, null],
                // Q2 (median)
                [1, dataStats.q2],
                [2, dataStats.q2],
                [null, null],
                // Q1 line
                [1, dataStats.q1],
                [2, dataStats.q1],
                [null, null],
                // min
                [1, dataStats.min],
                [2, dataStats.min],
                [null, null],
                // max to Q3
                [1.5, dataStats.max],
                [1.5, dataStats.q3],
                [null, null],
                // Q1 to min
                [1.5, dataStats.q1],
                [1.5, dataStats.min],
                [null, null],
                // Q3 to Q1
                [1, dataStats.q3],
                [1, dataStats.q1],
                [null, null],
                [2, dataStats.q3],
                [2, dataStats.q1],
                [null, null],
              ],
              color: colorScheme.graph1,
              lines: {
                show: true,
              },
            },
            {
              data: [
                [1.5, dataStats.avg],
              ],
              points: {
                show: true,
                symbol: 'cross',
                radius: 5,
              },
              color: colorScheme.graph1,
            },
          ];

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
      $('.modal-title').text(dispData.title);
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
