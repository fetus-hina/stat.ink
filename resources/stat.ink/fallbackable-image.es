($ => {
  $.fn.fallbackableImage = function (srcList) {
    this.each(function () {
      const $this = $(this);
      const sources = srcList.concat(); // copy array
      const updateImage = () => {
        window.setTimeout( // https://qiita.com/ichironagata/items/51965876a2daaf2c6152
          () => {
            const src = sources.shift();
            $this.attr('src', src);
          },
          1
        );
      };

      $this.on('error', updateImage);
      updateImage();
    });
    return this;
  };
})(jQuery);
