/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */
(($, bowser) => {
  $.fn.ieWarning = function () {
    const browser = bowser.parse(window.navigator.userAgent || '');
    this.each(function () {
      const $this = $(this);
      if (browser.browser && browser.browser.name === 'Internet Explorer') {
        $this.show();
      }
    });
    return this;
  };
})(jQuery, window.bowser);
