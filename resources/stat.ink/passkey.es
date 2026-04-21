'use strict';

/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

(function ($) {
  const config = window.__passkeyConfig || {};

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

  const showMessage = function (kind, text) {
    const $msg = $('#passkey-message');
    $msg.removeClass('alert alert-success alert-danger alert-warning')
      .addClass('alert')
      .addClass(kind === 'error' ? 'alert-danger' : kind === 'success' ? 'alert-success' : 'alert-warning')
      .text(text)
      .show();
  };

  const isSupported = function () {
    return !!(window.PublicKeyCredential && navigator.credentials && typeof navigator.credentials.create === 'function');
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

  const convertCreateOptions = function (options) {
    const publicKey = Object.assign({}, options.publicKey);
    publicKey.challenge = base64UrlToArrayBuffer(publicKey.challenge);
    publicKey.user = Object.assign({}, publicKey.user, {
      id: base64UrlToArrayBuffer(publicKey.user.id)
    });
    if (Array.isArray(publicKey.excludeCredentials)) {
      publicKey.excludeCredentials = publicKey.excludeCredentials.map(function (cred) {
        return Object.assign({}, cred, {
          id: base64UrlToArrayBuffer(cred.id)
        });
      });
    }
    return { publicKey };
  };

  const register = async function () {
    const nickname = ($('#passkey-nickname').val() || '').trim();
    if (nickname === '') {
      showMessage('error', config.messages.nicknameRequired);
      return;
    }

    const $button = $('#passkey-register-button');
    $button.prop('disabled', true);

    try {
      const options = await postJson(config.urls.start, {});
      const credentialOptions = convertCreateOptions(options);

      let credential;
      try {
        credential = await navigator.credentials.create(credentialOptions);
      } catch (e) {
        showMessage('error', e.message || config.messages.registerFailed);
        return;
      }

      if (!credential) {
        showMessage('error', config.messages.registerFailed);
        return;
      }

      const transports = (credential.response.getTransports && credential.response.getTransports()) || [];

      const result = await postJson(config.urls.finish, {
        client_data_json: arrayBufferToBase64Url(credential.response.clientDataJSON),
        attestation_object: arrayBufferToBase64Url(credential.response.attestationObject),
        nickname,
        transports
      });

      if (result && result.result) {
        window.location.reload();
      } else {
        const msg = (result && result.message) || config.messages.registerFailed;
        showMessage('error', msg);
      }
    } catch (e) {
      showMessage('error', (e && e.message) || config.messages.registerFailed);
    } finally {
      $button.prop('disabled', false);
    }
  };

  const remove = function () {
    const $btn = $(this);
    const id = $btn.attr('data-id');
    if (!window.confirm(config.messages.confirmDelete)) {
      return;
    }
    $btn.prop('disabled', true);
    postJson(config.urls.delete, { id }).always(function () {
      window.location.reload();
    });
  };

  $(function () {
    if (!isSupported()) {
      $('#passkey-unsupported-alert').show();
      $('#passkey-register-button').prop('disabled', true);
      return;
    }

    $('#passkey-register-button').on('click', register);
    $('.passkey-delete').on('click', remove);
  });
})(jQuery);
