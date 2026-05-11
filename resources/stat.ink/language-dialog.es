//! Copyright (C) 2015-2020 AIZAWA Hina

jQuery($ => {
  $('a.language-change').click(function () {
    const $this = $(this);
    window.statinkFetch('/user/language', {
      method: 'POST',
      data: {
        language: $this.attr('data-lang')
      }
    })
      .catch(() => {})
      .finally(() => {
        window.location.reload();
      });
  });

  $('a.language-change-machine-translation').click(function () {
    const $this = $(this);
    window.statinkFetch('/user/machine-translation', {
      method: 'POST',
      data: {
        direction: $this.attr('data-direction')
      }
    })
      .catch(() => {})
      .finally(() => {
        window.location.reload();
      });
  });
});
