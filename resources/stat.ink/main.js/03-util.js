// Copyright (C) 2015 AIZAWA Hina | MIT License
window.fest = {
    getFestId: function () {
        return $('.container[data-fest]').attr('data-fest');
    },
    isFestPage: function () {
        return !!window.fest.getFestId();
    },
    numberFormat: function (number) {
        // http://d.hatena.ne.jp/mtoyoshi/20090321/1237723345
        return number.toString().replace(
            /([\d]+?)(?=(?:\d{3})+$)/g,
            function (t) {
                return t + ',';
            }
        );
    },
    dateTimeFormat: function (date) {
        var zeroPadding = function (num) {
            num = ~~num;
            return (num > 9 ? '' : '0') + num;
        };
        return date.getFullYear() + '-' +
            zeroPadding(date.getMonth() + 1) + '-' +
            zeroPadding(date.getDate()) + ' ' +
            zeroPadding(date.getHours()) + ':' +
            zeroPadding(date.getMinutes()) + ' ' +
            date.getTimezoneAbbreviation();
    },
    getTimeBasedScaler: function () { // {{{
        var scaledScaler = function (value, time) { // {{{
            var scaleMap = [
                1.0000, 1.0000, 1.0000, 0.7778, // 00:00 - 01:30 JST
                0.5556, 0.3333, 0.1296, 0.2315, // 02:00
                0.0278, 0.0000, 0.0000, 0.0000, // 04:00
                0.0833, 0.1667, 0.1667, 0.1667, // 06:00
                0.1667, 0.2083, 0.2500, 0.2917, // 08:00
                0.3333, 0.3333, 0.3333, 0.3333, // 10:00
                0.3333, 0.3556, 0.3778, 0.4000, // 12:00
                0.4222, 0.4444, 0.4444, 0.4444, // 14:00
                0.4444, 0.3819, 0.4028, 0.4236, // 16:00
                0.3611, 0.2917, 0.2222, 0.2222, // 18:00
                0.2222, 0.3333, 0.4444, 0.5556, // 20:00
                0.6667, 0.7778, 0.8889, 1.0000, // 22:00 - 23:30 JST
            ];

            // 最大値(1.0000)に対してscaleMapの0.0000は実際にはどれだけ試合があったと想定するか
            var minScale = 0.2000; // 最小の時間帯は最大の時間帯のn%の試合数と想定

            // 時間関係
            var timeInDay = (time + 32400) % 86400; // 32400 = 9時間, 日本時間のずれ(日本時間00:00を0としたい)
            var timeIndex1 = Math.floor(timeInDay / 1800); // scaleMap の index。30分ごと。
            var timeIndex2 = (timeIndex1 + 1) % 48;
            var scaleOffset = (timeInDay % 1800) / 1800;

            var scale1 = scaleMap[timeIndex1] * (1 - minScale) + minScale;
            var scale2 = scaleMap[timeIndex2] * (1 - minScale) + minScale;

            // scale1 と scale2 の間を線形補間して scaleOffset の位置に相当する値(minScale～1.0000)
            var scale = (scale1 * (1 - scaleOffset)) + (scale2 * scaleOffset);

            // 適当に10倍して計算する
            return Math.round(value * 10 * scale);
        }; // }}}
        var asIsScaler = function (value/*, time*/) {
            return value;
        };
        return fest.conf.useGraphScale.get()
            ? scaledScaler
            : asIsScaler;
    }, // }}}
    getGraphOptions: function (term, teams) { // {{{
        var defaultInks = { alpha: 'd9435f', bravo: '5cb85c' };
        var useInkColor = window.fest.conf.useInkColor.get();
        return {
            series: {
                stack: window.fest.conf.graphType.get() === "stack",
                lines: {
                    show: true,
                    fill: true,
                    steps: false
                }
            },
            xaxis: {
                mode: "time",
                minTickSize: [30, "minute"],
                timeformat: "%H:%M",
                twelveHourClock: false,
                timezone: window.fest.conf.timezone.get(),
                min: term.begin * 1000,
                max: term.end * 1000
            },
            yaxis: {
                min: 0,
                max: 100
            },
            colors: [
                '#' + (useInkColor ? teams.alpha.ink : defaultInks.alpha),
                '#' + (useInkColor ? teams.bravo.ink : defaultInks.bravo)
            ]
        };
    }, // }}}
};
