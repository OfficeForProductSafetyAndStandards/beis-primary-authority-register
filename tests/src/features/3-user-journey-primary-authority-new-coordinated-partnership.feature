@ci
Feature: New Coordinated Partnership

    Scenario: New Coordinated Partnership

        # SEARCH PARTNERSHIPS

        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I expect that element "#block-par-theme-content" contains the text "Search for a partnership"
        And I expect that element "#block-par-theme-content" contains the text "See enforcement notifications"
        When I click on the link "Apply for a new partnership"
        And I click on the button "#edit-cancel"
        When I click on the link "Apply for a new partnership"

        # CHOOSE PARTNERSHIP TYPE

        And I click on authority selection if available
        And I click on the button "#edit-next"

        # CREATE NEW PARTNERSHIP FORM

        When I click on the radio "#edit-application-type-coordinated"
        And I click on the button "#edit-next"

        # CONFIRMATIONS

        When I click on the checkbox "#edit-coordinator-local-authority-suitable"
        And I click on the checkbox "#edit-suitable-nomination"
        And I click on the checkbox "#edit-written-summary-agreed"
        And I click on the checkbox "#edit-terms-local-authority-agreed"
        And I click on the button "#edit-next"

        # ADD ABOUT THE PARTNERSHIP

        Then I expect that element "h1.heading-xlarge" contains the text "New partnership application"
        When I add "About the partnership detail" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"

        # ORGANISATION NAME

        And I add "Business For Coordinated Partnership 1" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-next"

        # CONFIRM NEW PARTNERSHIP

        And I click new partnership if presented with choices

        # ADD BUSINESS DETAIL

        When I add "SE16 4NX" to the inputfield "#edit-postcode"
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        When I add "London" to the inputfield "#edit-town-city"
        When I add "London" to the inputfield "#edit-county"
        And I select the option with the text "United Kingdom" for element "#edit-country-code"
        And I select the option with the text "England" for element "#edit-nation"
        And I click on the button "#edit-next"

        # MAIN CONTACT

        When I add "Mr" to the inputfield "#edit-salutation"
        And I add "Fozzie" to the inputfield "#edit-first-name"
        And I add "Bear" to the inputfield "#edit-last-name"
        And I add "999999999" to the inputfield "#edit-work-phone"
        And I add "1111111111111" to the inputfield "#edit-mobile-phone"
        And I add "02079999999" to the inputfield "#edit-work-phone"
        And I add "078659999999" to the inputfield "#edit-mobile-phone"
        And I add "par_coordinator@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-next"

        # SAVE PARTNERSHIP APPLICATION
        And I click on the checkbox "#edit-partnership-info-agreed-authority"
        And I click on the button "#edit-save"
        And I click on the link "Log out"
