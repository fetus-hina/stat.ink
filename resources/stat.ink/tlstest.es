/*! Copyright (C) 2018 AIZAWA Hina | MIT License */
($ => {
  if (!String.prototype.trim) {
    String.prototype.trim = function () {
      return this.replace(/^\s+|\s+$/g, '');
    };
  }
  if (!Date.now) {
    Date.now = () => (new Date()).getTime();
  }

  // svgTls12, svgTls11, svgTls10 {{{
  const svgTls12 = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="140" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="140" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h57v20H0z"/><path fill="#4c1" d="M57 0h83v20H57z"/><path fill="url(#b)" d="M0 0h140v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="295" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="470">TLS Test</text><text x="295" y="140" transform="scale(.1)" textLength="470">TLS Test</text><text x="975" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="730">OK (TLS 1.2)</text><text x="975" y="140" transform="scale(.1)" textLength="730">OK (TLS 1.2)</text></g></svg>';
  const svgTls11 = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="174" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="174" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h57v20H0z"/><path fill="#e05d44" d="M57 0h117v20H57z"/><path fill="url(#b)" d="M0 0h174v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="295" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="470">TLS Test</text><text x="295" y="140" transform="scale(.1)" textLength="470">TLS Test</text><text x="1145" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="1070">Outdated (TLS 1.1)</text><text x="1145" y="140" transform="scale(.1)" textLength="1070">Outdated (TLS 1.1)</text></g></svg>';
  const svgTls10 = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="174" height="20"><linearGradient id="b" x2="0" y2="100%"><stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/></linearGradient><clipPath id="a"><rect width="174" height="20" rx="3" fill="#fff"/></clipPath><g clip-path="url(#a)"><path fill="#555" d="M0 0h57v20H0z"/><path fill="#e05d44" d="M57 0h117v20H57z"/><path fill="url(#b)" d="M0 0h174v20H0z"/></g><g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="110"><text x="295" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="470">TLS Test</text><text x="295" y="140" transform="scale(.1)" textLength="470">TLS Test</text><text x="1145" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="1070">Outdated (TLS 1.0)</text><text x="1145" y="140" transform="scale(.1)" textLength="1070">Outdated (TLS 1.0)</text></g></svg>';
  // }}}

  $(() => {
    const $badge = $('#tlstest-badge');
    const $labelHolders = $('.tlstest[data-tls]');

    let tls10 = null;
    let tls11 = null;
    let tls12 = null;

    const label = ($holder, isSuccessful, isRecommendedProto) => {
      const $label = $('<span class="label">');
      if (isSuccessful) {
        $label.append($('<span class="fas fa-fw fa-check">'));
        if (isRecommendedProto) {
          $label.addClass('label-success').css({'background-color': '#5cb85c'});
        } else {
          $label.addClass('label-warning');
        }
      } else {
        $label.addClass('label-danger')
          .append($('<span class="fas fa-fw fa-times">'));
      }
      $holder.empty().append($label);
    };

    const update = () => {
      if (tls12 !== null) {
        label($labelHolders.filter('[data-tls="1.2"]'), tls12, true);
      }
      if (tls11 !== null) {
        label($labelHolders.filter('[data-tls="1.1"]'), tls11, false);
      }
      if (tls10 !== null) {
        label($labelHolders.filter('[data-tls="1.0"]'), tls10, false);
      }

      if (tls11 !== null && tls12 !== null) {
        if (tls12) {
          $badge.html(svgTls12);
        } else if (tls11) {
          $badge.html(svgTls11);
        } else {
          $badge.html(svgTls10);
        }
      }
    };

    setTimeout(
      () => {
        const url = subdomain => {
          const timestamp = Math.floor(Date.now() / 1000);
          return `https://${subdomain}.stat.ink/?p=${subdomain}&t=${timestamp}`;
        };
        $.get(url('tls10'))
          .done(() => { tls10 = true; update() })
          .fail(() => { tls10 = false; update() });
        $.get(url('tls11'))
          .done(() => { tls11 = true; update() })
          .fail(() => { tls11 = false; update() });
        $.get(url('tls12'))
          .done(() => { tls12 = true; update() })
          .fail(() => { tls12 = false; update() });
      },
      1000
    );
  });
})(jQuery);
