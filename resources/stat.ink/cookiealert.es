/*
 * Bootstrap Cookie Alert by Wruczek
 * https://github.com/Wruczek/Bootstrap-Cookie-Alert
 * Released under MIT license
 */

jQuery(() => {
  const STORAGE_ITEM_NAME = 'acceptCookies';
  const STORAGE_ITEM_VALUE = 'accepted';

  const isAccepted = () => {
    return localStorage.getItem(STORAGE_ITEM_NAME) === STORAGE_ITEM_VALUE;
  };

  const markAccepted = () => {
    localStorage.setItem(STORAGE_ITEM_NAME, STORAGE_ITEM_VALUE);
  };

  const cookieAlert = document.querySelector('.cookiealert');
  const acceptCookies = document.querySelector('.acceptcookies');
  if (!cookieAlert) {
    return;
  }

  cookieAlert.offsetHeight; // Force browser to trigger reflow (https://stackoverflow.com/a/39451131)

  // Show the alert if we cant find the 'acceptCookies' cookie
  if (isAccepted()) {
    return;
  }

  cookieAlert.classList.add('show');

  acceptCookies.addEventListener('click', () => {
    markAccepted();
    cookieAlert.classList.remove('show');
  });
});
