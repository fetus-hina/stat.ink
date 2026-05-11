/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function (window, $) {
  'use strict';
  $('.slack-toggle-enable').change(function () {
    const $this = $(this);
    window.statinkFetch('/user/slack-suspend', {
      method: 'POST',
      data: {
        id: $this.attr('data-id'),
        suspend: $this.prop('checked') ? 'no' : 'yes'
      }
    }).catch(function () {}).finally(function () {
      $this.prop('disabled', false);
    });
    $this.prop('disabled', true);
  }).prop('disabled', false);

  $('.slack-test').click(function () {
    const $this = $(this);
    window.statinkFetch('/user/slack-test', {
      method: 'POST',
      data: {
        id: $this.attr('data-id')
      }
    }).catch(function () {});
  }).prop('disabled', false);

  $('.slack-del').click(function () {
    const $this = $(this);
    window.statinkFetch('/user/slack-delete', {
      method: 'POST',
      data: {
        id: $this.attr('data-id')
      }
    }).catch(function () {}).finally(function () {
      window.location.reload();
    });
    $this.prop('disabled', true);
  }).prop('disabled', false);
})(window, jQuery);
