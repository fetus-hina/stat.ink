(function (window) {
  "use strict";
  var $ = window.jQuery;
  $(window.document).ready(function () {
    var dirname = (function () { // {{{
      var ret = '/';
      $('script').each(function() {
        var src = ($(this).attr('src') + "")
          .replace(/#[^#]+$/, '') // フラグメントの削除
          .replace(/\?[^?]+$/, ''); // クエリパラメータの削除
        var match = src.match(/^(^|.*\/)tz-init\.js$/);
        if (match) {
          ret = match[1].replace(/\/$/, ''); // 末尾の / の削除
          return false;
        }
      });
      return ret;
    })(); // }}}
    
    timezoneJS.timezone.zoneFileBasePath = dirname + '/files';
    timezoneJS.timezone.init();
  });
})(window);
