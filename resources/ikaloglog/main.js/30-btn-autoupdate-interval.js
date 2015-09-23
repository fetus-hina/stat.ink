// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $menu = $('#dropdown-update-interval');
    $menu.parent().on('show.bs.dropdown', function () {
        var interval = window.fest.conf.updateInterval.get();
        $('.update-interval', $menu).each(function () {
            var $a = $(this);
            var $icon = $('.glyphicon', $a);
            var targetInterval = (~~$a.attr('data-interval')) * 1000;
            $icon.css(
                'color',
                (targetInterval === interval)
                    ? '#333'
                    : 'rgba(0,0,0,0)'
            );
        });
    });

    $('.update-interval', $menu).click(function () {
        var currentInterval = window.fest.conf.updateInterval.get();
        var targetInterval = (~~$(this).attr('data-interval')) * 1000;
        if (currentInterval === targetInterval) {
            return;
        }
        window.fest.conf.updateInterval.set(targetInterval);
        window.fest.conf.autoUpdate.set(true);
    });
});
