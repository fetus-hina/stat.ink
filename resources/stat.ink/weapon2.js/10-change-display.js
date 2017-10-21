($ => {
  $('#change-weapon').change(function() {
    const $select = $(this);
    const url = $select.attr('data-url').replace(
      'WEAPON_KEY',
      () => encodeURIComponent($select.val())
    );
    location.href = url;
  });
})(jQuery);
