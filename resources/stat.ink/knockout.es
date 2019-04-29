($ => {
  const redrawFlot = () => {
    $('.pie-flot-container').each((i, el) => {
      const $container = $(el);
      const data = JSON.parse($container.attr('data-flot'));
      if (data) {
        $.plot($container, data, {
          series: {
            pie: {
              show: true,
              radius: 1,
              label: {
                show: 'auto',
                radius: .618,
                formatter: (label, slice) => {
                  return $('<div>').append(
                    $('<div>').css({
                      'fontSize': '0.8em',
                      'lineHeight': '1.1em',
                      'textAlign': 'center',
                      'padding': '2px',
                      'color': '#000',
                      'textShadow': '0px 0px 3px #fff',
                    }).append(
                      slice.data[0][1] + ' / ' +
                      Math.round(slice.data[0][1] / (slice.percent / 100)) // FIXME
                    ).append(
                      $('<br>')
                    ).append(
                      slice.percent.toFixed(1) + '%'
                    )
                  ).html();
                },
              },
            },
          },
          legend: {
            show: false
          },
          colors: [
            window.colorScheme.ko,
            window.colorScheme.time
          ]
        });
      }
    });
  };

  $('.pie-flot-container').each((i, el) => {
    const $elem = $(el);
    const json = JSON.parse($elem.attr('data-json'));
    if (json.ok < 1 && json.battle < 1) {
      $elem.attr('data-flot', 'false');
    } else {
      const data = [
        {
          label: 'KO',
          data: json.ko
        },
        {
          label: 'Time Up',
          data: json.battle - json.ko
        }
      ];
      $elem.attr('data-flot', JSON.stringify(data));
    }
  });
  window.setTimeout(() => { redrawFlot() }, 1);

  let timerId = null;
  const onResize = () => {
    const $elem = $('.pie-flot-container');
    if ($elem.length) {
      $elem.height(Math.min($elem.width(), 200));
    }
  };
  window.setTimeout(onResize, 1);
  $(window).resize(() => {
    if (timerId) {
      window.clearTimeout(timerId);
    }
    timerId = window.setTimeout(() => {
      timerId = null;
      onResize();
    }, 33);
  });
})(jQuery);
