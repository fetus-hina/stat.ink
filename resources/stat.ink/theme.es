((window, $) => {
  $(() => {
    $('a.theme-switcher').click(function () {
      const $this = $(this);
      window.statinkFetch('/api/internal/theme', {
        method: 'POST',
        data: {
          theme: $this.data('theme')
        }
      })
        .then(() => {
          window.location.reload();
        })
        .catch(() => {
          window.alert('Failed to switch theme.\nPlease retry later.');
        });
    });
  });
})(window, jQuery);
