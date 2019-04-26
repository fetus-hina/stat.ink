/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

((window, $) => {
  $(() => {
    const $stat = $('#stat');
    const make = function (json, screen_name, filter) {
      const battlesUrl = map => {
        const params = [];
        for (let k in filter) {
          if (String(k).match(/^filter\[/)) {
            params.push(encodeURIComponent(k) + '=' + encodeURIComponent(filter[k]));
          }
        }
        params.push(encodeURIComponent('filter[map]') + '=' + encodeURIComponent(map));
        return `/@${screen_name}/spl1?${params.join('&')}`;
      };
      const $root = $('<div>').append($('<h2>').text(json.name));
      const maps = [];
      for (let i in json) {
        if (!json.hasOwnProperty(i)) {
          continue;
        }
        const map = json[i];
        const flotData = [
          { label: 'Won', data: map.win },
          { label: 'Lost', data: map.lose }
        ];

        const $map = $('<div>').addClass('col-xs-12 col-sm-6 col-md-4 col-lg-4')
          .append(
            $('<h3>').append(
              $('<a>', {'href': battlesUrl(i)}).text(map.name)
            )
          )
          .append(
            $('<div>')
              .addClass('pie-flot-container')
              .attr('data-flot', JSON.stringify(flotData))
              .attr('data-url', battlesUrl(i))
          );

        maps.push({
          'name': (map.name + ''),
          'dom': $map,
        });
      }
      maps.sort((a, b) => a.name.localeCompare(b.name));
      const $maps = $('<div>').addClass('row');
      for (let j = 0; j < maps.length; ++j) {
        $maps.append(maps[j].dom);
      }
      return $root.append($maps);
    };

    const redrawFlot = function () {
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
                radius: .61803398875,
                formatter: (label, slice) => $('<div>').append(
                    $('<div>').css({
                      'fontSize': '1em',
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
            clickable: true
          },
        });
        $container.bind('plotclick', function () {
          window.location.href = $(this).attr('data-url');
        });
      });
    };

    const json = JSON.parse($stat.attr('data-json'));
    const filter = JSON.parse($stat.attr('data-filter'));
    $stat.empty().append(make(json, $stat.attr('data-screen-name'), filter));
    window.setTimeout(
      () => { redrawFlot(); },
      1
    );

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
      window.setTimeout(() => {
        timerId = null;
        onResize();
      }, 33);
    });
  });
})(window, jQuery);
