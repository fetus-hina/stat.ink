"use strict";

/*! Copyright (C) 2016 AIZAWA Hina | MIT License */

(function ($, global) {
  global.updateBlackOutHint = function (newConfig) {
    var check = function ($e) {
      $e.removeClass('fa-square-o').addClass('fa-check-square-o');
    };
    var uncheck = function ($e) {
      $e.addClass('fa-square-o').removeClass('fa-check-square-o');
    };

    $('#blackout-info .blackout-info-icon').each(function (i, e) {
      var $this = $(e);
      var mode = $this.attr('data-mode');
      var category = $this.attr('data-category');

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
          return;
      }
    });
  };
})(jQuery, window);
