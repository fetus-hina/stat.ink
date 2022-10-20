/*! Copyright (C) 2015-2022 AIZAWA Hina | MIT License */

($ => {
  const fire = () => {
    $('.auto-tooltip')
      .tooltip({
        container: 'body'
      });
  };

  $(() => fire());
  $(document).on('pjax:complete', () => fire());
})(jQuery);
