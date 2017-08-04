($ => {
  $('.table-sortable')
    .stupidtable()
    .on("aftertablesort", function (event, data) {
      const th = $(this).find("th");
      th.find(".arrow").remove();
      const dir = $.fn.stupidtable.dir;
      const arrow = data.direction === dir.ASC ? "fa-angle-up" : "fa-angle-down";
      th.eq(data.column)
        .append(' ')
        .append(
          $('<span>').addClass('arrow fa').addClass(arrow)
        );
     });
})(jQuery);
