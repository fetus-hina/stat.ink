($ => {
  function updateTrends() {
    // {{{
    const stack = !!$('#stack-trends').prop('checked');
    const formatDate = date => {
      function zero(n) {
        n = String(n);
        return (n.length== 1) ? "0" + n : n;
      }
      return date.getUTCFullYear() + "-" + zero(date.getUTCMonth()+1) + "-" + zero(date.getUTCDate());
    };
    const date2unixTime = d => ((new Date(String(d) + 'T00:00:00Z')).getTime());
    const $graphs = $('#graph-trends');
    const json = JSON.parse($('#trends-json').text());
    const data = [];
    for (let i = 0; i < json[0].weapons.length; ++i) {
      const weapon = json[0].weapons[i];
      data.push({
        label: json[0].weapons[i].name,
        data: json.map(week => [
          date2unixTime(week.date),
          week.weapons[i].pct
        ]),
      });
    }
    if (stack) {
      data.push({
        label: $graphs.attr('data-label-others'),
        data: json.map(week => [
          date2unixTime(week.date),
          week.others_pct
        ]),
        color: '#cccccc'
      });
    }
    $graphs.height($graphs.width() * 9 / 16);
    $graphs.each((i, el) => {
      const $graph = $(el);
      $.plot($graph, data, {
        xaxis:{
          mode:'time',
          minTickSize:[7,'day'],
          tickFormatter: v => formatDate(new Date(v)),
        },
        yaxis: {
          min: 0,
          max: stack ? 100 : undefined,
          tickFormatter: v => v.toFixed(1) + "%",
        },
        series: {
          stack: stack,
          points: {
            show: !stack,
          },
          lines: {
            show: true,
            fill: stack,
            steps: false,
          }
        },
        legend: {
          sorted: stack ? "reverse" : false,
          position: "nw",
          container: $('#graph-trends-legends'),
          noColumns: (() => {
            const width = $(window).width();
            if (width < 768) {
              return 1;
            } else if (width < 992) {
              return 2;
            } else if (width < 1200) {
              return 4;
            } else {
              return 5;
            }
          })()
        },
      });
      window.setTimeout(() => {
        const $labels = $('td.legendLabel', $('#graph-trends-legends'));
        $labels.width(
          Math.max.apply(null, $labels.map(function () {
            return $(this).width('').width();
          })) + 12
        );
      }, 1);
    });
    // }}}
  }

  function updateInkPerformance() {
    const $graphs = $('.graph-inkperformance');
    $graphs.height($graphs.width() * 9 / 16);

    const $tooltip = $('<span>').css({
      position: 'absolute',
      display: 'none',
      padding: '2px',
      backgroundColor: '#fff',
      opacity: '0.9',
      fontSize: '12px',
    }).appendTo('body');

    // $('#placeholder").bind("plothover", function (event, pos, item) {

    //     if ($("#enablePosition:checked").length > 0) {
    //     var str = "(" + pos.x.toFixed(2) + ", " + pos.y.toFixed(2) + ")";
    //     $("#hoverdata").text(str);
    //     }

    //     if ($("#enableTooltip:checked").length > 0) {
    //     if (item) {
    //     var x = item.datapoint[0].toFixed(2),
    //     y = item.datapoint[1].toFixed(2);

    //     $("#tooltip").html(item.series.label + " of " + x + " = " + y)
    //     .css({top: item.pageY+5, left: item.pageX+5})
    //     .fadeIn(200);
    //     } else {
    //     $("#tooltip").hide();
    //     }
    //     }
    //     });
    $graphs.each((i, el) => {
      const $graph = $(el);
      const source = JSON.parse($('#' + $graph.data('source')).text());

      $.plot(
        $graph,
        [
          {
            data: source,
            points: {
              symbol: "diamond",
            },
            color: window.colorScheme._accent.blue,
          },
        ],
        {
          // xaxis:{
          //   mode:'time',
          //   minTickSize:[7,'day'],
          //   tickFormatter: v => formatDate(new Date(v)),
          // },
          yaxis: {
            tickFormatter: v => v.toFixed(1) + "%",
          },
          series: {
            points: {
              show: true,
            },
            lines: {
              show: false,
              steps: false,
            }
          },
          grid: {
            hoverable: true,
            clickable: false,
          },
        }
      );

      $graph.bind('plothover', (ev, pos, item) => {
        if (!item) {
          $tooltip.hide();
          return;
        }
        const data = item.series.data[item.dataIndex];
        $tooltip
          .text(`${data[2]}, n=${data[3]}`)
          .css({
            top: item.pageY + 5,
            left: item.pageX + 5,
          })
          .show();
      });
    });
    // }}}
  }

  let timerId = null;
  $(window).resize(() => {
    if (timerId !== null) {
      window.clearTimeout(timerId);
    }
    timerId = window.setTimeout(() => {
      updateTrends();
      updateInkPerformance();
    }, 33);
  }).resize();

  $('#stack-trends').click(() => {
    $(window).resize();
  });
})(jQuery);
