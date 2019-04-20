//! Copyright (C) 2015-2019 AIZAWA Hina
((window, $) => {
  $(() => {
    $('a.language-change').click(function () {
      const $this = $(this);
      const ajaxParams = {
        method: 'POST',
        url: '/user/language',
        data: {
          language: $this.attr('data-lang'),
        },
      };
      $.ajax(ajaxParams)
        .always(() => {
          window.location.reload();
        });
    });
  });
})(window, jQuery);
