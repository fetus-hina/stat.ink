/*! Copyright (C) 2018 AIZAWA Hina | MIT License */
($ => {
  if (!String.prototype.trim) {
    String.prototype.trim = function () {
      return this.replace(/^\s+|\s+$/g, '');
    };
  }

  $(() => {
    const $badge = $('#tlstest-badge');

    const updateBadgeTLS12 = () => {
      $badge.html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="140" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="140" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h57v20H0z"/><path fill="#4c1" d="M57 0h83v20H57z"/><path fill="url(#b)" d="M0 0h140v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="295" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="470">TLS Test</text><text x="295" y="140" transform="scale(.1)" textLength="470">TLS Test</text><text x="975" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="730">OK (TLS 1.2)</text><text x="975" y="140" transform="scale(.1)" textLength="730">OK (TLS 1.2)</text></g></svg>');
    };

    const updateBadgeToTLS11 = () => {
      $badge.html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="154" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="154" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h57v20H0z"/><path fill="#a4a61d" d="M57 0h97v20H57z"/><path fill="url(#b)" d="M0 0h154v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="295" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="470">TLS Test</text><text x="295" y="140" transform="scale(.1)" textLength="470">TLS Test</text><text x="1045" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="870">So-so (TLS 1.1)</text><text x="1045" y="140" transform="scale(.1)" textLength="870">So-so (TLS 1.1)</text></g> </svg>');
    };

    const updateBadgeToTLS10 = () => {
      $badge.html('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="174" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="174" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h57v20H0z"/><path fill="#e05d44" d="M57 0h117v20H57z"/><path fill="url(#b)" d="M0 0h174v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="295" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="470">TLS Test</text><text x="295" y="140" transform="scale(.1)" textLength="470">TLS Test</text><text x="1145" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="1070">Outdated (TLS 1.0)</text><text x="1145" y="140" transform="scale(.1)" textLength="1070">Outdated (TLS 1.0)</text></g> </svg>');
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
