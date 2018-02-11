@ci @PAR1055 @PAR1057 @PAR1059 @PAR1060 @PAR1062 @tota11y
Feature: New Direct Partnership For HD user To Revoke

    Scenario: New Direct Partnership For HD user To Revoke

        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I click on the link "Apply for a new partnership"
        And I click on the button "#edit-cancel"
        When I click on the link "Apply for a new partnership"

        # CHOOSE AUTHORITY REPRESENTED

        And I click on the radio "label*=Metropolitan District"
        And I click on the button "#edit-next"

        # CHOOSE PARTNERSHIP TYPE

        When I click on the radio "#edit-application-type-direct"
        And I click on the button "#edit-next"

        # CONFIRMATIONS

        When I click on the checkbox "#edit-business-eligible-for-partnership"
        And I click on the checkbox "#edit-local-authority-suitable-for-nomination"
        And I click on the checkbox "#edit-written-summary-agreed"
        And I click on the checkbox "#edit-terms-organisation-agreed"
        And I click on the radio "#edit-business-regulated-by-one-authority-1"
        And I expect that element "#par-partnership-application-authority-checklist" contains the text "Is this your local authority?"
        And I click on the radio "#edit-business-regulated-by-one-authority-1"
        And I click on the radio "#edit-is-local-authority-1"
  #        And I expect that element ".error-summary" contains the text "The business needs to be informed about local authority"
        When I click on the button "#edit-next"

        # ADD ABOUT THE PARTNERSHIP
        When I add "About the partnership for revoking detail" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"

        # ORGANISATION NAME

        And I add "Direct Partnership For Revoking" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-next"

        # CONFIRM NEW PARTNERSHIP

        And I click new partnership if presented with choices

        # ADD BUSINESS DETAIL
        And I add "Leidseplein 10" to the inputfield "#edit-address-line1"
        And I add "Leidseplein" to the inputfield "#edit-address-line2"
        When I add "Amsterdam" to the inputfield "#edit-town-city"
        Then I expect that element "#edit-country-code" is visible
        And I select the option with the text "Netherlands" for element "#edit-country-code"
        When I add "017 PT" to the inputfield "#edit-postcode"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" is visible
        And I clear the inputfield "#edit-postcode"
        When I add "1017 PT" to the inputfield "#edit-postcode"
        And I click on the button "#edit-next"

        # MAIN CONTACT
        And I add "Mr" to the inputfield "#edit-salutation"
        And I add "Test" to the inputfield "#edit-first-name"
        And I add "Revoking" to the inputfield "#edit-last-name"
        And I add "02085678453" to the inputfield "#edit-work-phone"
        And I add "075643334444" to the inputfield "#edit-mobile-phone"
        And I add "testrevoking88@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-next"

        # REVIEW THE APPLICATION

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "New partnership application"
        And I expect that element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        When I click on the checkbox "#edit-partnership-info-agreed-authority"
        And I click on the button "#edit-save"

        # PAR1106
        # Option given to send invite or complete business user journey

        # INVITATION

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "New partnership application"
        And I expect that element "h1.heading-xlarge" contains the text "Notify user of partnership invitation"
        And the inputfield "#edit-email-subject" contains the text "Invitation to join the Primary Authority Register"
        And the inputfield "#edit-email-body" contains the text "[invite:invite-accept-link]"
        When I click on the button "#edit-next"

        # INVITATION CONFIRMATION

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "New partnership application"
        Then I expect that element "h1.heading-xlarge" contains the text "Notification sent"
        Then I expect that element "#block-par-theme-content" contains the text "Mr Test Revoking will receive an email with a link to register/login to the PAR website."
        When I click on the link "Done"
        # And I open the url "/user/logout"
        # And I open the url "/user/login"
        # And I add "dadmin" to the inputfield "#edit-name"
        # And I add "TestPassword" to the inputfield "#edit-pass"
        # When I click on the button "#edit-submit"
        # And I open the url "/admin/reports/maillog"
        # And I add "Invitation to join the Primary Authority Register" to the inputfield "#edit-subject"
        # And I add "usertotestrevoking@example.com" to the inputfield "#edit-header-to"
        # And I click on the button "#edit-submit-maillog-overview"
        # When I click on the button "a*=Invitation to join the Primary Authority Register"
        # Then I expect that element "h1.heading-xlarge" contains the text "Invitation to join the Primary Authority Register"
        # When I extract the invitation url
        # And I complete the invitation process
        # And I click on the link "See your partnerships"
        # Then I expect that element ".table-scroll-wrapper" contains the text "Direct Partnership For Revoking"
        And I open the url "/user/logout"

        # CHECK HD USER HAS REVOKE LINK

        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I add "Direct Partnership For Revoking" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I expect that element ".table-scroll-wrapper" contains the text "Direct Partnership For Revoking"

