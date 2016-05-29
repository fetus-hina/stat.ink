window.statByRule = function () {
  var $stat = $('#stat');
  var make = function (json, screen_name, filter) {
    var battlesUrl = (function(rule) {
      var params = [];
      for (var k in filter) {
        if ((k + '').match(/^filter\[/)) {
          params.push(
            encodeURIComponent(k) + '=' + encodeURIComponent(filter[k])
          );
        }
      }
      params.push(
        encodeURIComponent('filter[rule]') + '=' + encodeURIComponent(rule)
      );
      return '/u/' + screen_name + '?' + params.join('&');
    });
    var $root = $('<div>').append($('<h2>').text(json.name));
    var rules = [];
    for (var i in json.rules) {
      if (!json.rules.hasOwnProperty(i)) {
        continue;
      }
      var rule = json.rules[i];
      var flotData = [
        { label: "Won", data: rule.win },
        { label: "Lost", data: rule.lose }
      ];

      var $rule = $('<div>').addClass('col-xs-12 col-sm-6 col-md-4 col-lg-4')
        .append(
          $('<h3>').append(
            $('<a>', {'href': battlesUrl(i)}).text(rule.name)
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
        "name": (rule.name + ""),
        "dom": $rule,
      });
    }
    rules.sort(function (a, b) {
      return a.name.localeCompare(b.name);
    });
    var $rules = $('<div>').addClass('row');
    for (var i = 0; i < rules.length; ++i) {
      $rules.append(rules[i].dom);
    }
    return $root.append($rules);
  };

  var redrawFlot = function () {
    $('.pie-flot-container').each(function () {
      var $container = $(this);
      var data = JSON.parse($container.attr('data-flot'));
      $.plot($container, data, {
        series: {
          pie: {
            show: true,
            radius: 1,
            label: {
              show: "auto",
              radius: .618,
              formatter: function(label, slice) {
                return $('<div>').append(
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
                ).html();
              },
            }
          }
        },
        legend: {
          show: false
        },
        colors: [
          window.colorScheme.win,
          window.colorScheme.lose,
        ],
        grid: {
          clickable: true
        },
      });
      $container.bind('plotclick', function (event, pos, obj) {
        window.location.href = $(this).attr('data-url');
      });
    });
  };

  (function () {
    var json = JSON.parse($stat.attr('data-json'));
    var filter = JSON.parse($stat.attr('data-filter'));
    var screen_name = $stat.attr('data-screen-name');
    $stat.empty();
    if (json.regular) {
      $stat.append(make(json.regular, screen_name, filter));
    }
    if (json.gachi) {
      $stat.append(make(json.gachi, screen_name, filter));
    }
    if (!json.regular && !json.gachi) {
      $stat.append(
        $('<p>').text($stat.attr('data-no-data'))
      );
    } else {
      window.setTimeout(function () { redrawFlot(); }, 1);
    }
  })();

  var timerId = null;
  var onResize = function () {
    var $elem = $('.pie-flot-container');
    if ($elem.length) {
      $elem.height(Math.min($elem.width(), 200));
    }
  };
  window.setTimeout(onResize, 1);
  $(window).resize(function () {
    if (timerId) {
      window.clearTimeout(timerId);
    }
    window.setTimeout(function () {
      timerId = null;
      onResize();
    }, 33);
  });
};
