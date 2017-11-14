@ci
Feature: New Direct Partnership For PA To Approve

    Scenario: New Direct Partnership

        Given I am logged in as "par_authority@example.com"
        And I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I expect that element "#block-par-theme-content" contains the text "Search for a partnership"
        And I expect that element "#block-par-theme-content" contains the text "See enforcement notifications"
        When I click on the link "Apply for a new partnership"
        And I click on the button "#edit-cancel"
        When I click on the link "Apply for a new partnership"


        # CHOOSE PARTNERSHIP TYPE

        And I click on authority selection if available
        And I click on the button "#edit-next"

        # CREATE NEW PARTNERSHIP FORM

        Then I expect that element "h1.heading-xlarge" contains the text "New partnership application"
        When I click on the radio "#edit-application-type-direct"
        And I click on the button "#edit-next"

        # CONFIRMATIONS

        When I click on the checkbox "#edit-business-eligible-for-partnership"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" is visible
        And I click on the checkbox "#edit-local-authority-suitable-for-nomination"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" is visible
        And I click on the checkbox "#edit-written-summary-agreed"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" is visible
        And I click on the checkbox "#edit-terms-organisation-agreed"
        And I click on the radio "#edit-business-regulated-by-one-authority-1"
        When I click on the button "#edit-next"
        Then I expect that element ".error-summary" is visible
        And I expect that element "#par-partnership-application-authority-checklist" contains the text "Is this your local authority?"
        And I click on the radio "#edit-business-regulated-by-one-authority-1"
        And I click on the radio "#edit-is-local-authority-1"
  #        And I expect that element ".error-summary" contains the text "The business needs to be informed about local authority"
        When I click on the button "#edit-next"
        Then I expect that element "error-summary" is not visible

        # ADD ABOUT THE PARTNERSHIP

        And I expect that element "#par-partnership-about" contains the text "Use this section to give a brief overview of the partnership"
        When I add "About the partnership detail" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"

        # ORGANISATION NAME

        And I add "Business For Direct Partnership 1" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-next"
#        And I click on the radio ".form-radio"
#        And I click on the button "#edit-next"

        # CONFIRM NEW PARTNERSHIP

        And I click new partnership if presented with choices
  #        And I click on the radio "#edit-par-data-organisation-id-new"
  #        And I click on the button "#edit-next"

        # ADD BUSINESS DETAIL

        When I add "SE16 4NX" to the inputfield "#edit-postcode"
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        When I add "London" to the inputfield "#edit-town-city"
        When I add "London" to the inputfield "#edit-county"
        And I select the option with the text "England" for element "#edit-country"
        And I click on the button "#edit-next"

        # MAIN CONTACT

        When I add "Mr" to the inputfield "#edit-salutation"
        And I add "Fozzie" to the inputfield "#edit-first-name"
        And I add "Bear" to the inputfield "#edit-last-name"
        And I add "999999999" to the inputfield "#edit-work-phone"
        And I add "1111111111111" to the inputfield "#edit-mobile-phone"
        And I add "02079999999" to the inputfield "#edit-work-phone"
        And I add "078659999999" to the inputfield "#edit-mobile-phone"
        And I add "par_business@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-next"
        And I click on the checkbox "#edit-partnership-info-agreed-authority"
        And I click on the button "#edit-save"

        # EMAIL

        Then the inputfield "#edit-email-subject" contains the text "Invitation to join the Primary Authority Register"
        When I click on the button "#edit-next"
