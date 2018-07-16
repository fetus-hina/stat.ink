/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
($ => {
  const colorInked = window.colorScheme._accent.orange;
  const colorWinPct = window.colorScheme._accent.blue;
  
  $(() => {
    const strings = JSON.parse($('#json-strings').text());
    const data = JSON.parse($('#json-battles').text())
      .filter(v => (v.sta !== null && v.ink !== null))
      .map(v => [v.sta, v.ink, v.win]);

    const $containers = $('.stage-inked');

    $containers.each((i, el) => {
      const $this = $(el);
      const $graph = $('.stat-inked', $this);
      const stage = $graph.attr('data-filter');
      const filter = stage ? (value => value[0] === stage) : (value => true);
      const filteredData = data.filter(filter).map(v => [v[1], v[2]]);

      $graph.attr('data-data', JSON.stringify(filteredData));
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

        const data = [
          {
            label: strings.inked.turfInked,
            data: JSON.parse($this.attr('data-data'))
              .map((v, i, arr) => [i - (arr.length - 1), v[0]]),
            color: colorInked,
            lines: {
              show: true,
              fill: true,
            },
            legend: {
              show: false,
            },
          },
        ];
        if ($this.attr('data-filter')) {
          const winPct = (json => {
            let battles = 0;
            let wins = 0;
            return json.map((v, i, arr) => {
              if (v[1] === true || v[1] === false) {
                ++battles;
                if (v[1]) {
                  ++wins;
                }
              }
              return [
                i - (arr.length - 1),
                battles > 0 ? (wins * 100.0 / battles) : null,
              ];
            });
          })(JSON.parse($this.attr('data-data')));

          data.push({
            label: strings.wp.entire,
            data: winPct,
            color: colorWinPct,
            yaxis: 2,
            lines: {
              show: true,
              fill: false,
            },
            legend: {
              show: false,
            },
          });
        }

        $.plot($this, data, {
          xaxis: {
            minTickSize: 1,
            tickFormatter: v => parseInt(v, 10),
          },
          yaxes: [
            {
              min: 0,
              minTickSize: 50,
              tickFormatter: v => (v + 'p'),
            },
            {
              min: 0,
              max: 100,
              minTickSize: 10,
              position: 'right',
              tickFormatter: v => (Number(v).toFixed(1) + '%'),
            },
          ],
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
