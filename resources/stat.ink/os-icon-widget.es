/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
(($, bowser) => {
  const createIcon = options => {
    const info = bowser(options.ua);
    // const base = options.logos;
    // const make = key => {
    //   const $img = $('<img>', {
    //     src: `${base}/${key}.png`,
    //   });
    //   return $img.css({
    //     height: options.size,
    //     width: 'auto',
    //   });
    // };

    if (info.mac) {
      return 'OSX';
    }
    if (info.windows) {
      return 'Windows';
    }
    if (info.windowsphone) {
      return 'WinPhone';
    }
    if (info.chromeos) {
      return 'CrOS';
    }
    if (info.android) {
      return 'Android';
    }
    if (info.ios) {
      return 'iOS';
    }
    if (info.linux) {
      return 'Linux';
    }
    return '';
  };

  $.fn.osIconWidget = function (options) {
    this.each((i, el) => {
      const $this = $(el);
      $this.empty().append(createIcon(options));
    });
  };
})(jQuery, bowser.detect);
