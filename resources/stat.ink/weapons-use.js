/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
($ => {
  let stack = false;
  function update () {
    const formatDate = function (date) {
      const zero = function (n) {
        n = String(n);
        return n.length === 1 ? '0' + n : n;
      };
      return date.getUTCFullYear() + '-' + zero(date.getUTCMonth() + 1) + '-' + zero(date.getUTCDate());
    };

    const date2unixTime = function (d) {
      return new Date(d + 'T00:00:00Z').getTime();
    };

    const $graphs = $('#graph-trends');
    $graphs.height($graphs.width() * 9 / 16);
    $graphs.each(function () {
      const $graph = $(this);
      const $legends = $graph.attr('data-legends')
        ? $('#' + $graph.attr('data-legends'))
        : null;
      const legendColumns = (function () {
        const width = $(window).width();
        if (!$legends) {
          return 1;
        }
        if (width < 768) { // xs
          return 1;
        } else if (width < 992) { // sm
          return 2;
        } else if (width < 1200) { // md
          return 4;
        } else { // lg
          return 5;
        }
      })();
      const json = JSON.parse($('#' + $graph.attr('data-refs')).text());
      const data = [];
      $.each(json.data, function () {
        data.push({
          label: this.legend,
          data: this.data.map(function (row) {
            return [
              date2unixTime(row[0]),
              row[1]
            ];
          })
        });
      });
      const size = Math.max(18, Math.ceil($graph.height() * 0.075));
      data.push({
        icons: {
          show: true,
          tooltip: function (x, $this, userData) {
            if (typeof userData === 'string') {
              $this
                .attr('title', userData)
                .tooltip({ container: 'body' })
                .css('opacity', '0.4');
            }
          }
        },
        data: json.events.map(function (item) {
          return [
            $graph
              .attr('data-icon')
              .replace(
                /\/dummy\.png.*/,
                function () {
                  return '/' + item[2] + '.png';
                }
              ),
            date2unixTime(item[0]),
            size,
            size,
            item[1]
          ];
        })
      });
      $.plot($graph, data, {
        xaxis: {
          mode: 'time',
          minTickSize: [7, 'day'],
          tickFormatter: function (v) {
            return formatDate(new Date(v));
          }
        },
        yaxis: {
          min: 0,
          tickFormatter: function (v) {
            return v.toFixed(1) + '%';
          }
        },
        series: {
          stack,
          points: {
            show: !stack
          },
          lines: {
            show: true,
            fill: stack,
            steps: false
          }
        },
        legend: {
          sorted: stack ? 'reverse' : false,
          position: 'nw',
          container: $legends,
          noColumns: legendColumns
        },
        grid: {
          markingsLineWidth: 2,
          markings: json.events.map(function (item) {
            const t = date2unixTime(item[0]);
            return {
              xaxis: { from: t, to: t },
              color: 'rgba(255,200,200,0.6)'
            };
          })
        }
      });

      if ($legends) {
        window.setTimeout(function () {
          const $labels = $('td.legendLabel', $legends);
          $labels.width(
            Math.max.apply(null, $labels.map(function () {
              return $(this).width('').width();
            })) + 12
          );
        }, 1);
      }
    });
  }
  let timerId = null;
  $(window).resize(function () {
    if (timerId !== null) {
      window.clearTimeout(timerId);
    }
    timerId = window.setTimeout(function () {
      update();
    }, 33);
  }).resize();
  $('#stack-trends').click(function () {
    stack = !!$(this).prop('checked');
    $(window).resize();
  });
})(jQuery);
