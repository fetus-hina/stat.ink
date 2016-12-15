window.useContainerFluid = window.localStorage.getItem('useFluid') == 1;
$(function () {
  var $elem = $('#toggle-use-fluid');
  var $icon = $('.fa', $elem);
  var update = function () {
    var $container = $([
      'body>.container',
      'body>.container-fluid',
      'nav.navbar>.container-fluid>.container',
      'nav.navbar>.container-fluid>.container-fluid',
      'footer>.container',
      'footer>.container-fluid',
    ].join(','))
    if (window.useContainerFluid) {
      $icon.removeClass('fa-square-o').addClass('fa-check-square-o');
      $container.removeClass('container').addClass('container-fluid');
      $('body').addClass('use-fluid');
    } else {
      $icon.removeClass('fa-check-square-o').addClass('fa-square-o');
      $container.removeClass('container-fluid').addClass('container');
      $('body').removeClass('use-fluid');
    }
  };
  window.useContainerFluid
  $elem.click(function () {
    window.useContainerFluid = !window.useContainerFluid;
    window.localStorage.setItem('useFluid', window.useContainerFluid ? 1 : 0);
    update();
  });
  update();
});
