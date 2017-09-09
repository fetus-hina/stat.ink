(($, anon) => {
  $(() => {
    $('.anonymize').each((i, el) => {
      const $this = $(el);
      $this.empty().text(anon());
    });
  });
})(jQuery, sillyName);
