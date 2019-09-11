/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */
jQuery($ => {
  $(document).on('click', 'a[href][rel~="external"]', function () {
    window.open($(this).attr('href'));
    return false;
  });
});
