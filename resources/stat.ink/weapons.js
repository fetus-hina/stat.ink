($ => {
  function update() {
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
  }

  let timerId = null;
  $(window).resize(() => {
    if (timerId !== null) {
      window.clearTimeout(timerId);
    }
    timerId = window.setTimeout(() => {
      update();
    }, 33);
  }).resize();

  $('#stack-trends').click(() => {
    $(window).resize();
  });
})(jQuery);
