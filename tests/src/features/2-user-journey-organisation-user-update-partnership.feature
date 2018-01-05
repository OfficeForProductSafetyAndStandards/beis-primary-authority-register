@ci @PAR990 @PAR991
Feature: Business User - Manage Addresses


    Scenario: Business User - Manage Addresses

        # PARTNERSHIPS DASHBOARD

        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I open the url "/dashboard"
        Then I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I click on the link "See your partnerships"
        And I click on the link "Business For Direct Partnership 1"
        And I expect that element "h1" is not empty

        # EDIT REGISTERED ADDRESS

        When  I click on the link "edit address"
        And I clear the inputfield "#edit-address-line1"
        And I clear the inputfield "#edit-address-line2"
        And I clear the inputfield "#edit-town-city"
        And I clear the inputfield "#edit-postcode"
        And I clear the inputfield "#edit-county"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        When I add "SE16 4NX" to the inputfield "#edit-postcode"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        When I add "London" to the inputfield "#edit-town-city"
        When I add "London" to the inputfield "#edit-county"
        And I select the option with the text "England" for element "#edit-country"
        When I click on the button "#edit-save"
        Then I expect that element "#edit-registered-address" contains the text "1 Change St"
        And I expect that element "#edit-registered-address" contains the text "New Change"
        And I expect that element "#edit-registered-address" contains the text "London"
        And I expect that element "#edit-registered-address" contains the text "SE16 4NX"

#        # EDIT ABOUT THE BUSINESS

        When I click on the link "edit about the business"
        And I add "Change to the about business details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-about-business" contains the text "Change to the about business details section"

        # ADD SIC CODES

        When I click on the link "add another sic code"
        And I select the option with the text "10-0 Health and social care" for element "#edit-sic-code"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-sic-codes" contains the text "Health and social care"

        # ADD EMPLOYEES

        When I click on the link "edit number of employees"
        And I select the option with the value "250" for element "#edit-employees-band"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-employee-no" contains the text "50 to 249"

        # ADD LEGAL ENTITY - Limited Liability Partnership

        When I click on the link "add another legal entity"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Primary Authority partnership information"
        Then I expect that element "h1.heading-xlarge" contains the text "Add a legal entity for your organisation"
        Then I expect that element ".form-item-registered-name label" contains the text "Enter name of the legal entity"
        When I add "New LLP Company" to the inputfield "#edit-registered-name"
        Then I expect that element ".form-item-legal-entity-type label" contains the text "Select type of Legal Entity"

        Then I expect that element ".form-item-registered-number" is visible
        And I select the option with the text "Sole Trader" for element "#edit-legal-entity-type"
        Then I expect that element ".form-item-registered-number" is not visible
        And I select the option with the text "Other" for element "#edit-legal-entity-type"
        Then I expect that element ".form-item-registered-number" is visible
        And I select the option with the text "Registered Charities" for element "#edit-legal-entity-type"
        Then I expect that element ".form-item-registered-number" is visible
        And I select the option with the text "Limited Company" for element "#edit-legal-entity-type"
        Then I expect that element ".form-item-registered-number" is visible
        And I select the option with the text "Public Limited Company" for element "#edit-legal-entity-type"
        Then I expect that element ".form-item-registered-number" is visible
        And I select the option with the text "Limited Partnership" for element "#edit-legal-entity-type"
        Then I expect that element ".form-item-registered-number" is visible
        And I select the option with the text "Limited Liability Partnership" for element "#edit-legal-entity-type"
        Then I expect that element ".form-item-registered-number" is visible
        Then I expect that element ".form-item-registered-number label" contains the text "Provide the registration number"
        When I add "1234567890" to the inputfield "#edit-registered-number"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-legal-entities" contains the text "New LLP Company"
        Then I expect that element "#edit-legal-entities" contains the text "Limited Liability Partnership"
        Then I expect that element "#edit-legal-entities" contains the text "1234567890"

        # EDIT LEGAL ENTITY

        When I click on the link "edit legal entity"
        And I clear the inputfield "#edit-registered-name"
        When I add "Changed" to the inputfield "#edit-registered-name"
        And I select the option with the text "Limited Company" for element "#edit-legal-entity-type"
        And I clear the inputfield "#edit-registered-number"
        When I add "0123456789" to the inputfield "#edit-registered-number"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-legal-entities" contains the text "Changed"
        Then I expect that element "#edit-legal-entities" contains the text "Limited Company"

        # ADD ANOTHER LEGAL ENTITY - Sole Trader

        When I click on the link "add another legal entity"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Primary Authority partnership information"
        Then I expect that element "h1.heading-xlarge" contains the text "Add a legal entity for your organisation"
        When I add "New Sole Trader" to the inputfield "#edit-registered-name"
        And I select the option with the text "Sole Trader" for element "#edit-legal-entity-type"
        Then I expect that element ".form-item-registered-number" is not visible
        And I click on the button "#edit-save"
        Then I expect that element "#edit-legal-entities" contains the text "New Sole Trader"
        Then I expect that element "#edit-legal-entities" contains the text "Sole Trader"

        # ADD NEW TRADING NAME

        When I click on the link "add another trading name"
        Then I expect that element "h1.heading-xlarge" contains the text "Add a trading name for your organisation"
        When I add "Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-trading-names" contains the text "Different Trading Name"

        # EDIT TRADING NAME

        When I click on the link "edit trading name"
        Then I expect that element "h1.heading-xlarge" contains the text "Edit trading name for your organisation"
        When I add "Change To Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-trading-names" contains the text "Change To Different Trading Name"

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
        Then I expect that element "#edit-organisation-contacts" contains the text "Dr Harvey Kneeslapper"
        And I expect that element "#edit-organisation-contacts" contains the text "02078886666"
        And I expect that element "#edit-organisation-contacts" contains the text "07965465723 (preferred)"

        # COMPLETE CHANGES

        When I click on the button "#edit-save"
        And I click on the checkbox "#edit-partnership-info-agreed-business"
        And I click on the button "#edit-save"
        And I select the option with the value "3" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I expect that element "#block-par-theme-content" contains the text "Business For Direct Partnership 1"



