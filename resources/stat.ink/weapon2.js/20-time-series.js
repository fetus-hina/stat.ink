($ => {
  const weekJson = JSON.parse($('#weekly-json').text());
  const $tooltip = $('<span>').css({
    position: 'absolute',
    display: 'none',
    padding: '2px',
    backgroundColor: '#fff',
    opacity: '0.9',
    fontSize: '12px',
  }).appendTo('body');
  const axis1 = '<span class="fas fa-fw fa-arrow-left"></span>';
  const axis2 = '<span class="fas fa-fw fa-arrow-right"></span>';
  const update = () => {
    const formatDate = date => { // {{{
      const zeroPad = number => {
        number = String(number);
        return (number.length > 1)
          ? number
          : ('0' + number);
      };
      return [
        date.getUTCFullYear(),
        zeroPad(date.getUTCMonth() + 1),
        zeroPad(date.getUTCDate()),
      ].join('-');
    }; // }}}
    const date2unixTime = d => (new Date(String(d) + 'T00:00:00Z')).getTime();

    const $graphs = $('.graph');
    $graphs.each((i, el) => {
      const $graph = $(el);
      $graph.height($graph.width() * 9 / 16);
    });

    // Use %, Win % {{{
    ($graphs => {
      $graphs.each((i, el) => {
        $.plot( // {{{
          $(el),
          [
            {
              'label': axis1 + $(el).attr('data-label-use-pct'),
              'data': weekJson.map(v => [
                date2unixTime(v.date),
                v.use_pct,
              ]),
              'color': window.colorScheme.graph1,
              'lines': {
                'fill': true,
              },
            },
            {
              'label': axis2 + $(el).attr('data-label-win-pct'),
              'data': weekJson.map(v => [
                date2unixTime(v.date),
                v.win_pct,
                v.win_pct_err * 2,
              ]),
              'color': window.colorScheme.graph2,
              'yaxis': 2,
              'points': {
                'errorbars': 'y',
                'yerr': {
                  'show': true,
                  'asymmetric': false,
                  'upperCap': '-',
                  'lowerCap': '-',
                  'color': window.colorScheme._gray.darkGray,
                },
              },
            },
          ],
          {
            'xaxis': {
              'mode': 'time',
              'minTickSize': [7, 'day'],
              'tickFormatter': v => formatDate(new Date(v)),
            },
            'yaxis': {
              'min': 0,
              'minTickSize': 1,
              'tickFormatter': v => v.toFixed(2) + '%',
            },
            'y2axis': {
              'min': 0,
              'max': 100,
              'minTickSize': 5,
              'tickFormatter': v => v.toFixed(0) + '%',
            },
            'series': {
              'points': {
                'show': true,
              },
              'lines': {
                'show': true,
                'steps': false,
              }
            },
            'grid': {
              'hoverable': true,
            },
            'legend': {
              'show': true,
              'position': 'nw',
            },
          }
        ); // }}}
        $(el).on('plothover', (event, pos, item) => { // {{{
          if (!item) {
            $tooltip.hide();
            return;
          }
          const date = item.datapoint[0];
          const pct = item.datapoint[1].toFixed(2) + '%';
          $tooltip
            .text(
              formatDate(new Date(date)) + '/' +
              formatDate(new Date(date + 6 * 86400000)) + ' : ' +
              pct
            )
            .css({
              'top': item.pageY - 20,
              'left': (item.pageX <= $(window).width() / 2)
                ? (item.pageX + 10)
                : (item.pageX - ($tooltip.width() + 10)),
            })
            .show();
        }); // }}}
      });
    })($graphs.filter('.stat-use-pct'));
    // }}}

    // Kills, Deaths, Specials and Inked {{{
    ($graphs => {
      $graphs.each((i, el) => {
        $.plot( // {{{
          $(el),
          [
            {
              'label': axis1 + $(el).attr('data-label-inked'),
              'data': weekJson.map(v => [
                date2unixTime(v.date),
                v.inked,
              ]),
              'color': window.colorScheme._accent.yellow,
              'lines': {
                'fill': true,
              },
            },
            {
              'label': axis2 + $(el).attr('data-label-kills'),
              'data': weekJson.map(v => [
                date2unixTime(v.date),
                v.kills,
              ]),
              'color': window.colorScheme._accent.blue,
              'yaxis': 2,
            },
            {
              'label': axis2 + $(el).attr('data-label-deaths'),
              'data': weekJson.map(v => [
                date2unixTime(v.date),
                v.deaths,
              ]),
              'color': window.colorScheme._accent.red,
              'yaxis': 2,
            },
            {
              'label': axis2 + $(el).attr('data-label-specials'),
              'data': weekJson.map(v => [
                date2unixTime(v.date),
                v.specials,
              ]),
              'color': window.colorScheme._accent.green,
              'yaxis': 2,
            },
          ],
          {
            'xaxis': {
              'mode': 'time',
              'minTickSize': [7, 'day'],
              'tickFormatter': v => formatDate(new Date(v)),
            },
            'yaxis': {
              'min': 0,
              'minTickSize': 100,
              'tickFormatter': v => v.toFixed(0) + 'p',
            },
            'y2axis': {
              'min': 0,
              'minTickSize': 1,
              'tickFormatter': v => v.toFixed(0) + '×',
            },
            'series': {
              'points': {
                'show': true,
              },
              'lines': {
                'show': true,
                'steps': false,
              }
            },
            'grid': {
              'hoverable': true,
            },
            'legend': {
              'show': true,
              'position': 'nw',
            },
          }
        ); // }}}
        $(el).on('plothover', (event, pos, item) => { // {{{
          if (!item) {
            $tooltip.hide();
            return;
          }
          const date = item.datapoint[0];
          const label = [
            $(el).attr('data-label-inked'),
            $(el).attr('data-label-kills'),
            $(el).attr('data-label-deaths'),
            $(el).attr('data-label-specials'),
          ][item.seriesIndex];
          const value = label + '=' + (
            (item.seriesIndex == 0)
              ? (item.datapoint[1].toFixed(1) + 'p')
              : item.datapoint[1].toFixed(3)
          );
          $tooltip
            .text(
              formatDate(new Date(date)) + '/' +
              formatDate(new Date(date + 6 * 86400000)) + ' : ' +
              value
            )
            .css({
              'top': item.pageY - 20,
              'left': (item.pageX <= $(window).width() / 2)
                ? (item.pageX + 10)
                : (item.pageX - ($tooltip.width() + 10)),
            })
            .show();
        }); // }}}
      });
    })($graphs.filter('.stat-kd-sp-inked'));
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
