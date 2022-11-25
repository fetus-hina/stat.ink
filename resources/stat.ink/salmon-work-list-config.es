/*! Copyright (C) 2015-2022 AIZAWA Hina | MIT License */

((window, $) => {
  $(() => {
    const storage = window.localStorage;
    const loadAllConfig = () => {
      const json = storage.getItem('work-list');
      const config = json ? JSON.parse(json) : {};
      const defaults = {
        hscroll: false,
        'cell-splatnet': true,
        'cell-map': true,
        'cell-map-short': false,
        'cell-special': false,
        'cell-result': true,
        'cell-golden': true,
        'cell-golden-wave': false,
        'cell-golden-total': false,
        'cell-golden-total-wave': false,
        'cell-power': true,
        'cell-power-wave': false,
        'cell-power-total': false,
        'cell-power-total-wave': false,
        'cell-danger-rate': true,
        'cell-title': true,
        'cell-title-after': false,
        'cell-datetime': true,
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
      storage.setItem('work-list', JSON.stringify(config));
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
