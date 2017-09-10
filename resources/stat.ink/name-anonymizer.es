(($, silly) => {
  $(() => {
    $('.anonymize').each((i, el) => {
      const $this = $(el);
      const hash = String($this.attr('data-anonymize'));
      let j = 0;
      const generator = () => {
        const hashVal = hash.substr(4 * j++, 4);
        const val = parseInt(hashVal, 16);
        // console.log({
        //   "hash": hash,
        //   "hashVal": hashVal,
        //   "j": j - 1,
        //   "val": val / 0x10000
        // });
        return val / 0x10000;
      };
      $this.empty().text(silly(generator));
    });
  });
})(jQuery, sillyName);
