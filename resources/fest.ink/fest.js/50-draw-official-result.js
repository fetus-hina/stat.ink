// Copyright (C) 2015 AIZAWA Hina | MIT License
$(document).ready(function () {
    if (!window.fest.isFestPage()) {
        return;
    }
    var $root = $('#official-result');
    var $container = $('#official-result-container');
    if (!$root || !$container) {
        return;
    }

    var makeDomTree = function (json) { // {{{
        $container.empty().append(
            $('<div>', {'data-team': 'alpha'}).addClass('official-result-team-bg')
        ).append(
            $('<div>', {'data-team': 'bravo'}).addClass('official-result-team-bg')
        ).append(
            $('<div>', {'id': 'official-result-label-vote'}).addClass('official-result-label').text('とくひょうりつ')
        ).append(
            $('<div>', {'id': 'official-result-label-win'}).addClass('official-result-label').text('しょうりつ')
        ).append(
            $('<div>', {'id': 'official-result-label-total'}).addClass('official-result-label').text('-')
        ).append(
            $('<div>', {'data-team': 'alpha'}).addClass('official-result-team').append(
                $('<div>').addClass('result-team').text(json.teams.alpha.name)
            ).append(
                $('<div>').addClass('result-data result-vote').append(
                    $('<span>').addClass('result-number').text(json.result.vote.alpha)
                ).append(
                    $('<span>').addClass('result-percent').text('%')
                ).append(
                    $('<span>').addClass('result-multiply').text('(x' + json.result.vote.multiply + ')').css({'display': json.result.vote.multiply > 1 ? 'inline' : 'none'})
                )
            ).append(
                $('<div>').addClass('result-data result-win').append(
                    $('<span>').addClass('result-number').text(json.result.win.alpha)
                ).append(
                    $('<span>').addClass('result-percent').text('%')
                ).append(
                    $('<span>').addClass('result-multiply').text('(x' + json.result.win.multiply + ')').css({'display': json.result.win.multiply > 1 ? 'inline' : 'none'})
                )
            ).append(
                $('<div>').addClass('result-data result-total').append(
                    $('<span>').addClass('result-number').text(
                        json.result.vote.alpha * json.result.vote.multiply + json.result.win.alpha * json.result.win.multiply
                    )
                )
            )
        ).append(
            $('<div>', {'data-team': 'bravo'}).addClass('official-result-team').append(
                $('<div>').addClass('result-team').text(json.teams.bravo.name)
            ).append(
                $('<div>').addClass('result-data result-vote').append(
                    $('<span>').addClass('result-number').text(json.result.vote.bravo)
                ).append(
                    $('<span>').addClass('result-percent').text('%')
                ).append(
                    $('<span>').addClass('result-multiply').text('(x' + json.result.vote.multiply + ')').css({'display': json.result.vote.multiply > 1 ? 'inline' : 'none'})
                )
            ).append(
                $('<div>').addClass('result-data result-win').append(
                    $('<span>').addClass('result-number').text(json.result.win.bravo)
                ).append(
                    $('<span>').addClass('result-percent').text('%')
                ).append(
                    $('<span>').addClass('result-multiply').text('(x' + json.result.win.multiply + ')').css({'display': json.result.win.multiply > 1 ? 'inline' : 'none'})
                )
            ).append(
                $('<div>').addClass('result-data result-total').append(
                    $('<span>').addClass('result-number').text(
                        json.result.vote.bravo * json.result.vote.multiply + json.result.win.bravo * json.result.win.multiply
                    )
                )
            )
        );

        if (json.teams.alpha.ink) {
            $('.official-result-team-bg[data-team="alpha"]').css({
                'background': '#' + json.teams.alpha.ink,
            });
        }
        if (json.teams.bravo.ink) {
            $('.official-result-team-bg[data-team="bravo"]').css({
                'background': '#' + json.teams.bravo.ink,
            });
        }
    }; // }}}

    var onResize = function () { // {{{
        var containerWidth = ~~$container.width();
        if (~~$container.attr('data-width') === containerWidth) {
            // サイズ変更なし
            return;
        }
        $container.attr('data-width', containerWidth);
        var containerHeight     = $container.width() * 9 / 16;
        var voteLabelTop        = containerHeight * 0.24845;
        var winLabelTop         = containerHeight * 0.49068;
        var totalLabelTop       = containerHeight * 0.73292;
        var voteWinLabelHeight  = containerHeight * 0.18634;
        var totalLabelHeight    = containerHeight * 0.26708;
        var voteWinFontSize     = containerHeight * 0.13043;
        var totalFontSize       = containerHeight * 0.21739;

        $container.height(containerHeight + 'px');

        (function () {
            var $labels = $('.official-result-label');

            $labels.css({
                'height': voteWinLabelHeight + 'px',
                'lineHeight': voteWinLabelHeight + 'px',
                'fontSize': (containerHeight * 0.086956) + 'px',
            });
            $labels.filter('#official-result-label-vote').css({
                'top': voteLabelTop + 'px',
            });
            $labels.filter('#official-result-label-win').css({
                'top': winLabelTop + 'px',
            });
            $labels.filter('#official-result-label-total').css({
                'top': totalLabelTop + 'px',
                'height': totalLabelHeight + 'px',
                'fontSize': totalFontSize + 'px',
                'lineHeight': totalLabelHeight + 'px',
            });
        })();

        (function () {
            var $data = $('.result-data');
            $data.css({
                'position': 'absolute',
                'left': 0,
                'width': '100%',
                'height': voteWinLabelHeight + 'px',
                'lineHeight': voteWinLabelHeight + 'px',
                'fontSize': voteWinFontSize + 'px',
            });

            $data.filter('.result-vote').css({
                'top': voteLabelTop + 'px',
            });
            $data.filter('.result-win').css({
                'top': winLabelTop + 'px',
            });
            $data.filter('.result-total').css({
                'top': totalLabelTop + 'px',
                'height': totalLabelHeight + 'px',
                'fontSize': totalFontSize + 'px',
                'lineHeight': totalLabelHeight + 'px',
            });
        })();

        (function() {
            $('.result-team').css({
                'position': 'absolute',
                'top': 0,
                'left': 0,
                'width': '100%',
                'fontSize': (containerHeight * 0.12422) + 'px',
                'height': voteLabelTop + 'px',
                'lineHeight': voteLabelTop + 'px',
            });
        })();
    }; // }}}

    $('#event').on('receiveUpdateData', function (ev, data_) {
        var json = data_.json;
        if (!json.result && $root.is(':visible')) {
            // 結果がないのになぜか見えてしまっているので隠す
            $root.hide();
        } else if(json.result && !$root.is(':visible')) {
            // 読み込み直後か結果確定後最初の状態なのでDOMツリーを作って整えて表示する
            makeDomTree(json);
            onResize();
            $root.show();
        }
    });

    // ウィンドウのリサイズが発生したらサイズ調整する
    var timerId = null;
    $(window).resize(function () {
        if (timerId !== null) {
            window.clearTimeout(timerId);
        }
        timerId = window.setTimeout(onResize, 25);
    });
});
