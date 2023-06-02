((window, $) => {
  $(() => {
    const updateTrends = () => {
      // {{{
      const stack = !!$('#stack-trends').prop('checked');
      const formatDate = date => {
        function zero (n) {
          n = String(n);
          return (n.length === 1) ? '0' + n : n;
        }
        return date.getUTCFullYear() + '-' + zero(date.getUTCMonth() + 1) + '-' + zero(date.getUTCDate());
      };
      const date2unixTime = d => ((new Date(String(d) + 'T00:00:00Z')).getTime());
      const $graphs = $('#graph-trends');
      const json = JSON.parse($('#trends-json').text());
      const data = [];
      for (let i = 0; i < json[0].weapons.length; ++i) {
        data.push({
          label: json[0].weapons[i].name,
          data: json.map(week => [
            date2unixTime(week.date),
            week.weapons[i].pct
          ])
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
          xaxis: {
            mode: 'time',
            minTickSize: [7, 'day'],
            tickFormatter: v => formatDate(new Date(v))
          },
          yaxis: {
            min: 0,
            max: stack ? 100 : undefined,
            tickFormatter: v => v.toFixed(1) + '%'
          },
          series: {
            stack,
            points: {
              show: !stack
            },
            lines: {
              show: true,
              fill: stack,
              steps: false
            }
          },
          legend: {
            sorted: stack ? 'reverse' : false,
            position: 'nw',
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
          }
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
    };

    const updateInkPerformance = () => {
      const $graphs = $('.graph-inkperformance');
      $graphs.height($graphs.width() * 9 / 16);

      const $tooltip = $('<span>').css({
        position: 'absolute',
        display: 'none',
        padding: '2px',
        backgroundColor: '#fff',
        opacity: '0.9',
        fontSize: '12px'
      }).appendTo('body');

      const calcCoefficients = (arrayA, arrayB) => {
        // 算術平均の計算
        const calcAverage = array => (array.reduce((val1, val2) => (val1 + val2)) / array.length);

        // 偏差の計算
        const calcDeviation = array => {
          const average = calcAverage(array);
          return array.map(value => value - average);
        };

        // 標準偏差の計算
        const calcStdDev = deviationArray => {
          return Math.sqrt(
            deviationArray
              .map(dev => dev * dev)
              .reduce((val1, val2) => val1 + val2) / deviationArray.length
          );
        };

        // 共分散の計算
        const calcCovariance = (devArrayA, devArrayB) => {
          return devArrayA
            .map((valA, i) => valA * devArrayB[i])
            .reduce((val1, val2) => (val1 + val2)) / devArrayA.length;
        };

        const calcCorrelationCoefficient = (devArrayA, devArrayB, stdDevA, stdDevB) => {
          const covariance = calcCovariance(devArrayA, devArrayB);
          return covariance / (stdDevA * stdDevB);
        };

        if (arrayA.length < 1 || arrayA.length !== arrayB.length) {
          return null;
        }

        const devArrayA = calcDeviation(arrayA);
        const devArrayB = calcDeviation(arrayB);
        const stdDevA = calcStdDev(devArrayA);
        const stdDevB = calcStdDev(devArrayB);
        if (stdDevA === 0 || stdDevB === 0) {
          return null;
        }
        const correlationCoefficient = calcCorrelationCoefficient(devArrayA, devArrayB, stdDevA, stdDevB);
        const regressionCoefficient = correlationCoefficient * (stdDevB / stdDevA);
        const intercept = calcAverage(arrayB) - (regressionCoefficient * calcAverage(arrayA)); // 切片

        return {
          stdDevA,
          stdDevB,
          correlationCoefficient,
          regressionCoefficient,
          intercept
        };
      };

      $graphs.each((i, el) => {
        const $graph = $(el);
        let data = $graph.data('data');
        if (!data) {
          const source = JSON.parse($('#' + $graph.data('source')).text());
          data = [
            {
              data: source.map(item => [item[0], item[1], `${item[2]}, n=${item[3]}`]),
              points: {
                symbol: 'diamond'
              },
              color: window.colorScheme._accent.blue
            }
          ];

          // 相関があれば回帰直線を表示
          const coefficients = calcCoefficients(
            source.map(weapon => weapon[0]),
            source.map(weapon => weapon[1])
          );
          if (coefficients && Math.abs(coefficients.correlationCoefficient) >= 0.2) {
            const line = (() => {
              const minX = Math.min.apply(null, source.map(weapon => weapon[0]));
              const maxX = Math.max.apply(null, source.map(weapon => weapon[0]));
              const calcValue = x => coefficients.intercept + (x * coefficients.regressionCoefficient);
              const label = $graph.data('labelCorrelationCoefficient') + ' = ' + coefficients.correlationCoefficient.toFixed(3);

              return {
                data: [
                  [minX, calcValue(minX), label],
                  [maxX, calcValue(maxX), label]
                ],
                lines: {
                  show: true
                },
                points: {
                  show: false
                },
                color: window.colorScheme._accent.orange
              };
            })();
            data.unshift(line);
          }

          $graph.data('data', data);
        }

        $.plot($graph, data, {
          yaxis: {
            tickFormatter: v => v.toFixed(1) + '%'
          },
          series: {
            points: {
              show: true
            },
            lines: {
              show: false,
              steps: false
            }
          },
          grid: {
            hoverable: true,
            clickable: false
          }
        });

        $graph.bind('plothover', (ev, pos, item) => {
          if (!item) {
            $tooltip.hide();
            return;
          }
          const data = item.series.data[item.dataIndex];
          if (!data || !data[2]) {
            $tooltip.hide();
            return;
          }
          $tooltip
            .text(data[2])
            .css({
              top: item.pageY + 5,
              left: item.pageX + 5
            })
            .show();
        });
      });
      // }}}
    };

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
  });
})(window, jQuery);
