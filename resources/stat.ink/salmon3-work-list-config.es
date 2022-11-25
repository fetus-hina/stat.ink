/*! Copyright (C) 2015-2022 AIZAWA Hina | MIT License */

((window, $) => {
  const STORAGE_NAME = 'salmon3-list';

  $(() => {
    const storage = window.localStorage;
    const loadAllConfig = () => {
      const json = storage.getItem(STORAGE_NAME);
      const config = json ? JSON.parse(json) : {};
      const defaults = {
        hscroll: false,
        'cell-map': false,
        'cell-map-short': true,
        'cell-weapon': true,
        'cell-special': false,
        'cell-special-icon': true,
        'cell-result': true,
        'cell-king-smell': true,
        'cell-golden': true,
        'cell-golden-total': false,
        'cell-power': true,
        'cell-power-total': false,
        'cell-danger-rate': true,
        'cell-title': false,
        'cell-title-after': true,
        'cell-datetime': false,
        'cell-reltime': false
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
      storage.setItem(STORAGE_NAME, JSON.stringify(config));
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
        $('.' + klass).show();
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
    loadConfigAndUpdateUI();
  });
})(window, jQuery);
