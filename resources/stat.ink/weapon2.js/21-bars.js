($ => {
  const update = () => {
    const $charts = $('.bar-flot-container');
    $charts.each((i, el) => {
      const $graph = $(el);
      $graph.height(Math.max(150, $graph.width() * 9 / 16));
    });

    // Kills, Deaths, Specials and Assisted {{{
    ($charts => {
      const formatValues = json => {
        const data = JSON.parse(json);
        const total = data.map(v => v.battles).reduce((v1, v2) => v1 + v2, 0);
        return data.map(v => [
          v.times - 0.5,
          v.battles * 100 / total,
        ]);
      };

      $charts.each((i, el) => {
        const $this = $(el);
        $.plot( // {{{
          $this,
          [
            {
              'data': formatValues($this.attr('data-json')),
              'color': window.colorScheme.graph1,
              'bars': {
                'show': true,
              },
            },
          ],
          {
            'xaxis': (type => { // {{{
              switch (type) {
                case 'inked':
                  return {
                    'min': 0,
                    'max': 1500,
                    'minTickSize': 100,
                  };
                default:
                  return {
                    'min': -0.5,
                    'max': 20.5,
                    'minTickSize': 1,
                  };
              }
            })($this.attr('data-type')), // }}}
            'yaxis': {
              'min': 0,
              'minTickSize': 1,
              'tickFormatter': value => value.toFixed(1) + '%',
            },
            'grid': {
              'hoverable': true,
            },
            'legend': {
              'show': false,
            },
          }
        ); // }}}
      });
    })($charts.filter((i, el) => {
      switch ($(el).attr('data-type')) {
        case 'kill':
        case 'death':
        case 'special':
        case 'assist':
          return true;

        default:
          return false;
      }
    }));
    // }}}
  };

  // Update all graph data on resized a window, or init phase.
  let timerId = null;
  $(window).resize(() => {
    if (timerId !== null) {
      clearTimeout(timerId);
    }
    timerId = setTimeout(() => {
      update();
    }, 33);
  }).resize();
})(jQuery);
