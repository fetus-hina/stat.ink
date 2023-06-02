/*! Copyright (C) 2015-2020 AIZAWA Hina | MIT License */

(($, moment) => {
  const decimalFormat = (value, digit) => {
    const locales = [
      $('html').attr('lang'),
      'en-US'
    ];
    const formatter = new Intl.NumberFormat(locales, {
      style: 'decimal',
      minimumFractionDigits: digit,
      maximumFractionDigits: digit
    });
    return formatter.format(value);
  };

  const timeFormat = unixtime => {
    const $html = $('html');
    const locale = $html.attr('lang');
    const timeZone = $('html').attr('data-timezone');
    return moment(unixtime).locale(locale).tz(timeZone).format('LT');
  };

  $.fn.leaguePowerHistory = function ($legend, translations, periodBeginTime, periodEndTime, currentPeriodMax, overallPeriodMax, historyData) {
    const options = {
      legend: {
        container: $legend,
        labelFormatter: label => `<span class="mr-2">${label}</span>`,
        noColumns: 4,
        show: true,
        sorted: 'reverse'
      },
      xaxis: {
        show: true,
        mode: 'time',
        min: periodBeginTime,
        max: periodEndTime,
        timeBase: 'milliseconds',
        autoScale: 'none',
        tickFormatter: value => timeFormat(Number(value))
      },
      yaxis: {
        minTickSize: 10,
        tickFormatter: value => decimalFormat(Number(value), 1)
      }
    };

    const makeData = (legend, list, color, lineWidth) => ({
      label: legend,
      color,
      data: list,
      lines: {
        show: true,
        lineWidth
      },
      points: {
        show: false
      }
    });

    const makeWinLose = (legend, list, onlyThisValue, color) => ({
      label: legend,
      color,
      data: list.map(value => [
        value.time,
        value.isWin === onlyThisValue
          ? (value.value || 1500)
          : null
      ]),
      lines: {
        show: false
      },
      points: {
        show: true
      }
    });

    const makePeak = (legend, value, color, lineWidth) => ({
      label: legend,
      color,
      data: [
        [periodBeginTime, value[0]],
        [periodEndTime, value[0]]
      ],
      lines: {
        show: true,
        lineWidth
      },
      points: {
        show: false
      },
      shadowSize: 0
    });

    $.plot(
      this,
      [
        overallPeriodMax
          ? makePeak(translations.highestEver, overallPeriodMax, window.colorScheme._gray.darkGray, 2)
          : null,
        currentPeriodMax
          ? makePeak(translations.highestCurrent, currentPeriodMax, window.colorScheme._accent.purple, 2)
          : null,
        makeData(
          translations.leaguePower,
          historyData.map(v => [
            v.time,
            v.value
          ]),
          window.colorScheme.graph1,
          3
        ),
        makeWinLose(
          translations.lose,
          historyData,
          false,
          window.colorScheme.lose
        ),
        makeWinLose(
          translations.win,
          historyData,
          true,
          window.colorScheme.win
        )
      ].filter(v => v !== null),
      options
    );

    return this;
  };
})(jQuery, window.moment);
