window.statByRule = function () {
  var $stat = $('#stat');
  var make = function (json) {
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
        .append($('<h3>').text(rule.name))
        .append($('<div>').addClass('pie-flot-container').attr('data-flot', JSON.stringify(flotData)));

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
      console.log(data);
      $.plot($container, data, {
        series: {
          pie: {
            show: true,
            radius: 1,
            label: {
              show: "auto",
              radius: .618,
              formatter: function(label, slice) {
                  console.log(slice);
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
          '#5cb85c',
          '#d9534f',
        ]
      });
    });
  };

  $.getJSON(
    '/api/internal/stat-by-rule?screen_name=' + encodeURIComponent($stat.attr('data-screen-name')),
    function (json) {
      $stat.empty();
      if (json.regular) {
        $stat.append(make(json.regular));
      }
      if (json.gachi) {
        $stat.append(make(json.gachi));
      }
      if (!json.regular && !json.gachi) {
        $stat.append(
          $('<p>').text($stat.attr('data-no-data'))
        );
      } else {
        window.setTimeout(function () { redrawFlot(); }, 1);
      }
      $('#loading').hide();
    }
  );

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
