jQuery($ => {
  function createSummary() {
    function getAvg(key) {
      function numberFormat(value, digit) {
        const locales = [
          $('html').attr('lang'),
          'en-US',
        ];
        const formatter = new Intl.NumberFormat(locales, {
          minimumFractionDigits: digit,
          maximumFractionDigits: digit,
        });
        return formatter.format(value);
      }

      const totalBattles = window.kddata.reduce((prev, cur) => prev + cur.battle, 0);
      const totalValue = window.kddata.reduce((prev, cur) => prev + cur[key] * cur.battle, 0);
      return (totalBattles > 0)
        ? numberFormat(totalValue / totalBattles, 2)
        : 'N/A';
    }

    return {
      'kill-avg': getAvg('kill'),
      'death-avg': getAvg('death'),
    };
  }

  const summary = createSummary();
  $('.kd-summary').each(function () {
    const $this = $(this);
    const typeKey = $this.attr('data-type');
    $this.text(summary[typeKey]);
  });
});
