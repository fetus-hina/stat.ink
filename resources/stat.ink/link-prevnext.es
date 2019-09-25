/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */
jQuery($ => {
  const $next = $('link[rel="next"]');
  const $prev = $('link[rel="prev"]');
  const $body = $('body');

  $(window).keydown(ev => {
    // do nothing if modal is opened
    if ($body.hasClass('modal-open')) {
      return;
    }
    if ($('.pswp').hasClass('pswp--open')) {
      return;
    }

    // 37: left
    // 39: right
    switch (ev.keyCode) {
      case 37:
        if ($prev.length) {
          location.href = $prev.attr('href');
          return false;
        }
        break;

      case 39:
        if ($next.length) {
          location.href = $next.attr('href');
          return false;
        }
        break;
    }
  });
});
