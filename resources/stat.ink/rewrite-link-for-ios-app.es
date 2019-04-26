/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

// apple-mobile-web-app-capable が yes の時、JavaScript で制御されたリンクを踏むと
// スタンドアロンモードが継続するらしいので、同一オリジンの時は location.href で移動するようにする
if (window.navigator.standalone) {
  ($ => {
    $(() => {
      function getOrigin(href) {
        const match = (href + '').match(/^(https?):\/\/[^\/:]+(:\d+)?/i); // スキームからパスの直前まで
        if (!match) {
          return null;
        }
      
        let origin = match[0];
        if (!match[2]) { // ポートなし
          const scheme = match[1].toLowerCase();
          if (scheme === 'http') {
            origin += ':80';
          } else if(scheme === 'https') {
            origin += ':443';
          }
        }
        return origin;
      }
      
      const myOrigin = getOrigin(window.location.href);
      if (!myOrigin) {
        return;
      }
      
      $('a[href]').each(function () {
        const self = this;
        const $this = $(self);
        if ($this.attr('rel') || $this.attr('target')) {
          return;
        }
        const linkOrigin = getOrigin(self.href);
        if (linkOrigin && linkOrigin === myOrigin) {
          $this.click(e => {
            e.preventDefault();
            window.location.href = self.href;
          });
        }
      });
    });
  })(jQuery);
}
