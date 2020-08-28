//! Copyright (C) 2015-2020 AIZAWA Hina

jQuery($ => {
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

  $('a.language-change-machine-translation').click(function () {
    const $this = $(this);
    const ajaxParams = {
      method: 'POST',
      url: '/user/machine-translation',
      data: {
        direction: $this.attr('data-direction'),
      },
    };
    $.ajax(ajaxParams)
      .always(() => {
        window.location.reload();
      });
  });
});
