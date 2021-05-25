/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
($ => {
  $(() => {
    const urlRegex = /^https?:\/\/[^/]+.*$/;
    const $td = $('#link-cell');
    const $displayGroup = $('#link-cell-display', $td);
    const $editGroup = $('#link-cell-edit', $td);
    if ($td.length && $displayGroup.length && $editGroup.length) {
      $('#link-cell-start-edit')
        .prop('disabled', false)
        .click(function () {
          $('#link-cell-edit-input').val($displayGroup.attr('data-url')).change();
          $editGroup.show();
          $displayGroup.hide();
        });

      (function () {
        let timerId = null;
        $('#link-cell-edit-input')
          .change(function () {
            const $this = $(this);
            const val = ($this.val() + '').trim();
            $('#link-cell-edit-apply')
              .prop('disabled', val !== '' && !val.match(urlRegex));
            if (timerId) {
              window.clearTimeout(timerId);
              timerId = null;
            }
          })
          .keydown(function () {
            const $this = $(this);
            if (timerId) {
              window.clearTimeout(timerId);
            }
            timerId = window.setTimeout(function () {
              $this.change();
            }, 50);
          });
      })();

      $('#link-cell-edit-apply')
        .click(function () {
          const $this = $(this);
          const val = ($('#link-cell-edit-input').val() + '').trim();
          if (!val.match(urlRegex) && val !== '') {
            return;
          }
          $this.prop('diabled', true);
          $.ajax($displayGroup.attr('data-post'), {
            method: 'POST',
            data: {
              _method: 'PATCH',
              link_url: val
            },
            success: function (json) {
              const url = (json.link_url || '') + '';
              $displayGroup.attr('data-url', url);
              if (url === '') {
                $('a', $displayGroup).remove();
              } else {
                let $a = $('a', $displayGroup);
                if (!$a.length) {
                  $a = $('<a>', { rel: 'nofollow' });
                  $displayGroup.prepend($a);
                }
                $a.attr('href', url).text(url);
              }
              $displayGroup.show();
              $editGroup.hide();
            },
            error: function () {
              window.alert($this.attr('data-error'));
              $this.prop('disabled', false);
              $('#link-cell-edit-input').focus().select();
            }
          });
        });
    }
  });
})(jQuery);
