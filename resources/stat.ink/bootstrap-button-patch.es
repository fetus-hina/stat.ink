/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

// Mitigation for CVE-2024-6485 (GHSA-vxmc-5x29-h64v)
//
// Bootstrap 3's button plugin inserts data-*-text values (e.g. data-loading-text)
// into the element as HTML. Bootstrap 3 is EOL and no fixed version exists,
// so this replaces Button.prototype.setState to insert them as plain text.
// Only the element's own original content is restored as HTML on reset.
($ => {
  const Button = $.fn.button && $.fn.button.Constructor;
  if (!Button) {
    return;
  }

  const originalKey = 'statinkButtonOriginalContent';

  Button.prototype.setState = function (state) {
    const d = 'disabled';
    const $el = this.$element;
    const isInput = $el.is('input');
    const data = $el.data();

    state += 'Text';

    if ($el.data(originalKey) === undefined) {
      $el.data(originalKey, isInput ? $el.val() : $el.html());
    }

    // push to event loop to allow forms to submit
    setTimeout(() => {
      const value = data[state] == null ? this.options[state] : data[state];
      if (value != null) {
        if (isInput) {
          $el.val(value);
        } else {
          $el.text(value);
        }
      } else if (state === 'resetText') {
        if (isInput) {
          $el.val($el.data(originalKey));
        } else {
          $el.html($el.data(originalKey));
        }
      }

      if (state === 'loadingText') {
        this.isLoading = true;
        $el.addClass(d).attr(d, d).prop(d, true);
      } else if (this.isLoading) {
        this.isLoading = false;
        $el.removeClass(d).removeAttr(d).prop(d, false);
      }
    }, 0);
  };
})(jQuery);
