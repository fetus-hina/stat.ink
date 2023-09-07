/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

((window, $) => {
  $(() => {
    const storage = window.localStorage;
    const loadAllConfig = () => {
      const json = storage.getItem('battle-list');
      const config = json ? JSON.parse(json) : {};
      const defaults = {
        'cell-datetime': true,
        'cell-death-min': false,
        'cell-inked-min': false,
        'cell-judge': true,
        'cell-kd': true,
        'cell-kill-min': false,
        'cell-kill-rate': false,
        'cell-kill-ratio': true,
        'cell-level': false,
        'cell-level-after': false,
        'cell-lobby': false,
        'cell-main-weapon-icon': true,
        'cell-main-weapon': true,
        'cell-main-weapon-short': false,
        'cell-map': true,
        'cell-map-short': false,
        'cell-point': false,
        'cell-rank': false,
        'cell-rank-after': false,
        'cell-rank-in-team': false,
        'cell-reltime': false,
        'cell-result': true,
        'cell-rule': true,
        'cell-rule-short': false,
        'cell-special': false,
        'cell-specials': false,
        'cell-specials-min': false,
        'cell-sub-weapon': false,
        'cell-team-icon': false,
        'cell-team-id': false,
        hscroll: false
      };
      for (const i in defaults) {
        if (Object.prototype.hasOwnProperty.call(defaults, i)) {
          if (config[i] === undefined) {
            config[i] = defaults[i];
          }
        }
      }
      return config;
    };
    const loadConfig = key => {
      const config = loadAllConfig();
      return config[key];
    };
    const updateConfig = (key, enable) => {
      const config = loadAllConfig();
      config[key] = !!enable;
      storage.setItem('battle-list', JSON.stringify(config));
    };
    const changeTableHScroll = enable => {
      if (enable) {
        $('.table-responsive').addClass('table-responsive-force');
      } else {
        $('.table-responsive').removeClass('table-responsive-force');
      }
    };
    const changeCellVisibility = (klass, enable) => {
      if (enable) {
        $('.' + klass).css({ display: 'table-cell' });
      } else {
        $('.' + klass).hide();
      }
    };
    const loadConfigAndUpdateUI = () => {
      $('#table-hscroll').each(function () {
        const enable = loadConfig('hscroll');
        $(this).prop('checked', enable);
        changeTableHScroll(enable);
      });
      $('.table-config-chk').each(function () {
        const klass = $(this).attr('data-klass');
        const enable = loadConfig(klass);
        $(this).prop('checked', enable);
        changeCellVisibility(klass, enable);
      });
    };
    loadConfigAndUpdateUI();
    $('#table-hscroll').click(function () {
      const enable = $(this).prop('checked');
      changeTableHScroll(enable);
      updateConfig('hscroll', enable);
    });
    $('.table-config-chk').click(function () {
      const klass = $(this).attr('data-klass');
      const enable = $(this).prop('checked');
      changeCellVisibility(klass, enable);
      updateConfig(klass, enable);
    });
    $(window).on('storage', () => {
      loadConfigAndUpdateUI();
    });
  });
})(window, jQuery);
