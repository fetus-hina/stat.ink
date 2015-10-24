window.statByMap = function () {
  var $stat = $('#stat');
  var make = function (json) {
    var $root = $('<div>').append($('<h2>').text(json.name));
    var maps = [];
    for (var i in json) {
      if (!json.hasOwnProperty(i)) {
        continue;
      }
      var map = json[i];
      var flotData = [
        { label: "Won", data: map.win },
        { label: "Lost", data: map.lose }
      ];

      var $map = $('<div>').addClass('col-xs-12 col-sm-6 col-md-4 col-lg-4')
        .append($('<h3>').text(map.name))
        .append($('<div>').addClass('pie-flot-container').attr('data-flot', JSON.stringify(flotData)));

      maps.push({
        "name": (map.name + ""),
        "dom": $map,
      });
    }
    maps.sort(function (a, b) {
      return a.name.localeCompare(b.name);
    });
    var $maps = $('<div>').addClass('row');
    for (var i = 0; i < maps.length; ++i) {
      $maps.append(maps[i].dom);
    }
    return $root.append($maps);
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
            },
          },
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

  var json = JSON.parse($stat.attr('data-json'));
  $stat.empty().append(make(json));
  window.setTimeout(function () { redrawFlot(); }, 1);

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
