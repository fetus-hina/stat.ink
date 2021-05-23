/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

((window, $) => {
  $(() => {
    const $stat = $('#stat');
    const make = (json, screenName, filter) => {
      const battlesUrl = rule => {
        const params = [];
        for (const k in filter) {
          if (String(k).match(/^filter\[/)) {
            params.push(encodeURIComponent(k) + '=' + encodeURIComponent(filter[k]));
          }
        }
        params.push(encodeURIComponent('filter[rule]') + '=' + encodeURIComponent(rule));
        return `/@${screenName}?${params.join('&')}`;
      };
      const $root = $('<div>').append($('<h2>').text(json.name));
      const rules = [];
      for (const i in json.rules) {
        if (!Object.prototype.hasOwnProperty.call(json.rules, i)) {
          continue;
        }
        const rule = json.rules[i];
        const flotData = [
          { label: 'Won', data: rule.win },
          { label: 'Lost', data: rule.lose }
        ];

        const $rule = $('<div>').addClass('col-xs-12 col-sm-6 col-md-4 col-lg-4')
          .append(
            $('<h3>').append(
              $('<a>', { href: battlesUrl(i) }).text(rule.name)
                .text(rule.name)
            )
          )
          .append(
            $('<div>')
              .addClass('pie-flot-container')
              .attr('data-flot', JSON.stringify(flotData))
              .attr('data-url', battlesUrl(i))
          );
        rules.push({
          name: (rule.name + ''),
          dom: $rule
        });
      }
      rules.sort((a, b) => a.name.localeCompare(b.name));
      const $rules = $('<div>').addClass('row');
      for (let j = 0; j < rules.length; ++j) {
        $rules.append(rules[j].dom);
      }
      return $root.append($rules);
    };

    const redrawFlot = () => {
      $('.pie-flot-container').each(function () {
        const $container = $(this);
        const data = JSON.parse($container.attr('data-flot'));
        $.plot($container, data, {
          series: {
            pie: {
              show: true,
              radius: 1,
              label: {
                show: 'auto',
                radius: 0.61803398875,
                formatter: (label, slice) => $('<div>')
                  .append(
                    $('<div>').css({
                      fontSize: '1em',
                      lineHeight: '1.1em',
                      textAlign: 'center',
                      padding: '2px',
                      color: '#fff',
                      textShadow: '0px 0px 3px #000'
                    }).append(
                      slice.data[0][1] + ' / ' +
                      Math.round(slice.data[0][1] / (slice.percent / 100)) // FIXME
                    ).append(
                      $('<br>')
                    ).append(
                      slice.percent.toFixed(1) + '%'
                    )
                  )
                  .html()
              }
            }
          },
          legend: {
            show: false
          },
          colors: [
            window.colorScheme.win,
            window.colorScheme.lose
          ],
          grid: {
            clickable: true
          }
        });
        $container.bind('plotclick', function () {
          window.location.href = $(this).attr('data-url');
        });
      });
    };

    (() => {
      const json = JSON.parse($stat.attr('data-json'));
      const filter = JSON.parse($stat.attr('data-filter'));
      const screenName = $stat.attr('data-screen-name');
      $stat.empty();
      if (json.regular) {
        $stat.append(make(json.regular, screenName, filter));
      }

      if (json.gachi) {
        $stat.append(make(json.gachi, screenName, filter));
      }

      if (!json.regular && !json.gachi) {
        $stat.append(
          $('<p>').text($stat.attr('data-no-data'))
        );
      } else {
        window.setTimeout(
          () => { redrawFlot(); },
          1
        );
      }
    })();

    let timerId = null;
    const onResize = () => {
      const $elem = $('.pie-flot-container');
      if ($elem.length) {
        $elem.height(Math.min($elem.width(), 200));
      }
    };
    $(window).resize(
      () => {
        if (timerId) {
          window.clearTimeout(timerId);
          timerId = null;
        }
        timerId = window.setTimeout(() => {
          timerId = null;
          onResize();
        }, 33);
      },
      33
    )
      .resize();
  });
})(window, jQuery);
