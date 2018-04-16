/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
($ => {
  const $containers = $('.graph');
  let jsonBattles = null;
  let jsonDataWP = null;
  let jsonDataStats = null;
  let timerId = null;
  $(window).resize(() => {
    if (jsonBattles === null) {
      jsonBattles = JSON.parse($('#json-battles').text());
    }
    if (timerId !== null) {
      clearTimeout(timerId);
    }
    timerId = setTimeout(() => {
      timerId = null;
      $containers.height($containers.width() * 9 / 16);

      if (jsonDataWP === null) {
        jsonDataWP = convertToWPData(jsonBattles);
      }

      if (jsonDataStats === null) {
        jsonDataStats = convertToStatsData(jsonBattles);
      }

      drawWPGraph($containers.filter('.stat-wp'), jsonDataWP);
      drawStatsGraph($containers.filter('.stat-stats'), jsonDataStats);
    }, 33);
  }).resize();
})(jQuery);
