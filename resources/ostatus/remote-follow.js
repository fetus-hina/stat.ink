/*! Copyright (C) 2015-2017 AIZAWA Hina | MIT License */
(($, undefined) => {
  $(() => {
    const $openButton = $('#miniinfo-remote-follow');
    const $modal = $('#remoteFollowModal');

    $openButton
      .attr('disabled', false)
      .click(() => { $modal.modal(); });
  })
})(jQuery);
