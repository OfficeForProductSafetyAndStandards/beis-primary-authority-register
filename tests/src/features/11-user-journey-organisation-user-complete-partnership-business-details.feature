@ci @test
Feature: Business User - Complete organisation details

    Scenario: Business User - Complete organisation details

        # PARTNERSHIPS DASHBOARD

        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I open the url "/dashboard"
        Then I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I click on the link "See your partnerships"
        # And I select the option with the text "Confirmed by Authority" for element "#edit-partnership-status"
        And I add "Organisation For Direct Partnership" to the inputfield "#edit-keywords"
        And I select the option with the text "Confirmed by the Authority" for element "#edit-partnership-status-1"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click on the link "Organisation For Direct Partnership"
        And I expect that element "h1" is not empty

        # EDIT ABOUT THE BUSINESS
        
        And I expect that element "h1.heading-xlarge" contains the text "Confirm the details about the organisation"
        And I add "Some information about organisation details" to the inputfield "#edit-about-business"
        When I click on the button "#edit-next"

        # EDIT REGISTERED ADDRESS

        Then I expect that element "h1.heading-xlarge" contains the text "Confirm the primary address details"
        And I clear the inputfield "#edit-address-line1"
        And I clear the inputfield "#edit-address-line2"
        And I clear the inputfield "#edit-town-city"
        And I clear the inputfield "#edit-postcode"
        And I clear the inputfield "#edit-county"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" does exist
        When I add "SE16 4NX" to the inputfield "#edit-postcode"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" does exist
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I add "London" to the inputfield "#edit-town-city"
        And I add "London" to the inputfield "#edit-county"
        And I select the option with the text "United Kingdom" for element "#edit-country-code"
        And I select the option with the text "England" for element "#edit-nation"
        When I click on the button "#edit-next"
        Then I expect that element "h1.heading-xlarge" contains the text "Confirm the primary contact details"
        When I click on the button "#edit-next"

        # ADD SIC CODES
        
        And I select the option with the text "10.0 - Health and social care" for element "#edit-sic-code"
        And I click on the button "#edit-next"

        # ADD EMPLOYEES
        
        And I select the option with the value "250" for element "#edit-employees-band"
        And I click on the button "#edit-next"
        
        # ADD TRADING NAME
        
        Then I expect that element "h1.heading-xlarge" contains the text "Confirm the trading name"
        When I add "Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        
        # ADD LEGAL ENTITY

        Then I expect that element "h1.heading-xlarge" contains the text "Confirm the legal entity"
        When I add "New LLP Company" to the inputfield "#edit-par-component-legal-entity-0-registered-name"
        And I select the option with the text "Limited Liability Partnership" for element "#edit-par-component-legal-entity-0-legal-entity-type"
        Then I expect that element ".form-item-par-component-legal-entity-0-registered-number label" contains the text "Provide the registration number"
        When I add "1234567890" to the inputfield "#edit-par-component-legal-entity-0-registered-number"
        When I click on the button "#edit-add-another"
        When I add "First Sole Trader" to the inputfield "#edit-par-component-legal-entity-0-registered-name"
        And I select the option with the text "Sole Trader" for element "#edit-par-component-legal-entity-1-legal-entity-type"
        Then I expect that element "#edit-par-component-legal-entity-1-registered-number" is not visible
        When I click on the button "#edit-add-another"
        When I add "Second New LLP Company" to the inputfield "#edit-par-component-legal-entity-2-registered-name"
        And I select the option with the text "Limited Liability Partnership" for element "#edit-par-component-legal-entity-2-legal-entity-type"
        When I add "0000000000" to the inputfield "#edit-par-component-legal-entity-2-registered-number"
        When I click on the button "#edit-par-component-legal-entity-2-remove"
        When I click on the button "#edit-next"

        # REVIEW PARTNERSHIP

        Then I expect that element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        And I expect that element "#edit-organisation-name" contains the text "Organisation For Direct Partnership"
        And I expect that element "#edit-organisation-registered-address" contains the text "1 Change St"
        And I expect that element "#edit-organisation-registered-address" contains the text "New Change"
        And I expect that element "#edit-organisation-registered-address" contains the text "London"
        And I expect that element "#edit-organisation-registered-address" contains the text "SE16 4NX"
        And I expect that element "#edit-about-organisation" contains the text "Some information about organisation details"
        And I expect that element "#edit-sic-code" contains the text "Health and social care"
#        Then I expect that element "#edit-number-employees" contains the text "50 to 249"
        And I expect that element "#edit-legal-entities" contains the text "New LLP Company"
        And I expect that element "#edit-legal-entities" contains the text "Limited Liability Partnership"
        And I expect that element "#edit-legal-entities" contains the text "1234567890"
        And I expect that element "#edit-legal-entities" contains the text "First Sole Trader"
        And I expect that element "#edit-legal-entities" not contains the text "Second New LLP Company"
        And I expect that element "#edit-legal-entities" not contains the text "0000000000"


        # CHANGE ABOUT BUSINESS

        When  I click on the link "Change the details about this partnership"
        Then I expect that element "h1.heading-xlarge" contains the text "Confirm the details about the organisation"
        When I clear the inputfield "#edit-about-business"
        And I add "Change to the information about organisation details" to the inputfield "#edit-about-business"
        And I click on the button "#edit-next"
        Then I expect that element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        And I expect that element "#edit-about-organisation" contains the text "Change to the information about organisation details"
        And I expect that element "#edit-about-organisation" not contains the text "Some information about organisation details"

        # CHANGE LEGAL ENTITIES

        When  I click on the link "Change the new legal entities"
        Then I expect that element "h1.heading-xlarge" contains the text "Confirm the legal entity"
        When I click on the button "#edit-par-component-legal-entity-1-remove"
        And I clear the inputfield "#edit-par-component-legal-entity-0-registered-name"
        And I add "Changed to Public Company" to the inputfield "#edit-par-component-legal-entity-0-registered-name"
        And I select the option with the text "Public Limited Company" for element "#edit-par-component-legal-entity-0-legal-entity-type"
        Then I expect that element ".form-item-par-component-legal-entity-0-registered-number label" contains the text "Provide the registration number"
        When I clear the inputfield "#edit-par-component-legal-entity-0-registered-number"
        And I add "55555555555" to the inputfield "#edit-par-component-legal-entity-0-registered-number"
        And I click on the button "#edit-next"
        Then I expect that element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        Then I expect that element "#edit-legal-entities" contains the text "Changed to Public Company"
        Then I expect that element "#edit-legal-entities" contains the text "55555555555"
        Then I expect that element "#edit-legal-entities" not contains the text "New LLP Company"
        Then I expect that element "#edit-legal-entities" not contains the text "First Sole Trader"

        # CONFIRMATION

        When I click on the button "#edit-save"
        Then I expect that element ".error-summary" is visible
        And I click on the checkbox "#edit-partnership-info-agreed-business"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" is visible
        And I click on the checkbox "#edit-terms-organisation-agreed"
        And I click on the button "#edit-save"
        Then I expect that element "h1.heading-xlarge" contains the text "Thank you for completing the application"
        When I click on the button ".button"
        And I add "Organisation For Direct Partnership" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        # And I click on the link "Organisation For Direct Partnership"
        Then I expect that element ".table-scroll-wrapper" contains the text "Confirmed by the Organisation"
