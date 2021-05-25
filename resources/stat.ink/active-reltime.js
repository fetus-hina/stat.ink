/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function ($, window, document) {
  'use strict';
  $(document).ready(function () {
    const formats = window.reltimeFormats;
    window.setInterval(function () {
      const now = Math.floor((new Date()) / 1000);
      const $targets = $('span.active-reltime');
      if ($targets.length < 1) {
        return;
      }
      $targets.each(function () {
        const $this = $(this);
        const time = parseInt($this.attr('data-time'), 10);
        const mode = $this.attr('data-mode');
        if (isNaN(time)) {
          return;
        }
        const reltime = (function (ago) {
          if (ago >= 31536000) {
            return ['year', Math.floor(ago / 31536000)];
          } else if (ago >= 2592000) {
            return ['mon', Math.floor(ago / 2592000)];
          } else if (ago >= 86400) {
            return ['day', Math.floor(ago / 86400)];
          } else if (ago >= 3600) {
            return ['hour', Math.floor(ago / 3600)];
          } else if (ago >= 60) {
            return ['min', Math.floor(ago / 60)];
          } else if (ago >= 15) {
            return ['sec', ago];
          } else {
            return ['now', ago];
          }
        })(now - time);
        $this.text(
          reltime[1] === 1
            ? formats[mode].one[reltime[0]]
            : formats[mode].many[reltime[0]].replace('42', reltime[1])
        );
      });
    }, 5000);
  });
})(jQuery, window, window.document);
