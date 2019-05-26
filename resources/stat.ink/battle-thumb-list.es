($ => {
  $('.battle-item-image').each((i, el) => {
    const $this = $(el);
    const fallback = $this.attr('data-fallback');
    if (fallback) {
      $this.css('background-image', `url(${fallback})`);
    }

    const src = $this.attr('data-src');
    if (src) {
      const img = new Image();
      img.src = src;
      img.onload = () => {
        $this.css('background-image', `url(${img.src})`);
      };
    }
  });
})(jQuery);
