/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */
(window => {
  const colorLock = !!window.colorLock;

  // http://jfly.iam.u-tokyo.ac.jp/colorset/
  const accentColors = {
    red: '#ec6110',
    yellow: '#fff001',
    green: '#07af7b',
    blue: '#3169b3',
    orange: '#f5a101',
    sky: '#68c8f2',
    pink: '#ef908a',
    brown: '#8a3b2c',
    purple: '#a53d92',

    altYellow: '#fff57f',
    altGreen: '#78c496'
  };

  const bgColors = {
    red: '#f7c7c7',
    yellow: '#fef4ad',
    green: '#87c9a5',
    blue: '#b9e3f9',
    orange: '#facd89',
    purple: '#d2cce6',
    yg: '#cede4a'
  };

  const gray = {
    white: '#ffffff',
    lightGray: '#dde1e4',
    darkGray: '#7d818d',
    black: '#000000'
  };

  window.colorScheme = {
    _accent: accentColors,
    _bg: bgColors,
    _gray: gray,

    win: accentColors.blue,
    lose: accentColors.red,
    ko: accentColors.sky,
    time: accentColors.orange,

    area: accentColors.green,
    yagura: accentColors.purple,
    hoko: accentColors.orange,
    asari: accentColors.sky,

    graph1: accentColors.orange,
    graph2: accentColors.blue,
    moving1: colorLock ? '#7fffbb' : 'rgba(64, 237, 64, 0.5)',
    moving2: colorLock ? '#fff57f' : 'rgba(148, 64, 237, 0.5)'
  };
})(window);
