/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function(window, $) {
    "use strict";

    var months = 4;

    var i18n = (function() {
        return {
            "ja-JP": {
                "months": [ "1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月" ],
                "itemName": [ "バトル", "バトル" ],
            },
            "en-US": {
                "months": [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
                "itemName": [ "battle", "battles" ],
            },
            "en-GB": {
                "months": [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
                "itemName": [ "battle", "battles" ],
            },
            "es-ES": {
                "months": [ "Ene.", "Feb.", "Mar.", "Abr.", "May.", "Jun.", "Jul.", "Ago.", "Sep.", "Oct.", "Nov.", "Dic." ],
                "itemName": [ "batalla", "batallas" ],
            },
            "es-MX": {
                "months": [ "Ene.", "Feb.", "Mar.", "Abr.", "May.", "Jun.", "Jul.", "Ago.", "Sep.", "Oct.", "Nov.", "Dic." ],
                "itemName": [ "batalla", "batallas" ],
            },
        }[$('html').attr('lang')];
    })();

    // 今日
    var today = (function() {
        var d = new Date();
        return new Date(d.getFullYear(), d.getMonth(), d.getDate());
    })();

    // カレンダーの表示開始日
    var start = new Date(
        today.getFullYear(),
        today.getMonth() - months + 1,
        1
    );

    $('.activity').each(function () {
        var $elem = $(this);
        var cal = new CalHeatMap();
        cal.init({
            itemSelector: $elem[0],
            domain: 'month',
            subDomain: 'day',
            range: months,
            start: start,
            weekStartOnMonday: false,
            cellSize: 8,
            cellPadding: 1,
            callRadius: 0,
            domainDynamicDimension: true,
            displayLegend: false,
            legendColors: ['#ededed', '#23527c'],
            domainLabelFormat: function (date) {
                return i18n.months[date.getMonth()];
            },
            itemName: i18n.itemName,
            subDomainDateFormat: "%Y-%m-%d",
            subDomainTitleFormat: {
                "empty": "{date}",
                "filled": "{date} : {count} {name}",
            },
            data: '/api/internal/activity?screen_name=' + encodeURIComponent($elem.attr('data-screen-name')),
            dataType: 'json',
            afterLoadData: function (json) {
                var ret = {};
                $.each(json, function () {
                    var timeStamp = Math.floor((new Date(this.date)).getTime() / 1000);
                    ret[timeStamp + ""] = this.battles;
                });
                return ret;
            },
            onClick: function (date, value) {
                var start = new Date();
                start.setUTCFullYear(date.getFullYear(), date.getMonth(), date.getDate());
                start.setUTCHours(2, 0, 0);

                var end = new Date();
                end.setUTCFullYear(start.getUTCFullYear(), start.getUTCMonth(), start.getUTCDate() + 1);
                end.setUTCHours(start.getUTCHours(), start.getUTCMinutes(), start.getUTCSeconds() - 1);

                var timeFormat = function (d) {
                    var zero = function (v) {
                        return v < 10 ? '0' + v : v;
                    };
                    return d.getUTCFullYear() + '-' +
                        zero(d.getUTCMonth() + 1) + '-' +
                        zero(d.getUTCDate()) + ' ' +
                        zero(d.getUTCHours()) + ':' +
                        zero(d.getUTCMinutes()) + ':' +
                        zero(d.getUTCSeconds());
                };

                window.location.href =
                    '/u/' + encodeURIComponent($elem.attr('data-screen-name')) + '?' +
                    'filter[term]=term&' +
                    'filter[term_from]=' + encodeURIComponent(timeFormat(start)) + '&' +
                    'filter[term_to]=' + encodeURIComponent(timeFormat(end)) + '&' +
                    'filter[timezone]=Etc/UTC';
            },
        });
    });
})(window, window.jQuery);
