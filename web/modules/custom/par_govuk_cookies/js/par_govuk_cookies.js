/**
 * @file
 * GOVUK Cookies banner behaviors.
 */
(function (Drupal, cookies) {

  'use strict';

  Drupal.behaviors.hideCookieBanner = {
    attach: function (context, settings) {
      // This should only be necessary if the page is cached
      // with the cookie banner in place.
      const banner_settings = settings.par_govuk_cookies.banner;
      const cookie_policy = cookies.get(banner_settings.cookie_name);

      // Remove the cookie banner if cookies are set.
      if (cookie_policy) {
        document.querySelectorAll('.par-govuk-cookie-banner__message').forEach(function (message) {
          message.remove()
        })
        document.querySelectorAll('.par-govuk-cookie-banner').forEach(function (banner) {
          banner.remove()
        })
      }
    }
  };

  Drupal.behaviors.interactCookieBanner = {
    attach: function (context, settings) {
      document.querySelectorAll('.par-govuk-cookie-banner__message .govuk-button').forEach(function (el) {
        el.addEventListener('click', function (event) {
          const banner_settings = settings.par_govuk_cookies.banner;
          let button = event.target.value;

          // Accepted.
          if (button === 'accept') {
            setCookie('cookie_preferences_set', 'true', 365);
            setCookie('cookie_policy', '{"usage":true,"campaigns":true}', 365);
          }

          // Rejected.
          if (button === 'reject') {
            setCookie('cookie_preferences_set', 'true', 365);
            setCookie('cookie_policy', '{"usage":false,"campaigns":false}', 365);
          }

          function setCookie(cname, cvalue, exdays) {
            const d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            let expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/;SameSite=Strict";
          }

          // Hide the current cookie message.
          let current_message = event.target.closest('.par-govuk-cookie-banner__message');
          current_message.hidden = true;

          // Show and focus the next appropriate cookie message if set.
          let next_message_id = event.target.dataset.cookieReplacementMessage;
          if (next_message_id !== undefined) {
            let next_message = document.getElementById(next_message_id);
            next_message.hidden = false;
            next_message.focus({ preventScroll:true });
          }
          // Else hide the entire banner.
          else {
            document.querySelectorAll('.par-govuk-cookie-banner__message').forEach(function (message) {
              message.remove()
            })
            document.querySelectorAll('.par-govuk-cookie-banner').forEach(function (banner) {
              banner.remove()
            })
          }
        });
      });
    }
  };

})(Drupal, window.Cookies);
