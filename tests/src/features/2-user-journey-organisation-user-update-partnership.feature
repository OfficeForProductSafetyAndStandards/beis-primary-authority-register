@ci @Bug @PAR990 @PAR991
Feature: Business User - Manage Addresses


    Scenario: Business User - Manage Addresses

        # PARTNERSHIPS DASHBOARD

        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And the element "#edit-submit" is enabled
        When I click on the button "#edit-submit"
        And I open the url "/dashboard"
        Then I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I click on the link "See your partnerships"
        # And I select the option with the text "Confirmed by Organisation" for element "#edit-partnership-status"
        And I add "Business For Direct Partnership" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click on the link "Business For Direct Partnership"
        And I run tota11y against the current page
        And I expect that element "h1" is empty

        # EDIT REGISTERED ADDRESS

        When  I click on the link "edit address"
        And I run tota11y against the current page
        And I clear the inputfield "#edit-address-line1"
        And I clear the inputfield "#edit-address-line2"
        And I clear the inputfield "#edit-town-city"
        And I clear the inputfield "#edit-postcode"
        And I clear the inputfield "#edit-county"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        When I add "SE16 4NX" to the inputfield "#edit-postcode"
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        When I add "London" to the inputfield "#edit-town-city"
        When I add "London" to the inputfield "#edit-county"
        And I select the option with the text "United Kingdom" for element "#edit-country-code"
        And I select the option with the text "England" for element "#edit-nation"
        When I click on the button "#edit-save"
        And I run tota11y against the current page
        Then I expect that element "#edit-registered-address" contains the text "1 Change St"
        And I expect that element "#edit-registered-address" contains the text "New Change"
        And I expect that element "#edit-registered-address" contains the text "London"
        And I expect that element "#edit-registered-address" contains the text "SE16 4NX"

#        # EDIT ABOUT THE BUSINESS

        When I click on the link "edit about the business"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Primary Authority partnership information"
        Then I expect that element "h1.heading-xlarge" contains the text "Information about the business"
        When I run tota11y against the current page
        And I add "Change to the about business details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-about-business" contains the text "Change to the about business details section"

        # ADD SIC CODES
        And I run tota11y against the current page
        When I click on the link "add another sic code"
        And I select the option with the text "10.0 - Health and social care" for element "#edit-sic-code"
        And I click on the button "#edit-save"
        And I run tota11y against the current page
        Then I expect that element "#edit-sic-codes" contains the text "Health and social care"

        # ADD EMPLOYEES

        When I click on the link "edit number of employees"
        And I select the option with the value "250" for element "#edit-employees-band"
        And I click on the button "#edit-save"
        And I run tota11y against the current page
        Then I expect that element "#edit-employee-no" contains the text "50 to 249"

        # ADD NEW TRADING NAME

        When I click on the link "add another trading name"
        Then I expect that element "h1.heading-xlarge" contains the text "Add a trading name for your organisation"
        When I add "Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        And I run tota11y against the current page
        Then I expect that element "#edit-trading-names" contains the text "Different Trading Name"

        # EDIT TRADING NAME

        When I click on the link "edit trading name"
        Then I expect that element "h1.heading-xlarge" contains the text "Edit trading name for your organisation"
        When I add "Change To Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        And I run tota11y against the current page
        Then I expect that element "#edit-trading-names" contains the text "Change To Different Trading Name"

        # ADD ORGANISATION CONTACT

        When I click on the link "add another organisation contact"
        And I add "Mr" to the inputfield "#edit-salutation"
        And I add "Added" to the inputfield "#edit-first-name"
        And I add "Contact" to the inputfield "#edit-last-name"
        And I add "02084445555" to the inputfield "#edit-work-phone"
        And I add "07865223222" to the inputfield "#edit-mobile-phone"
        And I add "Some additional notes for newly added contact" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        And I run tota11y against the current page
        Then I expect that element "#edit-organisation-contacts" contains the text "Mr Added Contact"
        And I expect that element "#edit-organisation-contacts" contains the text "02084445555"
        And I expect that element "#edit-organisation-contacts" contains the text "07865223222"

        # EDIT MAIN BUSINESS CONTACT

        When I click on the link "edit organisation contact"
        And I clear the inputfield "#edit-salutation"
        And I clear the inputfield "#edit-first-name"
        And I clear the inputfield "#edit-last-name"
        And I clear the inputfield "#edit-work-phone"
        And I clear the inputfield "#edit-mobile-phone"
        And I clear the inputfield "#edit-notes"
        And I add "Dr" to the inputfield "#edit-salutation"
        And I add "Harvey" to the inputfield "#edit-first-name"
        And I add "Kneeslapper" to the inputfield "#edit-last-name"
        And I add "02078886666" to the inputfield "#edit-work-phone"
        And I add "07965465723" to the inputfield "#edit-mobile-phone"
        And I check the checkbox "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        And I run tota11y against the current page
        Then I expect that element "#edit-organisation-contacts" contains the text "Dr Harvey Kneeslapper"
        And I expect that element "#edit-organisation-contacts" contains the text "02078886666"
        And I expect that element "#edit-organisation-contacts" contains the text "07965465723 (preferred)"

        # COMPLETE CHANGES

        When I click on the button "#edit-save"
        And I run tota11y against the current page
        And I select the option with the value "3" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I expect that element "#block-par-theme-content" contains the text "Business For Direct Partnership"
        And I run tota11y against the current page
