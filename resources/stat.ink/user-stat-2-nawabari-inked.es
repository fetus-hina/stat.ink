/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
($ => {
  const color = window.colorScheme._accent.orange;
  
  $(() => {
    const strings = JSON.parse($('#json-strings').text());
    const data = JSON.parse($('#json-battles').text())
      .filter(v => (v.sta !== null && v.ink !== null))
      .map(v => [v.sta, v.ink]);

    const $containers = $('.stage-inked');

    $containers.each((i, el) => {
      const $this = $(el);
      const $graph = $('.stat-inked', $this);
      const stage = $graph.attr('data-filter');
      const filter = stage ? (value => value[0] === stage) : (value => true);
      const filteredData = data.filter(filter).map(v => v[1]);

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
              .map((v, i, arr) => [i - (arr.length - 1), v]),
            color: color,
            lines: {
              show: true,
              fill: true,
            },
            legend: {
              show: false,
            },
          },
        ];

        $.plot($this, data, {
          xaxis: {
            minTickSize: 1,
            tickFormatter: v => parseInt(v, 10),
          },
          yaxis: {
            min: 0,
            minTickSize: 50,
            tickFormatter: v => (v + 'p'),
          },
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
