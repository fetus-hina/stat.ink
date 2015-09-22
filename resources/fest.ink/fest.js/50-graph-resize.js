// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    var $graph = $('.rate-graph');
    if ($graph.length < 1) {
        return;
    }

    var goldenRatio = (1 + Math.sqrt(5)) / 2;
    var onResize = function () {
        $graph.each(function () {
            var $this = $(this);
            $this.height(
                Math.min(
                    500,
                    Math.max(
                        Math.round($this.width() / goldenRatio),
                        50
                    )
                )
            );
        });
    };

    // ウィンドウのリサイズが発生したら高さ調整処理を走らせる
    // そのまま処理すると処理が爆発するので一定時間ためて止まった頃にやる
    var timerId = null;
    $(window).resize(function () {
        if (timerId !== null) {
            window.clearTimeout(timerId);
        }
        timerId = window.setTimeout(onResize, 25);
    });

    // 初回高さ調整
    window.setTimeout(onResize, 1);
});
