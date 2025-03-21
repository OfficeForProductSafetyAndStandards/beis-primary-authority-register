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
      const banner_settings = settings.govuk_cookies.banner;
      const cookie_policy = cookies.get(banner_settings.cookie_name);

      // Remove the cookie banner if cookies are set.
      if (cookie_policy) {
        document.querySelectorAll('.govuk-cookie-banner__message').forEach(function (message) {
          message.remove()
        })
        document.querySelectorAll('.govuk-cookie-banner').forEach(function (banner) {
          banner.remove()
        })
        console.log('Cookie banner hidden');
      }
    }
  };

  Drupal.behaviors.interactCookieBanner = {
    attach: function (context, settings) {
      const banner_settings = settings.govuk_cookies.banner;

      const el = document.querySelectorAll('.govuk-cookie-banner__message .govuk-button').forEach(function (elem) {
        elem.addEventListener("click", function(event){

          // Hide the current cookie message.
          let current_message = event.target.closest('.govuk-cookie-banner__message');
          current_message.hidden = true;

          // Show and focus the next appropriate cookie message if set.
          let next_message_id = event.target.dataset.cookieReplacementMessage;
          if (next_message_id !== undefined) {
            let next_message = document.getElementById(next_message_id);
            next_message.hidden = false;
            next_message.focus({preventScroll:true});
          }
          // Else hide the entire banner.
          else {
            document.querySelectorAll('.govuk-cookie-banner__message').forEach(function (message) {
              message.remove()
            })
            document.querySelectorAll('.govuk-cookie-banner').forEach(function (banner) {
              banner.remove()
            })
          }

        }, false);
      });
    }
  };
})(Drupal, window.Cookies);
