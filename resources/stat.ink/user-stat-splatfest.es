/*! Copyright (C) 2015-2021 AIZAWA Hina | MIT License */
(function (document, $) {
  const html = document.documentElement;
  const luxon = window.luxon;
  const color = (orgColor, alpha) => $.Color(orgColor).alpha(alpha).toRgbaString();
  const dateFmt = (time) => {
    const dt = luxon.DateTime.fromSeconds(time, {
      zone: html.dataset.timezone,
      locale: html.dataset.lang,
      outputCalendar: html.dataset.calendar
    });
    return dt.toLocaleString(luxon.DateTime.DATETIME_SHORT);
  };
  const powerFmt = (value) => (new Intl.NumberFormat(html.dataset.lang)).format(value);

  $('.chart-festpower').each(function (i, container) {
    const canvas = container.appendChild(document.createElement('canvas'));
    const ctx = canvas.getContext('2d');
    const dataLabels = JSON.parse(container.dataset.labels);
    const dataValues = JSON.parse(container.dataset.values);
    const hasExactPower = dataValues
      .filter(battle => typeof battle.my === 'number' && battle.my >= 1)
      .reduce(() => true, false);
    const averagePower = (() => {
      const powers = dataValues
        .map(battle => (
          hasExactPower
            ? (typeof battle.my === 'number' && battle.my >= 1)
                ? battle.my
                : null
            : battle.good
        ))
        .filter(pwr => typeof pwr === 'number');
      return powers.length
        ? (powers.reduce((acc, cur) => acc + cur) / powers.length)
        : null;
    })();
    const data = {
      labels: dataValues.map(battle => dateFmt(battle.at)),
      datasets: [
        // Win
        {
          backgroundColor: color(window.colorScheme.win, 0.2),
          borderColor: color(window.colorScheme.win, 0.8),
          data: dataValues.map(battle => (battle.isWin === true)
            ? hasExactPower
              ? (typeof battle.my === 'number' && battle.my >= 1)
                  ? battle.my
                  : (averagePower || 2000)
              : battle.good
            : null
          ),
          fill: false,
          label: dataLabels.win,
          showLine: false,
          lineTension: 0,
          pointRadius: 4,
          pointBorderWidth: 3,
          pointBackgroundColor: 'rgba(255, 255, 255, 0.8)'
        },
        // Lose
        {
          backgroundColor: color(window.colorScheme.lose, 0.2),
          borderColor: color(window.colorScheme.lose, 0.8),
          data: dataValues.map(battle => (battle.isWin === false)
            ? hasExactPower
              ? (typeof battle.my === 'number' && battle.my >= 1)
                  ? battle.my
                  : (averagePower || 2000)
              : battle.good
            : null
          ),
          fill: false,
          label: dataLabels.lose,
          lineTension: 0,
          showLine: false,
          pointRadius: 4,
          pointBackgroundColor: 'rgba(255, 255, 255, 0.8)',
          pointBorderWidth: 3
        },
        // Splatfest Power
        {
          backgroundColor: color(window.colorScheme.graph1, 0.2),
          borderColor: color(window.colorScheme.graph1, 0.8),
          data: dataValues.map(battle => (typeof battle.my === 'number' && battle.my >= 1) ? battle.my : null),
          fill: true,
          label: dataLabels.festPower,
          lineTension: 0,
          pointRadius: 0
        },
        // Estimated Good Guys Power
        {
          backgroundColor: color(window.colorScheme._accent.sky, 0.2),
          borderColor: color(window.colorScheme._accent.sky, 0.8),
          borderWidth: 2,
          data: dataValues.map(battle => battle.good),
          fill: false,
          label: dataLabels.estimateGood,
          lineTension: 0,
          pointRadius: 0
        },
        // Estimated Bad Guys Power
        {
          backgroundColor: color(window.colorScheme._accent.pink, 0.2),
          borderColor: color(window.colorScheme._accent.pink, 0.8),
          borderWidth: 2,
          data: dataValues.map(battle => battle.bad),
          fill: false,
          label: dataLabels.estimateBad,
          lineTension: 0,
          pointRadius: 0
        }
      ]
    };

    const chart = new window.Chart(ctx, {
      type: 'line',
      data,
      options: {
        aspectRatio: 1.61803398875,
        layout: {
          padding: {
            right: 5
          }
        },
        scales: {
          xAxes: [
            {
              display: false
            }
          ],
          yAxes: [
            {
              ticks: {
                callback: (value) => powerFmt(value)
              }
            }
          ]
        }
      }
    });

    canvas.addEventListener('click', (ev) => {
      const elements = chart.getElementsAtEvent(ev);
      if (!elements.length) {
        return;
      }

      const element = elements[0];
      if (!element) {
        return;
      }

      const dataValue = dataValues[element._index];
      if (!dataValue) {
        return;
      }

      window.open(dataValue.url);
    });
  });
})(window.document, window.jQuery);
