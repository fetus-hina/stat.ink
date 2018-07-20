/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
($ => {
  const $containers = $('.graph');
  let jsonBattles = null;
  let jsonDataWP = null;
  let jsonDataStats = null;
  let ranked = false;
  let timerId = null;
  $(window).resize(() => {
    if (jsonBattles === null) {
      const $json = $('#json-battles');
      jsonBattles = JSON.parse($json.text());
      ranked = $json.attr('data-has-rank') === 'true';
    }
    if (timerId !== null) {
      clearTimeout(timerId);
    }
    timerId = setTimeout(() => {
      timerId = null;
      $containers.height($containers.width() * 9 / 16);

      if (jsonDataWP === null) {
        jsonDataWP = convertToWPData(jsonBattles, ranked);
      }

      if (jsonDataStats === null) {
        jsonDataStats = convertToStatsData(jsonBattles, ranked);
      }

      drawWPGraph($containers.filter('.stat-wp'), jsonDataWP);
      drawStatsGraph($containers.filter('.stat-stats'), jsonDataStats);
    }, 33);
  }).resize();
})(jQuery);
