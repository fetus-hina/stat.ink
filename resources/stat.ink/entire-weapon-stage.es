jQuery($ => {
  function update () {
    function htmlEscape (str) {
      return $('<div>').text(String(str)).html();
    }

    function intFormat (value) {
      const formatter = new Intl.NumberFormat(
        [$('html').attr('lang'), 'en-US']
      );
      return formatter.format(value);
    }

    function percentFormat (value, digit) {
      const formatter = new Intl.NumberFormat(
        [$('html').attr('lang'), 'en-US'],
        {
          style: 'percent',
          minimumFractionDigits: digit,
          maximumFractionDigits: digit
        }
      );
      return formatter.format(value);
    }

    const goldenRatio = (1.0 + Math.sqrt(5)) / 2.0;
    const $graphs = $('.graph.stat-map-wp');

    $graphs.height($graphs.width());
    $graphs.each(function () {
      const $graph = $(this);
      const jsonData = JSON.parse($graph.attr('data-data'));
      const data = [
        { label: 'Won', data: jsonData.win },
        { label: 'Lost', data: jsonData.battle - jsonData.win }
      ];
      $.plot($graph, data, {
        series: {
          pie: {
            show: true,
            radius: 1,
            label: {
              show: 'auto',
              radius: 1 / goldenRatio, // 0.618
              formatter: function (label, slice) {
                return $('<div>').append(
                  $('<div>').css({
                    fontSize: '1em',
                    lineHeight: '1.1em',
                    textAlign: 'center',
                    padding: '2px',
                    color: '#fff',
                    textShadow: '0px 0px 3px #000'
                  }).append(
                    $('<div>')
                      .addClass('nobr')
                      .text(
                        intFormat(slice.data[0][1]) +
                        ' / ' +
                        intFormat(Math.round(slice.data[0][1] / (slice.percent / 100)))
                      )
                  ).append(
                    htmlEscape(percentFormat(slice.percent / 100, 1))
                  )
                ).html();
              }
            }
          }
        },
        legend: {
          show: false
        },
        colors: [
          window.colorScheme.win,
          window.colorScheme.lose
        ]
      });
    });
  }

  let timerId = null;
  $(window).resize(() => {
    if (timerId !== null) {
      clearTimeout(timerId);
      timerId = null;
    }
    timerId = setTimeout(() => {
      timerId = null;
      update();
    }, 10);
  });
});
