window.battleList = function () {
  "use strict";
  var lastPeriodId = null;
  $('.battle-row').each(function(){
    var $row = $(this);
    if ($row.attr('data-period') === lastPeriodId) {
      return;
    }
    if (lastPeriodId !== null) {
      $row.css('border-top', '2px solid grey');
    }
    lastPeriodId = $row.attr('data-period');
  });

  var hsv2rgb = function (h, s, v) {
    while (h < 0) {
      h += 360;
    }
    h = h % 360;
    return tinycolor.fromRatio({h: h / 360.0, s: s, v: v}).toHexString();
  };

  var calcColor = function (ratio) {
    var redH = 0, greenH = 120, defaultH = 60;
    var S = 0.40, V = 0.90;
    var H = Math.round((function () {
      if (ratio == 1.0) {
        return defaultH;
      } else if (ratio >= 3.0) {
        return greenH;
      } else if (ratio <= 1/3) {
        return redH;
      } else if (ratio > 1.0) {
        var pos = (ratio - 1.0) / 2.0;
        return defaultH + (greenH - defaultH) * pos;
      } else {
        var pos = (ratio - 1/3) * (3/2);
        return redH + (defaultH - redH) * pos;
      }
    })());
    return hsv2rgb(H, S, V);
  };

  $('.kill-ratio').each(function() {
    var $this = $(this);
    $this.css('background-color', calcColor(parseFloat($this.attr('data-kill-ratio'))));
  });
};
