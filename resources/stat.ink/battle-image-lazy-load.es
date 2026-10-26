/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

(() => {
  const setupImage = img => {
    const frame = img.closest('.battle-image-frame');
    if (!frame) {
      return;
    }

    const onLoad = () => {
      // Tell PhotoSwipe the real size (the markup has a 16:9 guess)
      const link = img.closest('a');
      if (link && img.naturalWidth > 0 && img.naturalHeight > 0) {
        link.dataset.pswpWidth = String(img.naturalWidth);
        link.dataset.pswpHeight = String(img.naturalHeight);
      }

      frame.classList.remove('battle-image-loading');
      frame.classList.add('battle-image-loaded');
    };

    const onError = () => {
      // e.g., s3-img-gen failed to generate the image
      const container = frame.closest('.image-container');
      (container || frame).remove();
    };

    if (img.complete) {
      if (img.naturalWidth > 0) {
        onLoad();
      } else {
        onError();
      }
      return;
    }

    img.addEventListener('load', onLoad, { once: true });
    img.addEventListener('error', onError, { once: true });
  };

  const init = () => {
    document.querySelectorAll('.battle-image-frame img').forEach(setupImage);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
