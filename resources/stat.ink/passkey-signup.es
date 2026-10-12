'use strict';

/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

(function ($) {
  const config = window.__passkeySignupConfig || {};

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
    $('#passkey-signup-message')
      .removeClass('alert-success alert-warning')
      .addClass('alert alert-danger')
      .text(text)
      .show();
  };

  const hideError = function () {
    $('#passkey-signup-message').hide().text('');
  };

  const isSupported = function () {
    return !!(
      window.PublicKeyCredential &&
      navigator.credentials &&
      typeof navigator.credentials.create === 'function'
    );
  };

  const postJson = function (url, data) {
    return window.statinkFetch(url, {
      method: 'POST',
      responseType: 'json',
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

  // Shows server-side validation errors (keyed by input ID) on the form, or a generic message
  const showFailure = function ($form, result) {
    if (result && result.errors) {
      $form.yiiActiveForm('updateMessages', result.errors, true);
      return;
    }
    showError((result && result.message) || config.messages.registerFailed);
  };

  const signUp = async function ($form) {
    hideError();

    const $button = $('#passkey-signup-button');
    $button.prop('disabled', true);

    let success = false;
    try {
      // The form contains the CSRF token
      const start = await postJson(config.urls.start, new URLSearchParams(new FormData($form.get(0))));
      if (!start || !start.result) {
        showFailure($form, start);
        return;
      }

      let credential;
      try {
        credential = await navigator.credentials.create(convertCreateOptions(start.options));
      } catch (e) {
        showError(e.message || config.messages.registerFailed);
        return;
      }

      if (!credential) {
        showError(config.messages.registerFailed);
        return;
      }

      const transports = (credential.response.getTransports && credential.response.getTransports()) || [];

      const result = await postJson(config.urls.finish, {
        client_data_json: arrayBufferToBase64Url(credential.response.clientDataJSON),
        attestation_object: arrayBufferToBase64Url(credential.response.attestationObject),
        transports
      });

      if (result && result.result) {
        success = true;
        window.location.href = result.redirect;
        return;
      }
      showFailure($form, result);
    } catch (e) {
      showError((e && e.message) || config.messages.registerFailed);
    } finally {
      if (!success) {
        $button.prop('disabled', false);
      }
    }
  };

  $(function () {
    const $form = $('#register-passkey-form');

    if (!isSupported()) {
      $('#passkey-signup-unsupported').show();
      $('#passkey-signup-button').prop('disabled', true);
      return;
    }

    // Fired by yiiActiveForm after the client-side validation passed
    $form.on('beforeSubmit', function () {
      signUp($form);
      return false;
    });
  });
})(jQuery);
