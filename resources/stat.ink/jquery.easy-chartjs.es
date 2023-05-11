/*! Copyright (C) 2015-2023 AIZAWA Hina | MIT License */

jQuery.fn.easyChartJs = function () {
  function looseJsonParse (obj) {
    /* eslint no-new-func: 0 */
    return Function('"use strict";return (' + obj + ')')();
  }

  this.each(
    function () {
      const elem = this;
      const config = looseJsonParse(elem.getAttribute('data-chart'));
      const canvas = elem.appendChild(document.createElement('canvas'));
      /* eslint no-new: 0 */
      new window.Chart(
        canvas.getContext('2d'),
        config
      );
    }
  );

  return this;
};
