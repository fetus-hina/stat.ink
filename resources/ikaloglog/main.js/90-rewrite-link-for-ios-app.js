// Copyright (C) 2015 AIZAWA Hina | MIT License
// 
// apple-mobile-web-app-capable が yes の時、JavaScript で制御されたリンクを踏むと
// スタンドアロンモードが継続するらしいので、同一オリジンの時は location.href で移動するようにする
if (window.navigator.standalone) {
    $(document).ready(function () {
        var getOrigin = function (href) {
            var match = (href + "").match(/^(https?):\/\/[^\/:]+(:\d+)?/i); // スキームからパスの直前まで
            if (!match) {
                return null;
            }
    
            var origin = match[0];
            if (!match[2]) { // ポートなし
                var scheme = match[1].toLowerCase();
                if (scheme === 'http') {
                    origin += ':80';
                } else if(scheme === 'https') {
                    origin += ':443';
                }
            }
            return origin;
        };
    
        var myOrigin = getOrigin(window.location.href);
        if (!myOrigin) {
            return;
        }
    
        $('a[href]').each(function() {
            var self = this;
            var $this = $(self);
            if ($this.attr('rel') || $this.attr('target')) {
                return;
            }
            var linkOrigin = getOrigin(self.href);
            if (linkOrigin && linkOrigin === myOrigin) {
                $this.click(function (e) {
                    e.preventDefault();
                    window.location.href = self.href;
                });
            }
        });
    });
}
