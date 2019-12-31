jQuery($ => {
  function update() {
    function getIntlLocales() {
      const $html = $('html');
      const results = [];

      // 日本の元号と民国紀元への特別対応
      const primaryLang = String($html.attr('lang'));
      if (
        primaryLang.substr(0, 3) === 'ja-' ||
        primaryLang.substr(0, 3) === 'zh-'
      ) {
        const calendar = String($html.attr('data-calendar'));
        if (calendar === 'japanese' || calendar === 'roc') {
          results.push(`${primaryLang}-u-ca-${calendar}`);
        }
      }
      results.push(primaryLang);
      results.push('en-US');
      return results;
    }

    function formatDate(date) {
      const locales = getIntlLocales();
      const options = {
        timeZone: 'UTC',
      };

      if (locales[0].indexOf('-ca-japanese') !== -1) {
        options.era = 'narrow';
      }

      const fmt = new Intl.DateTimeFormat(locales, options);
      return fmt.format(date);
    }

    function date2unixTime(d) {
      return (new Date(d + 'T00:00:00Z')).getTime();
    }

    function percentFormat(value, digit) {
      const formatter = new Intl.NumberFormat(getIntlLocales(), {
        style: 'percent',
        minimumFractionDigits: digit,
        maximumFractionDigits: digit,
      });
      return formatter.format(value);
    }

    const $graphs = $('.graph.stat-use-pct');
    const $tooltip = $('<span>')
      .css({
        position: 'absolute',
        display: 'none',
        padding: '2px',
        backgroundColor: '#fff',
        opacity: 0.9,
        fontSize: '12px',
      })
      .appendTo('body');
    const json = JSON.parse($('#use-pct-json').text());
    const data = [
      {
        data: json.map(v => [date2unixTime(v.date), v.use_pct]),
        color: window.colorScheme.graph1,
      },
    ];
    $graphs.height($graphs.width() * 9 / 16);
    $graphs.each(function () {
      $.plot($(this), data, {
        xaxis: {
          mode: 'time',
          minTickSize: [7, 'day'],
          tickFormatter: v => {
            // console.log(new Date(v));
            // return 'a';
            return formatDate(new Date(v));
          },
        },
        yaxis: {
          min: 0,
          tickFormatter: v => percentFormat(v / 100, 2),
        },
        series: {
          points: {
            show: true,
          },
          lines: {
            show: true,
            fill: true,
            steps: false
          }
        },
        grid: {
          hoverable: true
        },
        legend: {
          show: false,
        },
      });
    });

    $graphs.on('plothover', function (event, pos, item) {
      if (item) {
        const date = item.datapoint[0];
        const pct = percentFormat(item.datapoint[1] / 100, 3);
        $tooltip
          .text(
            formatDate(new Date(date)) + ' - ' +
            formatDate(new Date(date + 6 * 86400000)) + ' : ' +
            pct
          )
          .css({
            top: item.pageY - 20,
            left: (item.pageX <= $(window).width() / 2)
              ? (item.pageX + 10)
              : (item.pageX - ($tooltip.width() + 10)),
          })
          .show();
      } else {
        $tooltip.hide();
      }
    });
  }

  let timerId = null;
  $(window).resize(() => {
    if (timerId !== null) {
      clearTimeout(timerId);
      timerId = null;
    }
    timerId = window.setTimeout(() => {
      timerId = null;
      update();
    }, 10);
  });
});
