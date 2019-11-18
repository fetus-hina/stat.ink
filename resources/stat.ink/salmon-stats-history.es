($ => {
  $.fn.salmonStatsHistoryDialog = function () {
    const html = document.getElementsByTagName('html')[0];
    const locale = html.getAttribute('lang');
    const timezone = html.getAttribute('data-timezone') || 'UTC';
    const calendar = html.getAttribute('data-calendar');

    this.each(function () {
      const $this = $(this);
      const apiURL = $this.data('url');
      let data = null;

      const redrawGraph = ($graph) => {
        if (data && $graph.width() > 0 && $graph.height() > 0) {
          const attr = $graph.data('api');
          const type = $graph.data('type');
          const fmt = new Intl.NumberFormat(
            [
              locale,
              'en-US',
            ],
            {
              minimumFractionDigits: (type === 'total') ? 0 : 1,
              maximumFractionDigits: (type === 'total') ? 0 : 1,
            }
          );
          const dateFmt = new Intl.DateTimeFormat(
            [
              locale + (calendar ? ('-u-ca-' + calendar) : ''),
              locale,
              'en-US',
            ],
            {
              dateStyle: 'medium',
              timeZone: timezone,
            }
          );

          const flotData = [
            // series 1
            {
              data: data.map(row => [
                row.as_of.time * 1000,
                (!row[attr] || row['work_count'] < 1)
                  ? null
                  : Number(row[attr]) / (type === 'total' ? 1 : Number(row.work_count)),
              ]),
              color: window.colorScheme.graph1,
              lines: {
                show: true,
                fill: false,
              },
              points: {
                show: true,
              },
            },
          ];
          $.plot($graph, flotData, {
            xaxis: {
              mode: 'time',
              minTickSize: [1, 'day'],
              tickFormatter: v => dateFmt.format(v),
            },
            yaxis: {
              min: 0,
              minTickSize: type === 'total' ? 1 : 0.1,
              tickFormatter: v => fmt.format(v),
            },
          });
        }
      };

      $this.on('show.bs.modal', function () {
        if (data === null) {
          $.getJSON(apiURL)
            .done(json => {
              data = json;
              setTimeout(() => {
                $this.resize();
              }, 1);
            });
        }
      });

      $this.on('shown.bs.modal', function () {
        $this.resize();
      });

      // 表示領域のサイズが変わった時にグラフのサイズを適切に変更する
      $this.resize(function () {
        $('.salmon-stats-history-graph', $this).each(function () {
          const $graph = $(this);
          if ($graph.width() < 0) {
            return;
          }

          $graph.height(Math.ceil($graph.width() * 10 / 16));
          redrawGraph($graph);
        });
      });

      // 表示タブが切り替わった時、グラフのサイズを適切に変更する
      $('[data-toggle="tab"]', $this).on('shown.bs.tab', function () {
        $this.resize();
      });
    });

    return this;
  };
})(jQuery);
