/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function(window) {
    "use strict";
    // http://wikiwiki.jp/splatoon2ch/?%A5%AE%A5%A2%A5%D1%A5%EF%A1%BC%B8%A1%BE%DA#ha588ca0
    // reason = "oob" | "drown" | "fall" | other
    window.getRespawnTime = function (reason, mainCount, subCount) {
        switch (reason) {
            case 'oob':
            case 'fall':
            case 'drawn':
                return (function() {
                    var x = Math.max(mainCount * 10 + subCount * 3 - 12, 0);
                    var y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 30;
                    var f = (1 - y) * 180 + 120 + (reason === 'drawn' ? 120 : 30);
                    return f / 60;
                })();
            default:
                return (function() {
                    var x = mainCount * 10 + subCount * 3;
                    var y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 45;
                    var f = (1 - y) * 360 + 30 + 120;
                    return f / 60;
                })();
        }
    };
})(window);
