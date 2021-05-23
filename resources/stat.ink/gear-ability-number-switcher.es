($ => {
  function save (formatID) {
    try {
      if (window.localStorage) {
        window.localStorage.setItem('gear-ability-number-format', formatID);
      }
    } catch (e) {
      console && console.error(e);
    }
  }

  function load () {
    try {
      if (window.localStorage) {
        const value = window.localStorage.getItem('gear-ability-number-format');
        if (
          value === '5.7' ||
          value === '57' ||
          value === '3,9' ||
          value === '3+9'
        ) {
          return value;
        }
      }
    } catch (e) {
      console && console.error(e);
    }
    return null;
  }

  function changeFormat ($targets, formatID) {
    $targets.each(function () {
      const $target = $(this);
      const values = JSON.parse($target.attr('data-values'));
      $target.text(values[formatID]);
      save(formatID);
    });
  }

  $.fn.gearAbilityNumberSwitcher = function ($textTargets) {
    this.each(function () {
      const $selector = $(this);
      $selector.click(function () {
        changeFormat($textTargets, $selector.attr('data-format'));
        $selector.parents('.open').removeClass('open');
        return false;
      });
    });

    // 以前の表示方法を読み込み
    $(() => {
      const format = load();
      if (format !== null) {
        changeFormat($textTargets, format);
      }
    });

    return this;
  };
})(jQuery);
