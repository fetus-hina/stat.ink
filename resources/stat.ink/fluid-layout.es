/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */

((win, $) => {
  const useFluid = () => win.localStorage.getItem('useFluid') == 1;
  const toggleFluid = () => win.localStorage.setItem('useFluid', useFluid() ? 0 : 1);

  $(() => {
    const $elem = $('#toggle-use-fluid');
    const $icon = $('.fa-fw', $elem);
    const update = () => {
      const $container = $([
        'body>main>.container',
        'body>main>.container-fluid',
        'nav.navbar>.container-fluid>.container',
        'nav.navbar>.container-fluid>.container-fluid',
        'footer>.container',
        'footer>.container-fluid',
      ].join(','));

      if (useFluid()) {
        $icon
          .removeClass('fa-square')
          .addClass('fa-check-square');

        $container
          .removeClass('container')
          .addClass('container-fluid');

        $('body').addClass('use-fluid');
      } else {
        $icon
          .removeClass('fa-check-square')
          .addClass('fa-square');

        $container
          .removeClass('container-fluid')
          .addClass('container');

        $('body').removeClass('use-fluid');
      }
    };

    $elem.click(() => {
      toggleFluid();
      update();
    });

    update();
  });
})(window, jQuery);
