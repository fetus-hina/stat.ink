window.statByMapRule = function () {
  var redrawFlot = function () {
    $('.pie-flot-container').each(function () {
      var $container = $(this);
      var data = JSON.parse($container.attr('data-flot'));
      var click_href = $container.attr('data-clink-href');
      if (data) {
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
                  ).html();
                },
              },
            },
          },
          legend: {
            show: false
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
          $container.bind('plotclick', function (event, pos, obj) {
            window.location.href = click_href;
          });
        }
      }
    });
  };

  $('.pie-flot-container').each(function () {
    var $elem = $(this);
    var json = JSON.parse($elem.attr('data-json'));
    if (json.win < 1 && json.lose < 1) {
      $elem.attr('data-flot', 'false');
    } else {
      var data = [
        {
          label: "Won",
          data: json.win
        },
        {
          label: "Lost",
          data: json.lose
        }
      ];
      $elem.attr('data-flot', JSON.stringify(data));
    }
  });
  window.setTimeout(function () { redrawFlot(); }, 1);

  var timerId = null;
  var onResize = function () {
    var $elem = $('.pie-flot-container');
    if ($elem.length) {
      $elem.height(Math.min($elem.width(), 200));
      redrawFlot();
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
