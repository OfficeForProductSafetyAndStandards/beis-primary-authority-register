@ci
Feature: Coordinator User - Complete organisation details

    @coordinatedpartneship
    Scenario: Coordinator User - Complete organisation details

        #LOGIN
        
        Given I am logged in as "par_coordinator@example.com"
        And I click the link text "See your partnerships"
        And I add "Organisation For Coordinated Partnership Two" to the inputfield "#edit-keywords"
        And I select the option with the value "confirmed_authority" for element "#edit-partnership-status-1"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click the link text "Organisation For Coordinated Partnership Two"
        And the element "h1" is not empty

        # EDIT REGISTERED ADDRESS
        
        And the element "h1.heading-xlarge" contains the text "Confirm the details about the organisation"
        And I add "Change to the about organisation details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-next"
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

        Then the element "h1.heading-xlarge" contains the text "Confirm the SIC code"
        And I select the option with the value "38" for element "#edit-sic-code"
        And I click on the button "#edit-next"

        # ADD ORGANISATION SIZE

        Then the element "h1.heading-xlarge" contains the text "Confirm the size of the membership list"
        And I select the option with the value "small" for element "#edit-business-size"
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
        And I click on the button "#edit-next"

        # REVIEW

        Then the element "h1.heading-xlarge" contains the text "Review the partnership summary information below"
        Then the element "#edit-organisation-registered-address" contains the text "1 Change St"
        And the element "#edit-organisation-registered-address" contains the text "New Change"
        And the element "#edit-organisation-registered-address" contains the text "London"
        And the element "#edit-organisation-registered-address" contains the text "SE16 4NX"
        Then the element "#edit-about-organisation" contains the text "Change to the about organisation details section"
        Then the element "#edit-sic-code" contains the text "Social care activities without accommodation"
        Then the element "#edit-legal-entities" contains the text "New LLP Company"
        Then the element "#edit-legal-entities" contains the text "Limited Liability Partnership"
        Then the element "#edit-legal-entities" contains the text "1234567890"
        And I click on the checkbox "#edit-partnership-info-agreed-business"
        And I click on the checkbox "#edit-terms-organisation-agreed"
        And I click on the button "#edit-save"

        # CHECK SEARCH PICKS UP ON TRADERS AND MEMBERS


        Then the element "h1.heading-xlarge" contains the text "Thank you for completing the application"
        And I click on the button ".button"
        And I open the path "/user/logout"
        And I open the path "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I click the link text "Search for a partnership"
        When I add "New LLP Company" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        Then the element ".table-scroll-wrapper" contains the text "Organisation For Coordinated Partnership Two"
        When I add "Coordinated Member 1" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        Then the element ".table-scroll-wrapper" contains the text "Organisation For Coordinated Partnership Two"
