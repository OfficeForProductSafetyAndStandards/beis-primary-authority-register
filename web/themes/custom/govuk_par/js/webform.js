(function ($, Drupal) {
  Drupal.behaviors.WebformBehavior = {
    attach: function (context) {
      $(".webform-submission-form", context).once('error-processed').each(function () {
        $('.webform-submission-form .form-submit').on('click', function( event ) {
          var errors = [];
          // Validate all required inputs
          $('.form-item .form-control').filter(':visible').each( function () {
            if ($(this).hasClass('required')) {
              if (!$(this).val()) {
                $(this).addClass('govuk-input--error');
                $(this).closest('.form-group').addClass('govuk-form-group--error');
                if (!$(this).parent().find(".govuk-error-message").length) {
                  var errorText = $(this).attr('data-webform-required-error');
                  var escapedErrorText = Drupal.checkPlain(errorText);
                  $('<div class="govuk-error-message">' + escapedErrorText + '</div>').insertBefore(this);
                }
                $(this).on('input', function() {
                  if (!$(this).val()) {
                    $(this).parent().find('.govuk-error-message').show();
                    $(this).addClass('govuk-input--error');
                    $(this).closest('.form-group').addClass('govuk-form-group--error');
                  }else{
                    $(this).parent().find('.govuk-error-message').hide();
                    $(this).removeClass('govuk-input--error');
                    $(this).closest('.form-group').removeClass('govuk-form-group--error');
                  }
                });
                errors.push($(this));
              }else{
                $(this).removeClass('govuk-input--error');
              }
            }else{
              $(this).removeClass('govuk-input--error');
            }
          });
          if (errors.length) {
            var html = '<div class="govuk-error-summary" aria-labelledby="error-summary-title" role="alert" tabindex="-1" data-module="govuk-error-summary"><h2 class="govuk-error-summary__title" id="error-summary-title">There is a problem</h2><div class="govuk-error-summary__body"><ul class="govuk-list govuk-error-summary__list">';
            for ( var i = 0, l = errors.length; i < l; i++ ) {
              var text = $(errors[i]).parent().find('.govuk-error-message').text();
              if (!text) {
                text = $(errors[i]).parent().parent().find('.govuk-error-message').text();
              }
              if (!text) {
                text = $(errors[i]).parent().parent().parent().find('.govuk-error-message').text();
              }
              var escapedText = Drupal.checkPlain(text);
              html += '<li><a href="javascript:void(0)" onclick="scrollToError(\'' + $(errors[i]).attr('id') + '\');">' + escapedText + '</a></li>';
            }
            html += '</ul></div>';
            $('.govuk-error-summary').remove();
            $(html).insertBefore($('#block-govuk-page-title'));
            $('.govuk-error-summary').focus();
            event.stopImmediatePropagation();
            return false;
          }
          return true;
        });
      });
    }
  };
})(jQuery, Drupal);

function scrollToError(id) {
  jQuery('#' + id).focus();
  jQuery([document.documentElement, document.body]).animate({
    scrollTop: jQuery('#' + id).offset().top - 100
  }, 500);
}
