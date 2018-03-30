/*! Copyright (C) 2018 AIZAWA Hina | MIT License */
($ => {
  if (!String.prototype.trim) {
    String.prototype.trim = function () {
      return this.replace(/^\s+|\s+$/g, '');
    };
  }

  $(() => {
    const $badge = $('#tlstest-badge');

    const updateBadge = (text, color) => {
      const esc = param => {
        param = String(param).trim();
        return encodeURIComponent(param.replace(
          /[ _-]/g,
          match => {
            switch (match) {
              case ' ':
                return '_';

              case '_':
                return '__';

              case '-':
                return '--';

              default:
                return match;
            }
          }
        ));
      };

      $badge.empty().append(
        $('<img>', {
          src: 'https://img.shields.io/badge/' + esc('TLS Test') + '-' + esc(text) + '-' + esc(color) + '.svg',
        })
      );
    };

    const updateBadgeTLS12 = () => {
      updateBadge('OK (TLS 1.2)', 'brightgreen');
    };

    const updateBadgeToTLS11 = () => {
      updateBadge('So-so (TLS 1.1)', 'yellowgreen');
    };

    const updateBadgeToTLS10 = () => {
      updateBadge('Outdated', 'red');
    };

    setTimeout(
      () => {
        $.get('https://tls12.stat.ink/')
          .done(updateBadgeTLS12)
          .fail(() => {
            $.get('https://tls11.stat.ink/')
              .done(updateBadgeToTLS11)
              .fail(updateBadgeToTLS10);
          })
      },
      1000
    );
  });
})(jQuery);
