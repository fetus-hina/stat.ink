/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function(window) {
    "use strict";
    // http://wikiwiki.jp/splatoon2ch/?%A5%AE%A5%A2%A5%D1%A5%EF%A1%BC%B8%A1%BE%DA
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

    window.getInkRecoveryTime = function (mainCount, subCount) {
        var x = mainCount * 10 + subCount * 3;
        var y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 75;
        var z = 100 / (180 * (1 - y));
        var f = Math.ceil(100 / z);
        return f / 60;
    };

    // defaultTime
    //      5: bubbler
    //      6: kraken
    //      12: echolocator
    window.getSpecialDuration = function (defaultTime, mainCount, subCount) {
        var x = mainCount * 10 + subCount * 3;
        return (1 + x) * defaultTime;
    };

    // frame:
    //      22: burst bomb
    //      33: splat & suction bomb
    //      38: seeker
    //      64: inkzooka
    window.getSpecialCount = function (frame, mainCount, subCount) {
        var f = window.getSpecialDuration(6, mainCount, subCount) * 60;
        return Math.ceil(f / frame);
    };
})(window);
