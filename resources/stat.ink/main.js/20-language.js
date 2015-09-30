$(document).ready(function () {
    $('a.language-change').click(function () {
        var $this = $(this);
        var newLang = $this.attr('data-lang');
        $.ajax({
            method: 'POST',
            url: '/user/language',
            data: {
                language: $this.attr('data-lang'),
            },
            complete: function () {
                window.location.reload();
            },
            headers: {
                'X-CSRF-Token': getCsrfToken(),
            },
        });
    });
});
