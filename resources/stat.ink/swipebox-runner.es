/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
($ => {
  $(() => {
    $('.swipebox').swipebox();
    $(document.body)
      .on(
        'click touchend',
        '#swipebox-slider .current img',
        e => false
      )
      .on(
        'click touchend',
        '#swipebox-slider .current',
        e => {
          $('#swipebox-close')
            .trigger('click');
        }
      );
  });
})(jQuery);
