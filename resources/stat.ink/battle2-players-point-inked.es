(($) => {
  const save = showInked => {
    if (!window.localStorage) {
      return;
    }
    window.localStorage.setItem('battle2-point-inked', showInked ? 'inked' : 'point');
  };

  const load = () => {
    if (!window.localStorage) {
      return false;
    }
    const value = window.localStorage.getItem('battle2-point-inked');
    return value === 'inked';
  };

  $(() => {
    let showInked = load();
    const $button = $('#players-swith-point-inked').attr('disabled', false);
    const $points = $('.col-point-point');
    const $inks = $('.col-point-inked');
    const updateUi = () => {
      $button
        .removeClass('btn-default')
        .removeClass('btn-primary')
        .addClass(showInked ? 'btn-primary' : 'btn-default');
      if (showInked) {
        $points.addClass('hidden').attr('aria-hidden', 'true');
        $inks.removeClass('hidden').attr('aria-hidden', 'false');
      } else {
        $inks.addClass('hidden').attr('aria-hidden', 'true');
        $points.removeClass('hidden').attr('aria-hidden', 'false');
      }
    };
    $button.click(() => {
      showInked = !showInked;
      updateUi();
      save(showInked);
    });
    updateUi(); // init
  });
})(jQuery);
