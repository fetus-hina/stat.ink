/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
(($, bowser) => {
  const createIcon = options => {
    const info = bowser(options.ua);
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

    if (info.chrome || info.chromium) {
      return make('chrome');
    }
    if (info.firefox) {
      return make('firefox');
    }
    if (info.msie) {
      return make('internet-explorer_9-11');
    }
    if (info.msedge) {
      return make('edge');
    }
    if (info.opera) {
      return make('opera');
    }
    if (info.ios) {
      return make('safari-ios');
    }
    if (info.safari) {
      return make('safari');
    }
    if (info.samsungBrowser) {
      return make('samsung-internet');
    }
    if (info.silk) {
      return make('silk');
    }
    if (info.android) {
      return 'android';
    }
    return '';
  };

  $.fn.browserIconWidget = function (options) {
    this.each((i, el) => {
      const $this = $(el);
      $this.empty().append(createIcon(options));
    });
  };
})(jQuery, bowser.detect);
