/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

((window, $) => {
  $(() => {
    const redrawFlot = () => {
      $('.pie-flot-container').each(function () {
        const $container = $(this);
        const data = JSON.parse($container.attr('data-flot'));
        const click_href = $container.attr('data-click-href') || '';
        if (data) {
          $.plot($container, data, {
            series: {
              pie: {
                show: true,
                radius: 1,
                label: {
                  show: 'auto',
                  radius: .61803398875,
                  formatter: (label, slice) => $('<div>')
                    .append(
                      $('<div>').css({
                        'fontSize': '0.8em',
                        'lineHeight': '1.1em',
                        'textAlign': 'center',
                        'padding': '2px',
                        'color': '#fff',
                        'textShadow': '0px 0px 3px #000',
                      }).append(
                        slice.data[0][1] + ' / ' +
                        Math.round(slice.data[0][1] / (slice.percent / 100)) // FIXME
                      ).append(
                        $('<br>')
                      ).append(
                        slice.percent.toFixed(1) + '%'
                      )
                    )
                    .html(),
                },
              },
            },
            legend: {
              show: false,
            },
            colors: [
              window.colorScheme.win,
              window.colorScheme.lose,
            ],
            grid: {
              clickable: click_href != '',
            },
          });
          if (click_href != '') {
            $container.bind('plotclick', () => {
              window.location.href = click_href;
            });
          }
        }
      });
    };

    $('.pie-flot-container').each(function () {
      const $elem = $(this);
      const json = JSON.parse($elem.attr('data-json'));
      if (json.win < 1 && json.lose < 1) {
        $elem.attr('data-flot', 'false');
      } else {
        const data = [
          {
            label: 'Won',
            data: json.win
          },
          {
            label: 'Lost',
            data: json.lose
          }
        ];
        $elem.attr('data-flot', JSON.stringify(data));
      }
    });

    let timerId = null;
    const onResize = () => {
      const $elem = $('.pie-flot-container');
      if ($elem.length) {
        $elem.height(Math.min($elem.width(), 200));
        redrawFlot();
      }
    };
    $(window).resize(() => {
      if (timerId) {
        window.clearTimeout(timerId);
      }
      timerId = window.setTimeout(() => {
        timerId = null;
        onResize();
      }, 33);
    }).resize();
  });
})(window, jQuery);
