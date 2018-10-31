/*! Copyright (C) 2018 AIZAWA Hina | MIT License */

window.workList = () => {
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
};
