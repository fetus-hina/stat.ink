/*! Copyright (C) Twitter https://dev.twitter.com/web/intents */
((window, document) => {
  if (window.__twitterIntentHandler) {
    return;
  }

  const intentRegex = /twitter\.com\/intent\/(\w+)/;
  const windowOptions = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes';
  const width = 550;
  const height = 420;
  const winHeight = screen.height;
  const winWidth = screen.width;

  const handleIntent = e => {
    e = e || window.event;
    let target = e.target || e.srcElement;
    let m;
    let left;
    let top;

    while (target && target.nodeName.toLowerCase() !== 'a') {
      target = target.parentNode;
    }

    if (target && target.nodeName.toLowerCase() === 'a' && target.href) {
      m = target.href.match(intentRegex);
      if (m) {
        left = Math.round((winWidth / 2) - (width / 2));
        top = 0;
        if (winHeight > height) {
          top = Math.round((winHeight / 2) - (height / 2));
        }
        window.open(
          target.href,
          'intent',
          `${windowOptions},width=${width},height=${height},left=${left},top=${top}`
        );
        e.returnValue = false;
        e.preventDefault && e.preventDefault();
      }
    }
  };

  if (document.addEventListener) {
    document.addEventListener('click', handleIntent, false);
  } else if (document.attachEvent) {
    document.attachEvent('onclick', handleIntent);
  }
  window.__twitterIntentHandler = true;
})(window, document);
