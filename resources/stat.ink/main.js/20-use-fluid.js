window.useContainerFluid = window.localStorage.getItem('useFluid') == 1;
$(function () {
  var $elem = $('#toggle-use-fluid');
  var $icon = $('.fa-fw', $elem);
  var update = () => {
    var $container = $([
      'body>main>.container',
      'body>main>.container-fluid',
      'nav.navbar>.container-fluid>.container',
      'nav.navbar>.container-fluid>.container-fluid',
      'footer>.container',
      'footer>.container-fluid',
    ].join(','))
    if (window.useContainerFluid) {
      $icon.removeClass('fa-square').addClass('fa-check-square');
      $container.removeClass('container').addClass('container-fluid');
      $('body').addClass('use-fluid');
    } else {
      $icon.removeClass('fa-check-square').addClass('fa-square');
      $container.removeClass('container-fluid').addClass('container');
      $('body').removeClass('use-fluid');
    }
  };
  $elem.click(function () {
    window.useContainerFluid = !window.useContainerFluid;
    window.localStorage.setItem('useFluid', window.useContainerFluid ? 1 : 0);
    update();
  });
  update();
});
