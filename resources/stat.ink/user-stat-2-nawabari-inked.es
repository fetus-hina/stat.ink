/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
($ => {
  const colorInked = window.colorScheme._accent.orange;
  const colorWinPct = window.colorScheme._accent.green;
  const colorKills = window.colorScheme._accent.red;
  const colorDeaths = window.colorScheme._accent.blue;

  $(() => {
    const strings = JSON.parse($('#json-strings').text());
    const data = JSON.parse($('#json-battles').text())
      .filter(v => (v.sta !== null));

    const $containers = $('.stage-inked');

    $containers.each((i, el) => {
      const $this = $(el);
      const $graph = $('.stat-inked', $this);
      const stage = $graph.attr('data-filter');
      const filter = stage ? value => value.sta === stage : () => true;
      const filteredData = data.filter(filter);

      $graph.data('data', filteredData);
      if (filteredData.length < 1) {
        $this.hide();
      }
    });

    let timerId = null;
    const updatePlot = () => {
      // {{{
      timerId = null;

      let needsUpdate = false;
      $containers.each((i, el) => {
        const $el = $(el);
        if ($el.is(':hidden')) {
          return;
        }

        const $this = $('.stat-inked', $el);
        if ($this.height() < 1) {
          needsUpdate = true;
          return;
        }

        const rawData = $this.data('data');
        const icons = {
          left1: '<span class="bi bi-chevron-left"></span>',
          left2: '<span class="bi bi-chevron-double-left"></span>',
          right1: '<span class="bi bi-chevron-right"></span>',
          right2: '<span class="bi bi-chevron-double-right"></span>'
        };

        const data = [
          {
            label: `${icons.left1} ${strings.inked.turfInked}`,
            data: rawData.map((v, i, arr) => [i - (arr.length - 1), v.ink]),
            color: colorInked,
            lines: {
              show: true,
              fill: true
            },
            legend: {
              show: false
            }
          }
        ];
        if ($this.attr('data-filter')) {
          data.push({
            label: `${icons.left2} ${strings.wp.entire}`,
            data: (json => {
              let battles = 0;
              let wins = 0;
              return json.map((v, i, arr) => {
                if (v.win === true || v.win === false) {
                  ++battles;
                  if (v.win) {
                    ++wins;
                  }
                }
                return [
                  i - (arr.length - 1),
                  battles > 0 ? (wins * 100.0 / battles) : null
                ];
              });
            })(rawData),
            color: colorWinPct,
            yaxis: 2,
            lines: {
              show: true,
              fill: false
            },
            legend: {
              show: false
            }
          });
          data.push({
            label: `${icons.right1} ${strings.stats.avgKill}`,
            data: (json => {
              let battles = 0;
              let total = 0;
              return json.map((v, i, arr) => {
                if (v.k !== null) {
                  ++battles;
                  total += v.k;
                }
                return [
                  i - (arr.length - 1),
                  battles > 0 ? (total / battles) : null
                ];
              });
            })(rawData),
            color: colorKills,
            yaxis: 3,
            lines: {
              show: true,
              fill: false
            },
            legend: {
              show: false
            }
          });
          data.push({
            label: `${icons.right1} ${strings.stats.avgDeath}`,
            data: (json => {
              let battles = 0;
              let total = 0;
              return json.map((v, i, arr) => {
                if (v.d !== null) {
                  ++battles;
                  total += v.d;
                }
                return [
                  i - (arr.length - 1),
                  battles > 0 ? (total / battles) : null
                ];
              });
            })(rawData),
            color: colorDeaths,
            yaxis: 3,
            lines: {
              show: true,
              fill: false
            },
            legend: {
              show: false
            }
          });
        }

        $.plot($this, data, {
          xaxis: {
            minTickSize: 1,
            tickFormatter: v => parseInt(v, 10)
          },
          yaxes: [
            {
              min: 0,
              minTickSize: 50,
              position: 'left',
              tickFormatter: v => (v + 'p')
            },
            {
              max: 100,
              min: 0,
              minTickSize: 10,
              position: 'left',
              tickFormatter: v => (Number(v).toFixed(1) + '%')
            },
            {
              min: 0,
              minTickSize: 1,
              position: 'right',
              tickFormatter: v => (v + 'x')
            }
          ],
          legend: {
            position: 'nw'
          }
        });
      });

      if (needsUpdate) {
        timerId = setTimeout(updatePlot, 20);
      }
      // }}}
    };

    $(window).resize(() => {
      if (timerId !== null) {
        clearTimeout(timerId);
      }
      timerId = setTimeout(updatePlot, 20);
    }).resize();
  });
})(jQuery);
