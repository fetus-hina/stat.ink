/*! Copyright (C) 2015-2020 AIZAWA Hina | MIT License */
($ => {
  const moment = window.moment;
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
  const getLocaleDateFormat = (locale, calendar) => {
    locale = String(locale).toLowerCase();

    if (
      ((locale === 'ja' || locale === 'ja-jp') && calendar === 'japanese') ||
      ((locale === 'zh' || locale === 'zh-tw') && calendar === 'roc')
    ) {
      return 'Ny/M/D'; // H30/4/1 23:59 (if japanese)
    }

    return 'l';
  };

  $.fn.currentTime = function (locale, timeZone, calendar) {
    const $this = this;
    window.setInterval(
      () => {
        $this.empty()
          .append(htmlEncode(
            moment().locale(locale).tz(timeZone).format(
              getLocaleDateFormat(locale, calendar)
            )
          ))
          .append(' ')
          .append(htmlEncode(
            moment().locale(locale).tz(timeZone).format('LT')
          ))
          .append(' ')
          .append(
            $('<a href="#timezone-dialog" data-toggle="modal">').text(
              moment().locale(locale).tz(timeZone).format('z')
            )
          );
      },
      1000
    );
    return $this;
  };
})(jQuery);
