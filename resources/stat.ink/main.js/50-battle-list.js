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

  var calcColor = function (ratio) {
    /*
      var colorHigh = $.Color("#3e8ffa"); // H:214, S:75, V:98 / S:95, L:61
      var colorMid  = $.Color("#9c9c9c"); // H:  0, S: 0, V:98 / S: 0, L:61
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

    if (window.colorLock) {
      if (ratio2 >= 50) {
        return $.Color({
          hue: 214,
          saturation: 0.95 * ((ratio2 - 50) * 2 / 100),
          lightness: 0.61, //0.53 + 0.08 * ((ratio2 - 50) * 2 / 100),
        })
        .toRgbaString();
      } else {
        return $.Color({
          hue: 22,
          saturation: 0.95 * ((50 - ratio2) * 2 / 100),
          lightness: 0.61, //0.53 + 0.08 * ((50 - ratio2) * 2 / 100)
        }).toRgbaString();
      }
    } else {
      return $.Color({
        hue: 22 + (76 * ratio2 / 100),
        saturation: 0.80,
        lightness: 0.55,
      }).toRgbaString();
    }
  };

  var calcFgColor = function (c) {
    var color = $.Color(c);
    var y = Math.round(color.red() * 0.299 + color.green() * 0.587 + color.blue() * 0.114);
    return y > 153 ? '#000' : '#fff';
  };

  $('.kill-ratio,.kill-rate').each(function() {
    var $this = $(this);
    var kr = parseFloat($this.attr('data-kill-ratio'));
    $this.css('background-color', calcColor(kr));
    $this.css('color', calcFgColor($this.css('background-color')));
  });
};
