/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */
(($, bowser) => {
  const createIcon = options => {
    const info = bowser.parse(options.ua);
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

    switch (info.os.name) {
      case 'macOS':
        return make('apple');

      case 'Windows':
      case 'Windows Phone':
        return make('microsoft-windows');

      case 'Chrome OS':
        return make('chrome');

      case 'Android':
        return make('android-icon');

      case 'iOS':
        return make('ios');

      case 'Linux':
        return make('linux-tux');
    }
    return '';
  };

  $.fn.osIconWidget = function (options) {
    this.each((i, el) => {
      const $this = $(el);
      $this.empty().append(createIcon(options));
    });
    return this;
  };
})(jQuery, window.bowser);
