/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */
(($, bowser) => {
  const createIcon = options => {
    const info = bowser.parse(options.ua);
    const base = options.logos;
    const make = key => {
      const $img = $('<img>', {
        src: `${base}/${key}.png`,
      });
      return $img.css({
        height: options.size,
        width: 'auto',
      });
    };

    switch (info.browser.name) {
      case 'Chrome':
      case 'Chromium':
        return make('chrome');

      case 'Firefox':
        return make('firefox');

      case 'Internet Explorer':
        return make('internet-explorer_9-11');

      case 'Microsoft Edge':
        return make('edge');

      case 'Opera':
        return make('opera');

      case 'Safari':
        return (info.os.name === 'iOS')
          ? make('safari-ios')
          : make('safari');

      case 'Samsung Internet for Android':
        return make('samsung-internet');

      case 'Amazon Silk':
        return make('silk');

      case 'Android Browser':
        return make('android');
    }

    return '';
  };

  $.fn.browserIconWidget = function (options) {
    this.each((i, el) => {
      const $this = $(el);
      $this.empty().append(createIcon(options));
    });
  };
})(jQuery, window.bowser);
