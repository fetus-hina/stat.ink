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
    /*
      var colorHigh = $.Color("#3e8ffa"); // H:214, S:75, V:98 / S:95, L:61
      var colorMid  = $.Color("#888888"); // H:  0, S: 0, V:53 / S: 0, L:53
      var colorLow  = $.Color("#fa833e"); // H: 22, S:75, V:98 / S:95, L:61
    */
    var ratio2 = (function() {
        if (ratio >= 4.0) {
            return 1.0;
        } else if (ratio <= 0.25) {
            return 0.0;
        } else if (ratio >= 1.0) {
            return (ratio - 1.0) / 3.0 * 0.5 + 0.5;
        } else {
            return (ratio - 0.25) / 0.75 * 0.5;
        }
    })() * 100;

    if (ratio2 >= 50) {
      return $.Color({
        hue: 214,
        saturation: 0.95 * ((ratio2 - 50) * 2 / 100),
        lightness: 0.53 + 0.08 * ((ratio2 - 50) * 2 / 100),
      })
      .toRgbaString();
    } else {
      return $.Color({
        hue: 22,
        saturation: 0.95 * ((50 - ratio2) * 2 / 100),
        lightness: 0.53 + 0.08 * ((50 - ratio2) * 2 / 100)
      }).toRgbaString();
    }
  };

  $('.kill-ratio').each(function() {
    var $this = $(this);
    var kr = parseFloat($this.attr('data-kill-ratio'));
    $this.css('background-color', calcColor(kr));
    if (kr >= 1.00) {
        $this.css('color', '#fff');
    }
  });
};
