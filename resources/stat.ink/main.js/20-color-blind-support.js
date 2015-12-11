window.colorLock = window.localStorage.getItem('colorLock') == 1;
$(document).ready(function () {
    var $elem = $('#toggle-color-lock');
    var $icon = $('.fa', $elem);
    $elem.click(function() {
        window.colorLock = !window.colorLock;
        window.localStorage.setItem('colorLock', window.colorLock ? 1 : 0);
        window.location.reload();
    });

    if (window.colorLock) {
        $icon.removeClass('fa-square-o').addClass('fa-check-square-o');
        $('body').addClass('color-locked');
    } else {
        $icon.removeClass('fa-check-square-o').addClass('fa-square-o');
        $('body').removeClass('color-locked');
    }
});
