/*! Copyright (C) 2018 AIZAWA Hina | MIT License */

($ => {
  $(() => {
    const $title = $('<h4 class="modal-title">');
    const $hint = $('<p>').text('Please copy this URL:');
    const $input = $('<input class="form-control" type="text" readonly>');
      
    const $dialog = $('<div class="modal fade" tabindex="-1" role="dialog">').append(
      $('<div class="modal-dialog" role="document">').append(
        $('<div class="modal-content">').append(
          $('<div class="modal-header">').append(
            $('<button type="button" class="close" data-dismiss="modal" aria-label="Close">').append(
              $('<span aria-hidden="true">').append(
                $('<span class="fas fa-times">')
              )
            )
          ).append(
            $title
          )
        ).append(
          $('<div class="modal-body">').append(
            $hint
          ).append(
            $input
          )
        )
      )
    );

    $dialog.on('shown.bs.modal', ev => {
      $input.focus();
    });

    $input.on('focus', ev => {
      $(ev.target).select();
    });

    const getUrl = () => {
      const $link = $('link[rel="canonical"]');
      if ($link.length > 0) {
        return $link.attr('href');
      }

      const $twitter = $('meta[name="twitter:url"]');
      if ($twitter.length > 0) {
        return $twitter.attr('content');
      }

      return window.location.href;
    };

    $('body').append($dialog);

    $('.label-permalink').click(ev => {
      const $this = $(ev.target);
      $title.text($this.attr('data-dialog-title'));
      $hint.text($this.attr('data-dialog-hint'));
      $input.val(getUrl());
      $dialog.modal();
    });
  });
})(jQuery);
