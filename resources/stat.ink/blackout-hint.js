'use strict';

/*! Copyright (C) 2016 AIZAWA Hina | MIT License */

(function ($, global) {
  global.updateBlackOutHint = function (newConfig, target_) {
    const target = target_ || '#blackout-info';
    const check = function ($e) {
      $e.removeClass('fa-square').addClass('fa-check-square');
    };
    const uncheck = function ($e) {
      $e.addClass('fa-square').removeClass('fa-check-square');
    };

    $(target + ' .blackout-info-icon').each(function (i, e) {
      const $this = $(e);
      const mode = $this.attr('data-mode');
      const category = $this.attr('data-category');

      // 自分はどんな設定でも塗らない
      if (category === 'user') {
        uncheck($this);
        return;
      }

      switch (newConfig) {
        // 塗らない
        case 'no':
          uncheck($this);
          return;

        // プラベでは塗らない
        case 'not-private':
          if (mode === 'private') {
            uncheck($this);
          } else {
            check($this);
          }
          return;

        // 友達は塗らない
        case 'not-friend':
          switch (mode) {
            case 'private':
              uncheck($this);
              break;

            case 'squad_3':
            case 'squad_4':
              if (category === 'bad-guys') {
                check($this);
                break;
              } else {
                uncheck($this);
                break;
              }

            default:
              check($this);
              break;
          }
          return;

        // 塗る
        case 'always':
          check($this);
      }
    });
  };
})(jQuery, window);
