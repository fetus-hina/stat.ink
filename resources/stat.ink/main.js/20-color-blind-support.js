window.colorLock = window.localStorage.getItem('colorLock') == 1;
$(document).ready(function () {
    var $elem = $('#toggle-color-lock');
    var $icon = $('.fa-fw', $elem);
    $elem.click(function() {
        window.colorLock = !window.colorLock;
        window.localStorage.setItem('colorLock', window.colorLock ? 1 : 0);
        window.location.reload();
    });

    if (window.colorLock) {
        $icon.removeClass('fa-square').addClass('fa-check-square');
        $('body').addClass('color-locked');
    } else {
        $icon.removeClass('fa-check-square').addClass('fa-square');
        $('body').removeClass('color-locked');
    }
});
