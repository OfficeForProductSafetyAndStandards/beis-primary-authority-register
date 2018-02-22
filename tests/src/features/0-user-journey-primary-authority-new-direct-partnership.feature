@ci @PAR1034 @test
Feature: New Direct Partnership For PA To Approve

    Scenario: New Direct Partnership
        Given I open the url "/user/login"
        And I set "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element "h1.heading-xlarge" contains the text "Primary Authority Register"
        And I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I expect that element "#block-par-theme-content" contains the text "Search for a partnership"
        And I expect that element "#block-par-theme-content" contains the text "See enforcement notifications"

        # PARTNERSHIP APPLY

        When I click on the link "Apply for a new partnership"
        And I click on the button "#edit-cancel"
        When I click on the link "Apply for a new partnership"
        And I run tota11y against the current page
        Then I expect that element "h1.heading-xlarge" contains the text "New partnership application"
        And I expect that element "h1.heading-xlarge" contains the text "Which authority are you acting on behalf of?"
        When I click on the radio "label*=Metropolitan District"
        And I click on the button "#edit-next"
        When I click on the radio "#edit-application-type-direct"
        And I click on the button "#edit-next"
        When I click on the checkbox "#edit-business-eligible-for-partnership"
        And I click on the checkbox "#edit-local-authority-suitable-for-nomination"
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
        When I click on the button "#edit-next"
        Then I expect that element "error-summary" is not visible

        # ADD ABOUT THE PARTNERSHIP

        And I expect that element "#par-partnership-about" contains the text "Use this section to give a brief overview of the partnership"
        When I add "About the partnership detail" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"

        # ORGANISATION NAME

        And I add "Business For Direct Partnership" to the inputfield "#edit-organisation-name"
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
        And I add "par_business@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-next"

        # REVIEW THE APPLICATION

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "New partnership application"
        Then I expect that element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        When I click on the checkbox "#edit-partnership-info-agreed-authority"
        And I click on the button "#edit-save"

        # INVITATION

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "New partnership application"
        Then I expect that element "h1.heading-xlarge" contains the text "Notify user of partnership invitation"
        Then the inputfield "#edit-email-subject" contains the text "Invitation to join the Primary Authority Register"
        Then the inputfield "#edit-email-body" contains the text "[invite:invite-accept-link]"
        When I click on the button "#edit-next"

        # INVITATION CONFIRMATION

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "New partnership application"
        Then I expect that element "h1.heading-xlarge" contains the text "Notification sent"
        Then I expect that element "#block-par-theme-content" contains the text "Mr Fozzie Bear will receive an email with a link to register/login to the PAR website."
        When I click on the link "Done"

        # RETURN TO DASHBOARD

        Then I expect that element "h1.heading-xlarge" contains the text "Primary Authority Register"
