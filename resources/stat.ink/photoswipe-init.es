/*! Copyright (C) 2019-2026 AIZAWA Hina | MIT License */

(() => {
  const IMAGE_RE = /\.(?:gif|jpe?g|png|bmp|webp)(?:\?|$)/i;

  const preloadImage = url => new Promise(resolve => {
    const img = new window.Image();
    img.onload = () => resolve({ width: img.naturalWidth, height: img.naturalHeight });
    img.onerror = () => resolve(null);
    img.src = url;
  });

  const setupGallery = async container => {
    const links = Array.from(container.querySelectorAll('a[href]'))
      .filter(a => IMAGE_RE.test(a.getAttribute('href') || ''));
    if (links.length === 0) {
      return;
    }

    await Promise.all(links.map(async a => {
      if (a.dataset.pswpWidth && a.dataset.pswpHeight) {
        return;
      }
      const size = await preloadImage(a.href);
      if (!size) {
        return;
      }
      a.dataset.pswpWidth = String(size.width);
      a.dataset.pswpHeight = String(size.height);
    }));

    const lightbox = new window.PhotoSwipeLightbox({
      gallery: container,
      children: 'a',
      pswpModule: window.PhotoSwipe
    });
    lightbox.init();
  };

  const init = () => {
    document.querySelectorAll('[data-pswp]').forEach(setupGallery);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
