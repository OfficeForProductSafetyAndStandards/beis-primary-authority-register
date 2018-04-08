@ci @test
Feature: Business User - Complete organisation details

    Scenario: Business User - Complete organisation details

        #LOGIN

        Given I am logged in as "par_business@example.com"
        And I open the path "/dashboard"
        And I click the link text "See your partnerships"
        # And I select the option with the value "Confirmed by Authority" for element "#edit-partnership-status"
        And I add "Organisation For Direct Partnership One" to the inputfield "#edit-keywords"
        And I select the option with the value "confirmed_authority" for element "#edit-partnership-status-1"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click the link text "Organisation For Direct Partnership One"
        And the element "h1" is not empty

        # EDIT ABOUT THE BUSINESS
        
        And the element "h1.heading-xlarge" contains the text "Confirm the details about the organisation"
        And I add "Some information about organisation details" to the inputfield "#edit-about-business"
        When I click on the button "#edit-next"

        # EDIT REGISTERED ADDRESS

        Then the element "h1.heading-xlarge" contains the text "Confirm the primary address details"
        # And I click on the button "#edit-next"
        # Then the element ".error-summary" is visible
        When I clear the inputfield "#edit-postcode"
        And I click on the button "#edit-next"
        Then the element ".error-summary" is visible
        When I add "SE16 4NX" to the inputfield "#edit-postcode"
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I add "London" to the inputfield "#edit-town-city"
        And I add "London" to the inputfield "#edit-county"
        And I select the option with the value "GB" for element "#edit-country-code"
        And I select the option with the value "GB-ENG" for element "#edit-nation"
        When I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Confirm the primary contact details"
        When I click on the button "#edit-next"

        # ADD SIC CODES
        
        And I select the option with the value "38" for element "#edit-sic-code"
        And I click on the button "#edit-next"

        # ADD EMPLOYEES
        
        And I select the option with the value "250" for element "#edit-employees-band"
        And I click on the button "#edit-next"
        
        # ADD TRADING NAME
        
        Then the element "h1.heading-xlarge" contains the text "Confirm the trading name"
        When I add "Different Trading Name" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        
        # ADD LEGAL ENTITY

        Then the element "h1.heading-xlarge" contains the text "Confirm the legal entity"
        When I add "New LLP Company" to the inputfield "#edit-par-component-legal-entity-0-registered-name"
        And I select the option with the value "limited_liability_partnership" for element "#edit-par-component-legal-entity-0-legal-entity-type"
        Then the element ".form-item-par-component-legal-entity-0-registered-number label" contains the text "Provide the registration number"
        When I add "1234567890" to the inputfield "#edit-par-component-legal-entity-0-registered-number"
        When I click on the button "#edit-add-another"
        When I add "First Sole Trader" to the inputfield "#edit-par-component-legal-entity-1-registered-name"
        And I select the option with the value "sole_trader" for element "#edit-par-component-legal-entity-1-legal-entity-type"
        When I click on the button "#edit-add-another"
        When I add "Second New LLP Company" to the inputfield "#edit-par-component-legal-entity-2-registered-name"
        And I select the option with the value "limited_liability_partnership" for element "#edit-par-component-legal-entity-2-legal-entity-type"
        When I add "0000000000" to the inputfield "#edit-par-component-legal-entity-2-registered-number"
        When I click on the button "#edit-par-component-legal-entity-2-remove"
        When I click on the button "#edit-next"

        # REVIEW PARTNERSHIP

        Then the element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        And the element "#edit-organisation-name" contains the text "Organisation For Direct Partnership One"
        And the element "#edit-organisation-registered-address" contains the text "1 Change St"
        And the element "#edit-organisation-registered-address" contains the text "New Change"
        And the element "#edit-organisation-registered-address" contains the text "London"
        And the element "#edit-organisation-registered-address" contains the text "SE16 4NX"
        And the element "#edit-about-organisation" contains the text "Some information about organisation details"
        And the element "#edit-sic-code" contains the text "Social care activities without accommodation"
#        Then the element "#edit-number-employees" contains the text "50 to 249"
        And the element "#edit-legal-entities" contains the text "New LLP Company"
        And the element "#edit-legal-entities" contains the text "Limited Liability Partnership"
        And the element "#edit-legal-entities" contains the text "1234567890"
        And the element "#edit-legal-entities" contains the text "First Sole Trader"
        And the element "#edit-legal-entities" does not contain the text "Second New LLP Company"

        # CHANGE ABOUT BUSINESS

        When I click the link text "Change the details about this partnership"
        Then the element "h1.heading-xlarge" contains the text "Confirm the details about the organisation"
        And I add "Change to the information about organisation details" to the inputfield "#edit-about-business"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        And the element "#edit-about-organisation" contains the text "Change to the information about organisation details"
        And the element "#edit-about-organisation" does not contain the text "Some information about organisation details"

        # CHANGE LEGAL ENTITIES

        When  I click the link text "Change the new legal entities"
        Then the element "h1.heading-xlarge" contains the text "Confirm the legal entity"
        When I click on the button "#edit-par-component-legal-entity-1-remove"
        And I add "Changed to Public Company" to the inputfield "#edit-par-component-legal-entity-0-registered-name"
        And I select the option with the value "public_limited_company" for element "#edit-par-component-legal-entity-0-legal-entity-type"
        Then the element ".form-item-par-component-legal-entity-0-registered-number label" contains the text "Provide the registration number"
        And I add "55555555558" to the inputfield "#edit-par-component-legal-entity-0-registered-number"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        Then the element "#edit-legal-entities" contains the text "Changed to Public Company"
        Then the element "#edit-legal-entities" contains the text "55555555558"
        Then the element "#edit-legal-entities" does not contain the text "New LLP Company"
        Then the element "#edit-legal-entities" does not contain the text "First Sole Trader"

        # CONFIRMATION

        When I click on the button "#edit-save"
        Then the element ".error-summary" is visible
        And I click on the checkbox "#edit-partnership-info-agreed-business"
        And I click on the button "#edit-save"
        Then the element ".error-summary" is visible
        And I click on the checkbox "#edit-terms-organisation-agreed"
        And I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Thank you for completing the application"
        When I click on the button ".button"
        And I add "Organisation For Direct Partnership One" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        # And I click the link text "Organisation For Direct Partnership One"
        Then the element ".table-scroll-wrapper" contains the text "Confirmed by the Organisation"
