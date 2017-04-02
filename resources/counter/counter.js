/*! Copyright (C) 2015-2017 AIZAWA Hina | MIT License */
(($) => {
    const interval_short = 2 * 60 * 1000;
    const interval_long = 10 * 60 * 1000;
    const $counters = $('.dseg-counter');
    const createBackgroud = () => {
        $counters.each((i, el) => {
            const $this = $(el);
            $this.prepend(
                $('<span>')
                    .addClass('dseg-counter-bg')
                    .text($this.text().replace(/\d/, '8'))
            );
        });
    };
    const update = () => {
        $.ajax('/api/internal/counter', {
            cache: false,
            dataType: 'json',
            method: 'GET',
        })
        .then(
            json => {
                setTimeout(update, interval_short);
                $counters.each((i, el) => {
                    const $this = $(el);
                    $this.empty().text(
                        String(parseInt(json[$this.attr('data-type')], 10))
                    );
                });
                createBackgroud();
            },
            () => {
                setTimeout(update, interval_long);
            }
        );
    };

    $(() => {
        createBackgroud();
        setTimeout(update, interval_short);
    });
})(jQuery);
