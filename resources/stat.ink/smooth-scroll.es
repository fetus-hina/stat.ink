/*! Copyright (C) 2015-2019 AIZAWA Hina / MIT License */
($ => {
  $(() => {
    $('a[href^="#"]')
      .not('[data-toggle="tab"]')
      .smoothScroll({
        offset: -60,
      });
  });
})(jQuery);
