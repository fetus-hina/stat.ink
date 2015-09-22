// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    var $timezoneMenu = $('#timezone-list');
    if ($timezoneMenu.length < 1) {
        return;
    }
    $timezoneMenu.parent().on(
        'show.bs.dropdown',
        function () {
            var $li = $('li', $timezoneMenu);
            var onDisplay = function () {
                var currentTimezone = window.fest.conf.timezone.get();
                $('.glyphicon-ok', $timezoneMenu).each(function () {
                    var $this = $(this);
                    $this.css(
                        'color',
                        $this.parent().attr('data-timezone') === currentTimezone
                            ? '#333'
                            : 'rgba(0,0,0,0)'
                    );
                });
            };

            var changeTimezone = function (zone) { // {{{
                if ($('meta[name=timezone]').attr('content') === zone) {
                    return;
                }

                $.ajax({
                    url: '/timezone/set',
                    type: 'POST',
                    data: { zone: zone },
                    dataType: 'json',
                    headers: {
                        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content'),
                    },
                    error: function () {
                        window.location.reload();
                    },
                    success: function () {
                        window.location.reload();
                    },
                });
            }; // }}}

            if ($li.length > 0) {
                onDisplay();
                return;
            }

            // 読み込み開始
            $timezoneMenu.append(
                $('<li>').css('text-align', 'center').append(
                    $('<span>').addClass('fa fa-spin fa-refresh')
                )
            );

            $.getJSON(
                '/timezone.json',
                { '_': Math.floor(new Date() / 1000) },
                function (zones) { // {{{
                    var currentArea = null;
                    var $currentArea = null;
                    var currentInitial = null;
                    var $currentInitial = null;
                    $timezoneMenu.empty().append(
                        $('<li>').append(
                            $('<a>', {'href': 'javascript:;', 'data-timezone': 'Asia/Tokyo'}).append(
                                $('<span>').addClass('glyphicon glyphicon-ok')
                            ).append(
                                ' 日本時間'
                            ).click(function () {
                                changeTimezone('Asia/Tokyo')
                            })
                        )
                    ).append(
                        $('<li>').addClass('divider')
                    );
                    for (var i = 0; i < zones.length; ++i) {
                        var match = zones[i].id.match(/^([^\/]+)\/((.).*)$/);
                        if (match) {
                            // "Asia"
                            if (currentArea !== match[1]) {
                                currentArea = match[1];
                                currentInitial = null;
                                $currentInitial = null;
                                $currentArea = $('<ul>').addClass('dropdown-menu');
                                $timezoneMenu.append(
                                    $('<li>').addClass('dropdown-submenu').append(
                                        $('<a>', {'href': 'javascript:;', 'data-toggle': 'dropdown'}).text(
                                            currentArea
                                        )
                                    ).append(
                                        $currentArea
                                    )
                                );
                            }

                            // Asia/"T"okyo
                            if (currentInitial !== match[3]) {
                                currentInitial = match[3];
                                $currentInitial = $('<ul>').addClass('dropdown-menu');
                                $currentArea.append(
                                    $('<li>').addClass('dropdown-submenu').append(
                                        $('<a>', {'href': 'javascript:;', 'data-toggle': 'dropdown'}).text(
                                            currentInitial
                                        )
                                    ).append(
                                        $currentInitial
                                    )
                                );
                            }


                            // "Asia"->"T"->"Asia/Tokyo"
                            $currentInitial.append(
                                $('<li>').append(
                                    $('<a>', {'href': 'javascript:;', 'data-timezone': match[0]}).append(
                                        $('<span>').addClass('glyphicon glyphicon-ok')
                                    ).append(
                                        ' ' + match[0]
                                    ).click(function () {
                                        changeTimezone($(this).attr('data-timezone'));
                                    })
                                )
                            );
                        }
                    }
                    onDisplay();
                } // }}}
            );
        }
    );
});
