/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
(($, bowser) => {
  const createIcon = options => {
    const info = bowser(options.ua);
    const base = options.logos;
    const make = key => {
      const $img = $('<img>', {
        src: `${base}/${key}.svg`,
      });
      return $img.css({
        height: options.size,
        width: 'auto',
      });
    };

    if (info.mac) {
      return make('macosx');
    }
    if (info.windows || info.windowsphone) {
      return make('microsoft-windows');
    }
    if (info.chromeos) {
      return make('chrome');
    }
    if (info.android) {
      return make('android-icon');
    }
    if (info.ios) {
      return make('ios');
    }
    if (info.linux) {
      return make('linux-tux');
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
