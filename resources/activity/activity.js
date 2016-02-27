(function(window, $) {
    "use strict";
    var messages = {
        en: {
            daysOfWeek: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            months: ['Jan', 'Fev', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        ja: {
            daysOfWeek: ['月', '火', '水', '木', '金', '土', '日'],
            months: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
        },
    };
    $('.activity').each(function () {
        var $elem = $(this);
        var screen_name = $elem.attr('data-screen-name');
        if ((screen_name + "").length > 0) {
            $.get('/api/internal/activity', {'screen_name': screen_name}, function (json) {
                var data = [];
                $.each(json, function () {
                    var day = this;
                    var match = (day.date + "").match(/^(\d+)-(\d+)-(\d+)$/);
                    if (match) {
                        data.push({
                            'date': new Date(
                                ~~(match[1]),
                                ~~(match[2]) - 1,
                                ~~(match[3])
                            ),
                            'value': ~~day.battles,
                        });
                    }
                });
                $elem.gammacalendar(data, {
                    weeks: Math.min(53, Math.ceil(((new Date()) - (new Date(2015, 9 - 1, 27))) / (7 * 86400 * 1000))),
                    i18n: messages[
                        ($('html').attr('lang').match(/^([a-z]+)-/))[1].toLowerCase()
                    ],
                    startOnSunday: true,
                    highlightToday: false,
                    baseColor: {
                        r: 0x1e,
                        g: 0x68,
                        b: 0x23
                    }
                });
            });
        }
    });
})(window, window.jQuery);
