// Copyright (C) 2015 AIZAWA Hina | MIT License
(function () {
    var localStorage = window.localStorage;
    var isEventFiredMyself = false; // IEでは自分自身でイベントが起動する
    var eventTestValue = (function () {
        var r = '00000000' + Math.floor(Math.random() * 0x100000000).toString(16);
        return r.substr(r.length - 8, 8);
    })();
    var save = function (key, value) { // {{{
        if (!localStorage) {
            return;
        }
        localStorage.setItem(key, value);
    }; // }}}
    var loadBoolean = function (key, trueValue, falseValue, defaultValue) { // {{{
        if (!localStorage) {
            return defaultValue;
        }
        switch (localStorage.getItem(key)) {
            case trueValue:
                return true;

            case falseValue:
                return false;

            default:
                return defaultValue;
        }
    }; // }}}
    var loadInteger = function (key, defaultValue, checker) { // {{{
        if (!localStorage) {
            return defaultValue;
        }
        var value = localStorage.getItem(key);
        if ((value + '').match(/^\d+$/)) {
            value = ~~value;
            if (!checker || checker.call(window, value)) {
                return value;
            }
        }
        return defaultValue;
    }; // }}}
    var loadStringIn = function (key, defaultValue, values) { // {{{
        if (!localStorage) {
            return defaultValue;
        }
        var value = localStorage.getItem(key) + '';
        if (values.indexOf(value) >= 0) {
            return value;
        }
        return defaultValue;
    }; // }}}

    window.fest.conf = {
        hasStorage: !!localStorage,
        autoUpdate: { // {{{
            get: function () {
                return loadBoolean('autoupdate', 'enabled', 'disabled', true);
            },
            set: function (isEnabled) {
                save('autoupdate', isEnabled ? 'enabled' : 'disabled');
                isEventFiredMyself || $('#event').trigger('updateConfigAutoUpdate');
            },
        }, // }}}
        updateInterval: { // {{{
            get: function () {
                return loadInteger('update-interval', 10 * 60 * 1000); // 10min
            },
            set: function (value) {
                save('update-interval', (~~value) + "");
                isEventFiredMyself || $('#event').trigger('updateConfigUpdateInterval');
            },
        }, // }}}
        graphType: { // {{{
            get: function () {
                return loadStringIn('graph-type', 'stack', ['stack', 'overlay']);
            },
            set: function (value) {
                save('graph-type', value === 'stack' ? 'stack' : 'overlay');
                isEventFiredMyself || $('#event').trigger('updateConfigGraphType');
            },
        }, // }}}
        useInkColor: { // {{{
            get: function () {
                return loadBoolean('graph-ink', 'use', 'not use', true);
            },
            set: function (isUse) {
                save('graph-ink', isUse ? 'use' : 'not use');
                isEventFiredMyself || $('#event').trigger('updateConfigGraphInk');
            },
        }, // }}}
        useGraphScale: { // {{{
            get: function () {
                return loadBoolean('graph-scale', 'use', 'not use', false);
            },
            set: function (isUse) {
                save('graph-scale', isUse ? 'use' : 'not use');
                isEventFiredMyself || $('#event').trigger('updateConfigGraphScale');
            },
        }, // }}}
        timezone: { // {{{
            get: function () {
                return $('meta[name=timezone]').attr('content');
            },
            set: function (tz) {
                $('#event').trigger('updateConfigTimezone', tz);
            },
        }, // }}}
    };

    $(document).ready(function () {
        var map = {
            'autoupdate':       'updateConfigAutoUpdate',
            'update-interval':  'updateConfigUpdateInterval',
            'graph-type':       'updateConfigGraphType',
            'graph-ink':        'updateConfigGraphInk',
            'graph-scale':      'updateConfigGraphScale',
        };
        $(window).on('storage', function ($ev) {
            var ev = $ev.originalEvent;
            var $event = $('#event');
            if (ev.key === null) { // clear all
                for (var i in map) {
                    if (map.hasOwnProperty(i)) {
                        $event.trigger(map[i]);
                    }
                }
            } else if (map[ev.key] && map.hasOwnProperty(ev.key)) {
                $event.trigger(map[ev.key]);
            } else if (ev.key === 'event-test' && ev.newValue === eventTestValue) {
                // 自分自身にイベントが飛んでくるタイプのブラウザ
                isEventFiredMyself = true;
            }
        });

        // 自分自身にイベントが飛んでくるブラウザかテストする
        save('event-test', eventTestValue);
    });
})();
