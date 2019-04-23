$(document).ready(function () {
  $('a.timezone-change').click(function () {
    var $this = $(this);
    $.ajax({
      method: 'POST',
      url: '/user/timezone',
      data: {
        timezone: $this.attr('data-tz'),
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
