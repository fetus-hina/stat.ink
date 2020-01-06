jQuery($ => {
  const events = window.battleEvents.filter(v => v.type === 'objective' || v.type === 'splatzone');

  if (events.length > 0) {
    $('#enable-smoothing')
      .prop('disabled', false)
      .change(function () {
        $('#timeline').attr(
          'data-object-smoothing',
          $(this).prop('checked') ? 'enable' : 'disable'
        );
        $(window).resize(); // redraw
      })
      .change();
  }
});
