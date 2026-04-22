'use strict';

/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

(function ($) {
  const base64UrlToArrayBuffer = function (input) {
    const padded = input + '==='.slice((input.length + 3) % 4);
    const base64 = padded.replace(/-/g, '+').replace(/_/g, '/');
    const binary = window.atob(base64);
    const bytes = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) {
      bytes[i] = binary.charCodeAt(i);
    }
    return bytes.buffer;
  };

  const arrayBufferToBase64Url = function (buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.byteLength; i++) {
      binary += String.fromCharCode(bytes[i]);
    }
    return window.btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
  };

  const postJson = function (config, url, payload) {
    const data = Object.assign({}, payload);
    if (config.csrfParam && config.csrfToken) {
      data[config.csrfParam] = config.csrfToken;
    }
    return $.ajax(url, {
      type: 'POST',
      dataType: 'json',
      data
    });
  };

  const convertGetOptions = function (options) {
    const publicKey = Object.assign({}, options.publicKey);
    publicKey.challenge = base64UrlToArrayBuffer(publicKey.challenge);
    if (Array.isArray(publicKey.allowCredentials)) {
      publicKey.allowCredentials = publicKey.allowCredentials.map(function (cred) {
        return Object.assign({}, cred, {
          id: base64UrlToArrayBuffer(cred.id)
        });
      });
    }
    return { publicKey };
  };

  const signIn = async function (config) {
    let options;
    try {
      options = await postJson(config, config.urls.start, {});
    } catch (e) {
      window.location.href = config.urls.login;
      return;
    }

    let assertion;
    try {
      assertion = await navigator.credentials.get(convertGetOptions(options));
    } catch (e) {
      return;
    }

    if (!assertion || !assertion.response || !assertion.response.userHandle) {
      return;
    }

    try {
      const result = await postJson(config, config.urls.finish, {
        credential_id: arrayBufferToBase64Url(assertion.rawId),
        client_data_json: arrayBufferToBase64Url(assertion.response.clientDataJSON),
        authenticator_data: arrayBufferToBase64Url(assertion.response.authenticatorData),
        signature: arrayBufferToBase64Url(assertion.response.signature),
        user_handle: arrayBufferToBase64Url(assertion.response.userHandle),
        remember_me: '1'
      });
      if (result && result.result) {
        window.location.href = config.urls.redirect;
      } else {
        window.location.href = config.urls.login;
      }
    } catch (e) {
      window.location.href = config.urls.login;
    }
  };

  $(function () {
    $(document).on('click', '#navbar-passkey-login', function (e) {
      e.preventDefault();
      const config = window.__navbarPasskeyLoginConfig || {};
      if (!config.urls) {
        return;
      }
      signIn(config);
    });
  });
})(jQuery);
