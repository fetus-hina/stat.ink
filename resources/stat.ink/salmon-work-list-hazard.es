($ => {
  $(() => {
    $('.danger-rate-bg').each((i, el) => {
      const $this = $(el);
      const dangerRate = parseFloat($this.attr('data-danger-rate'));
      const url = $this.attr('data-bg-url') || '/static-assets/rect-danger.min.svg';
      const width = dangerRate / 2;
      $this.css({
        'background-color': '#ccc',// 'transparent',
        'background-image': `url(${url})`,
        'background-position': 'left',
        'background-size': `${width}% 100%`,
        'background-repeat': 'no-repeat',
        'color': '#fff',
        'text-shadow': '1px 1px 3px rgba(0,0,0,.8)',
      });
    });
  });
})(jQuery);
