/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

(function (window, document) {
  'use strict';

  const getCsrfToken = function () {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : null;
  };

  const isPlainObject = function (value) {
    if (value === null || typeof value !== 'object') {
      return false;
    }
    const proto = Object.getPrototypeOf(value);
    return proto === null || proto === Object.prototype;
  };

  const toFormBody = function (data) {
    if (data instanceof URLSearchParams || data instanceof FormData) {
      return data;
    }
    if (!isPlainObject(data)) {
      throw new TypeError('statinkFetch: data must be a plain object, URLSearchParams, or FormData');
    }
    const params = new URLSearchParams();
    Object.keys(data).forEach(function (key) {
      const value = data[key];
      if (value === undefined || value === null) {
        return;
      }
      if (Array.isArray(value)) {
        value.forEach(function (v) {
          params.append(key, v === undefined || v === null ? '' : String(v));
        });
      } else {
        params.append(key, String(value));
      }
    });
    return params;
  };

  // Lightweight fetch wrapper.
  // - Auto-attaches X-CSRF-Token (read from <meta name="csrf-token">), matching Yii's
  //   $.ajaxPrefilter behavior so that POST/PUT/PATCH/DELETE pass CSRF validation.
  // - Plain-object `data` is sent as application/x-www-form-urlencoded (jQuery default).
  // - Non-2xx responses reject with an Error carrying `.status` and `.response`.
  const statinkFetch = async function (url, options) {
    const opts = options || {};
    const method = (opts.method || 'GET').toUpperCase();
    const responseType = opts.responseType || 'response';

    const headers = Object.assign({}, opts.headers || {});
    const token = getCsrfToken();
    if (token && !('X-CSRF-Token' in headers)) {
      headers['X-CSRF-Token'] = token;
    }

    const init = {
      method,
      credentials: 'same-origin',
      headers
    };
    if (opts.signal) {
      init.signal = opts.signal;
    }

    if (opts.data !== undefined && opts.data !== null && method !== 'GET' && method !== 'HEAD') {
      init.body = toFormBody(opts.data);
    }

    const response = await fetch(url, init);
    if (!response.ok) {
      const err = new Error('HTTP ' + response.status + ' ' + response.statusText);
      err.status = response.status;
      err.response = response;
      throw err;
    }

    if (responseType === 'json') {
      return await response.json();
    }
    if (responseType === 'text') {
      return await response.text();
    }
    return response;
  };

  window.statinkFetch = statinkFetch;
})(window, document);
