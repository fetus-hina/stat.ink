/*! Copyright (C) 2015-2018 AIZAWA Hina | MIT License */
($ => {
  $.fn.privateNote = function privateNote () {
    const $this = this;
    const $label = $('.fas', $this);
    $this
      .hover(
        () => {
          $label.removeClass('fa-lock').addClass('fa-unlock');
        },
        () => {
          $label.removeClass('fa-unlock').addClass('fa-lock');
        }
      )
      .click(() => {
        const $target = $($this.data('target'));
        $this.hide();
        $target.show();
      });
    return $this;
  };
})(jQuery);
