((window, $) => {
  $(() => {
    $('a.theme-switcher').click(function () {
      const $this = $(this);
      $.post({
        url: '/api/internal/theme',
        data: {
          theme: $this.data('theme')
        },
        dataType: 'json'
      })
        .done(() => {
          window.location.reload();
        })
        .fail(() => {
          window.alert('Failed to switch theme.\nPlease retry later.');
        });
    });
  });
})(window, jQuery);
