'use strict';

/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

(function ($) {
  const config = window.__passkeyLoginConfig || {};

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

  const showError = function (text) {
    $('#passkey-login-message')
      .removeClass('alert-success alert-warning')
      .addClass('alert alert-danger')
      .text(text)
      .show();
  };

  const hideError = function () {
    $('#passkey-login-message').hide().text('');
  };

  const isSupported = function () {
    return !!(
      window.PublicKeyCredential &&
      navigator.credentials &&
      typeof navigator.credentials.get === 'function'
    );
  };

  const postJson = function (url, payload) {
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

  const signIn = async function () {
    hideError();

    const $button = $('#passkey-login-button');
    $button.prop('disabled', true);

    try {
      const options = await postJson(config.urls.start, {});
      const credentialOptions = convertGetOptions(options);

      let assertion;
      try {
        assertion = await navigator.credentials.get(credentialOptions);
      } catch (e) {
        showError(e.message || config.messages.loginFailed);
        return;
      }

      if (!assertion || !assertion.response || !assertion.response.userHandle) {
        showError(config.messages.loginFailed);
        return;
      }

      const rememberMe = $('#passkey-login-remember').is(':checked') ? '1' : '0';
      const result = await postJson(config.urls.finish, {
        credential_id: arrayBufferToBase64Url(assertion.rawId),
        client_data_json: arrayBufferToBase64Url(assertion.response.clientDataJSON),
        authenticator_data: arrayBufferToBase64Url(assertion.response.authenticatorData),
        signature: arrayBufferToBase64Url(assertion.response.signature),
        user_handle: arrayBufferToBase64Url(assertion.response.userHandle),
        remember_me: rememberMe
      });

      if (result && result.result) {
        window.location.href = config.urls.redirect;
      } else {
        showError((result && result.message) || config.messages.loginFailed);
      }
    } catch (e) {
      showError((e && e.message) || config.messages.loginFailed);
    } finally {
      $button.prop('disabled', false);
    }
  };

  $(function () {
    if (!isSupported()) {
      $('#passkey-login-panel').hide();
      return;
    }
    $('#passkey-login-button').on('click', signIn);
  });
})(jQuery);
