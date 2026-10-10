'use strict';

/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

// Re-authenticates the logged-in user with a passkey before submitting a form.
// Target: <form data-passkey-reauth="form"> (plain form)
//         <form data-passkey-reauth="active-form"> (yii\widgets\ActiveForm)
(function ($) {
  const config = window.__passkeyReauthConfig || {};

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
    $('#passkey-reauth-message')
      .addClass('alert alert-danger')
      .text(text)
      .show();
  };

  const hideError = function () {
    $('#passkey-reauth-message').hide().text('');
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
    return window.statinkFetch(url, {
      method: 'POST',
      responseType: 'json',
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

  // Resolves true on success, false on failure (error message is shown)
  const reauthenticate = async function () {
    hideError();
    try {
      const options = await postJson(config.urls.start, {});
      if (!options || !options.publicKey) {
        showError(config.messages.failed);
        return false;
      }

      let assertion;
      try {
        assertion = await navigator.credentials.get(convertGetOptions(options));
      } catch (e) {
        showError(e.message || config.messages.failed);
        return false;
      }

      if (!assertion || !assertion.response) {
        showError(config.messages.failed);
        return false;
      }

      const result = await postJson(config.urls.finish, {
        credential_id: arrayBufferToBase64Url(assertion.rawId),
        client_data_json: arrayBufferToBase64Url(assertion.response.clientDataJSON),
        authenticator_data: arrayBufferToBase64Url(assertion.response.authenticatorData),
        signature: arrayBufferToBase64Url(assertion.response.signature)
      });
      if (result && result.result) {
        return true;
      }
      showError((result && result.message) || config.messages.failed);
    } catch (e) {
      showError((e && e.message) || config.messages.failed);
    }
    return false;
  };

  $(function () {
    const $forms = $('form[data-passkey-reauth]');
    if (!isSupported()) {
      showError(config.messages.unsupported);
      $forms.find('[type="submit"]').prop('disabled', true);
      return;
    }

    $forms.each(function () {
      const $form = $(this);
      let verified = false;
      let running = false;

      const intercept = function () {
        if (verified) {
          return true;
        }
        if (!running) {
          running = true;
          const $submit = $form.find('[type="submit"]').prop('disabled', true);
          reauthenticate().then(function (ok) {
            running = false;
            $submit.prop('disabled', false);
            if (ok) {
              verified = true;
              $form.trigger('submit');
            }
          });
        }
        return false;
      };

      // yii.activeForm triggers "beforeSubmit" after client-side validation
      if ($form.attr('data-passkey-reauth') === 'active-form') {
        $form.on('beforeSubmit', intercept);
      } else {
        $form.on('submit', intercept);
      }
    });
  });
})(jQuery);
