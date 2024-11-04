var cookieName = '';

(function ($, Drupal) {
  Drupal.behaviors.GOVUKBehavior = {
    attach: function (context) {
      $(once('GOVUKBehavior', '#main-content', context)).each(function () {
        cookieName = drupalSettings.govuk.cookie_name;
        // For anonymous users, once the accept/reject cookie policy cookie
        // is set then the cookie banner will still show on pages that are cached
        // in the browser.
        if (!getCookie(cookieName) && $('.govuk-cookie-banner').length) {
          $('.govuk-cookie-banner').show();
        }
      });
    }
  };
})(jQuery, Drupal);

function acceptCookiePolicy () {
  setCookie('cookie_preferences_set', 'true', 365);
  setCookie('cookie_policy', '"usage":true,"campaigns":true', 365);
}

function rejectCookiePolicy () {
  setCookie('cookie_preferences_set', 'false', 365);
  setCookie('cookie_policy', '"usage":false,"campaigns":false', 365);
}

function setCookie(cname, cvalue, exdays) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=./;SameSite=Strict";
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) === 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
