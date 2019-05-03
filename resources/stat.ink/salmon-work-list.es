/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

((window, $) => {
  $(() => {
    let lastPeriodId = null;
    $('.battle-row').each(function() {
      const $row = $(this);

      if ($row.attr('data-period') === lastPeriodId) {
        return;
      }

      if (lastPeriodId !== null) {
        $row.css('border-top', '2px solid grey');
      }

      lastPeriodId = $row.attr('data-period');
    });
  });
})(window, jQuery);
