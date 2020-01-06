jQuery($ => {
  const $btn = $('#private-note-show');
  const $txt = $('#private-note');
  const $i = $('.fas', $btn);
  $btn
    .hover(
      () => {
        $i.removeClass('fa-lock').addClass('fa-unlock-alt');
      },
      () => {
        $i.removeClass('fa-unlock-alt').addClass('fa-lock');
      }
    )
    .click(() => {
      $btn.hide();
      $txt.removeClass('d-none');
    });
});
