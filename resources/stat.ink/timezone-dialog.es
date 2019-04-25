/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

($ => {
  $(() => {
    $('a.timezone-change').click(function () {
      const $this = $(this);
      const ajaxOptions = {
        method: 'POST',
        url: '/user/timezone',
        data: {
          timezone: $this.attr('data-tz'),
        },
      };
      $.ajax(ajaxOptions)
        .always(() => {
          window.location.reload();
        });
    });
  });

  $.fn.timezoneDialog = function () {
    $('[data-toggle="collapse"]', this).each(function () {
      const $parent = $(this);
      const $icon = $('.fa-chevron-down', $parent);
      $($parent.data('target'))
        .on('hidden.bs.collapse', () => {
          $icon
            .removeClass('fa-chevron-up')
            .addClass('fa-chevron-down');
        })
        .on('shown.bs.collapse', () => {
          $icon
            .removeClass('fa-chevron-down')
            .addClass('fa-chevron-up');
        });
    });
  };
})(jQuery);
