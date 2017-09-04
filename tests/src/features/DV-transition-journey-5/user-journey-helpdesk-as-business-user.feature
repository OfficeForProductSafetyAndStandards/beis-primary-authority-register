@ci @journey5
Feature: Helpdesk As Business User - Manage Addresses

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Helpdesk As Business User - Manage Addresses
        # LOGIN SCREEN

        Given I am logged in as "par_helpdesk@example.com"

        # HD DASHBOARD

        Then I expect that element "h1" contains the text "RD Helpdesk Dashboard"
        When I add "ABCD" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-rd-helpdesk-dashboard"
        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
        When I click on the button "td.views-field.views-field-organisation-name a"

        # PARTNERSHIP TASKS

        When I click on the link "Review and confirm your business details"
        Then I expect that element "#edit-about-business" contains the text "About the business"
        And I expect that element "#edit-legal-entity" contains the text "Legal Entities"
        And I expect that element "#edit-primary-contact" contains the text "Main business contact"
        And I expect that element "#edit-0" contains the text "Trading Names"

        # CHANGE NAME AND SUMMARY

        When I click on the link "edit"
        And I add "Change to the about business details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-about-business" contains the text "Change to the about business details section"
        When I click on the button "form#par-flow-transition-business-details #edit-0.js-form-item a.flow-link"
        And I add "Trading Name Change" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        Then I expect that element "#par-flow-transition-business-details" contains the text "Trading Name Change"
        When I click on the link "add another trading name"
        And I click on the button "#edit-next"
        And I add "Trading Name Add" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-next"
        Then I expect that element "#par-flow-transition-business-details" contains the text "Trading Name Add"

        # EDIT REGISTERED ADDRESS

        When  I click on the button "form#par-flow-transition-business-details #edit-primary-address a.flow-link"
        And I clear the inputfield "#edit-address-line1"
        And I clear the inputfield "#edit-address-line2"
        And I clear the inputfield "#edit-town-city"
        And I clear the inputfield "#edit-postcode"
        And I clear the inputfield "#edit-county"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        And I add "change" to the inputfield "#edit-postcode"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        And I add "New Change State" to the inputfield "#edit-town-city"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        And I add "US-CH" to the inputfield "#edit-county"
        And I select the option with the text "Wales" for element "#edit-country"
        When I click on the button "#edit-next"
        Then I expect that element "span.address-line1" contains the text "1 Change St"
        And I expect that element "span.address-line2" contains the text "New Change"
        And I expect that element "span.locality" contains the text "New Change State"
        And I expect that element "span.postal-code" contains the text "change"
        And I expect that element "#edit-primary-address" contains the text "Wales"

        # EDIT MAIN BUSINESS CONTACT

        When I click on the button "form#par-flow-transition-business-details #edit-primary-contact a.flow-link"
        And I add "Fozzie" to the inputfield "#edit-first-name"
        And I add "Bear" to the inputfield "#edit-last-name"
        And I add "999999999" to the inputfield "#edit-work-phone"
        And I add "1111111111111" to the inputfield "#edit-mobile-phone"
        And I add "02079999999" to the inputfield "#edit-work-phone"
        And I add "078659999999" to the inputfield "#edit-mobile-phone"
        And I add "par_business_change@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-primary-contact" contains the text "Fozzie"
        And I expect that element "#edit-primary-contact" contains the text "Bear"
        And I expect that element "#edit-primary-contact" contains the text "par_business_change@example.com"
        And I expect that element "#edit-primary-contact" contains the text "2079999999"
        And I expect that element "#edit-primary-contact" contains the text "78659999999"

        # EDIT ALTERNATE CONTACT

        When I click on the button "form#par-flow-transition-business-details #edit-alternative-people a.flow-link"
        And I add "Professor" to the inputfield "#edit-first-name"
        And I add "Hastings" to the inputfield "#edit-last-name"
        And I add "01723999999" to the inputfield "#edit-work-phone"
        And I add "08654999999" to the inputfield "#edit-mobile-phone"
        And I add "par_business_change@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-alternative-people" contains the text "Professor"
        And I expect that element "#edit-alternative-people" contains the text "Hastings"
        And I expect that element "#edit-alternative-people" contains the text "par_business_change@example.com"
        And I expect that element "#edit-alternative-people" contains the text "01723999999"
        And I expect that element "#edit-alternative-people" contains the text "08654999999"

        # EDIT LEGAL ENTITIES

        When I click on the button "form#par-flow-transition-business-details #edit-legal-entity a.flow-link"
        And I add "Legal Entity Change" to the inputfield "#edit-registered-name"
        And I select the option with the text "Limited Company" for element "#edit-legal-entity-type"
        And I add "987654321" to the inputfield "#edit-company-house-no"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-legal-entity div" contains the text "Legal Entity Change"
        And I expect that element "#edit-legal-entity" contains the text "987654321"
        And I expect that element "#edit-legal-entity" contains the text "Limited Company"

        # ADD LEGAL ENTITIES

        When I click on the link "add another legal entity"
        And I click on the button "#edit-next"
        And I add "Another Legal Entity" to the inputfield "#edit-registered-name"
        And I select the option with the text "Sole Trader" for element "#edit-legal-entity-type"
        And I click on the button "#edit-next"
        Then I expect that element "#par-flow-transition-business-details" contains the text "Another Legal Entity"
        And I expect that element "#par-flow-transition-business-details" contains the text "Sole Trader"

        # GO BACK TO PARTNERSHIPS DASHBOARD

        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"
        Then I expect that element "#block-par-theme-content" contains the text "Confirmed by the Organisation"
        And I expect that element ".table-scroll-wrapper" not contains the text "Invite the business to confirm their details"
        And I expect that element ".table-scroll-wrapper" not contains the text "Review and confirm your inspection plan"
        And I expect that element ".table-scroll-wrapper" not contains the text "Review and confirm your documentation"
