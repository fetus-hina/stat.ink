/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

($ => {
  const htmlEncode = str => {
    return String(str).replace(/[<>&"']/g, match => {
      const table = {
        '<': '&lt;',
        '>': '&gt;',
        '&': '&amp;',
        '"': '&quot;',
        '\'': '&#39;',
      };
      return table[match];
    });
  };

  $(() => {
    $(document).on('click', 'a.timezone-change', function () {
      const $this = $(this);
      const ajaxOptions = {
        method: 'POST',
        url: '/user/timezone',
        data: {
          timezone: $this.attr('data-tz'),
        },
      };
      $.ajax(ajaxOptions)
        .always(() => {
          window.location.reload();
        });
    });
  });

  $.fn.timezoneDialog = function () {
    this.on('show.bs.modal', function () {
      const $this = $(this);
      const ajaxOptions = {
        'method': 'GET',
        'dataType': 'json',
        'url': '/api/internal/guess-timezone',
      };

      const $labels = $('.guessed-timezone', $this);
      // Change label face to "Loading..."
      $labels.each(function () {
        const $label = $(this);
        $label.empty()
          .append($('<span class="fas fa-fw fa-spinner fa-spin">'))
          .append(' ')
          .append(htmlEncode($label.attr('data-loading')));
      });

      // Let's request
      $.ajax(ajaxOptions)
        .done(data => {
          $labels.each(function () {
            const $label = $(this);
            if (!data.guessed) {
              $label.empty().append(htmlEncode($label.attr('data-unknown')));
            } else {
              $label.empty()
                .append(
                  $('<a class="timezone-change text-link">')
                    .attr('data-tz', data.guessed.identifier)
                    .attr(
                      'title',
                      String($label.attr('data-tooltip')).replace('{timezone}', data.geoip)
                    )
                    .append(htmlEncode(data.guessed.name))
                    .tooltip()
                );
            }
          });
        })
        .fail(() => {
          $labels.each(function () {
            const $label = $(this);
            $label.empty().append(htmlEncode($label.attr('data-error')));
          });
        });
    });

    $('[data-toggle="collapse"]', this).each(function () {
      const $parent = $(this);
      const $icon = $('.fa-chevron-down', $parent);
      $($parent.data('target'))
        .on('hidden.bs.collapse', () => {
          $icon
            .removeClass('fa-chevron-up')
            .addClass('fa-chevron-down');
        })
        .on('shown.bs.collapse', () => {
          $icon
            .removeClass('fa-chevron-down')
            .addClass('fa-chevron-up');
        });
    });
  };
})(jQuery);
