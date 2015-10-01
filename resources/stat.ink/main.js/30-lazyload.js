$(document).ready(function () {
    var map = {};
    var onImageLoad = function (src) {
        var image = this;
        map[src] = image;
        $('img.lazyload[data-src]').each(function () {
            var $this = $(this);
            if ($this.attr('data-src') === src) {
                this.src = image.src;
                $this.removeClass('lazyload').addClass('lazyloaded');
            }
        });
    };

    $('img.lazyload[data-src]').each(function () {
        var $img = $(this);
        var src = $img.attr('data-src');
        if (!map[src]) {
            map[src] = true;
            var image = new Image();
            image.onload = function() {
                onImageLoad.call(this, src);
            };
            image.src = src;
        } else if(map[src] !== true) {
            $img.src = map[src].src;
        }
    });
});
