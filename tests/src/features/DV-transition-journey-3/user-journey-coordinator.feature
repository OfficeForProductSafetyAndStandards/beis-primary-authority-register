@ci @journey3 @deprecated
Feature: Coordinator User - Manage Addresses

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Coordinator User - Manage Addresses
        # LOGIN SCREEN

        Given I am logged in as "par_coordinator@example.com"
        And I open the url "/dashboard"
        When I click on the link "See your partnerships"

        # PARTNERSHIPS DASHBOARD
        When I select my next coordinated partnership awaiting review

        # TERMS AND CONDITIONS SCREEN

        And I expect that element ".par-flow-transition-coordinator-terms" contains the text "Please review the new Primary Authority terms and conditions and confirm that you agree with them"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" contains the text "You must agree to the new terms and conditions"
        When I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-save"
        # And I scroll to element ".table-scroll-wrapper"
        And I click on the link "Review and confirm your association details"

        # REVIEW ASSOCIATION DETAILS

        Then I expect that element "#edit-business-name" contains the text "Co Mart"
        And I expect that element "#edit-about-business" contains the text "About the business"
        And I expect that element "#edit-legal-entity" contains the text "Legal Entities"
        And I expect that element "#edit-primary-contact" contains the text "Main business contact"
        And I expect that element "#edit-0" contains the text "Trading Names"

        # EDIT REGISTERED ADDRESS

        And I click on the button "form#par-flow-transition-coordinator-details #edit-primary-address a.flow-link"
        And I add "Change Postcode" to the inputfield "#edit-postcode"
        And I add "1 Change Road" to the inputfield "#edit-address-line1"
        And I add "A Change" to the inputfield "#edit-address-line2"
        And I add "Change Town" to the inputfield "#edit-town-city"
        And I add "Change County" to the inputfield "#edit-county"
        And I select the option with the text "Wales" for element "#edit-country"
        When I click on the button "#edit-save"
        Then I expect that element "span.address-line1" contains the text "1 Change Road"
        And I expect that element "span.address-line2" contains the text "A Change"
        And I expect that element "span.locality" contains the text "Change Town"
        And I expect that element "span.postal-code" contains the text "Change Postcode"
        And I expect that element "#edit-primary-address" contains the text "Wales"

        # EDIT MAIN BUSINESS CONTACT

        When I click on the button "form#par-flow-transition-coordinator-details #edit-primary-contact a.flow-link"
        And I add "Jim" to the inputfield "#edit-first-name"
        And I add "Henson" to the inputfield "#edit-last-name"
        And I add "999999999" to the inputfield "#edit-work-phone"
        And I add "1111111111111" to the inputfield "#edit-mobile-phone"
        And I add "02079999999" to the inputfield "#edit-work-phone"
        And I add "078659999999" to the inputfield "#edit-mobile-phone"
        And I add "par_coordinator_change@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-primary-contact" contains the text "Jim"
        And I expect that element "#edit-primary-contact" contains the text "Henson"
        And I expect that element "#edit-primary-contact" contains the text "par_coordinator_change@example.com"
        And I expect that element "#edit-primary-contact" contains the text "2079999999"
        And I expect that element "#edit-primary-contact" contains the text "78659999999"

        # EDIT ALTERNATE CONTACT

        When I click on the button "form#par-flow-transition-coordinator-details #edit-alternative-people a.flow-link"
        And I add "Frank" to the inputfield "#edit-first-name"
        And I add "Oz" to the inputfield "#edit-last-name"
        And I add "01723999999" to the inputfield "#edit-work-phone"
        And I add "08654999999" to the inputfield "#edit-mobile-phone"
        And I add "par_coordinator_change@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-alternative-people" contains the text "Frank"
        And I expect that element "#edit-alternative-people" contains the text "Oz"
        And I expect that element "#edit-alternative-people" contains the text "par_coordinator_change@example.com"
        And I expect that element "#edit-alternative-people" contains the text "01723999999"
        And I expect that element "#edit-alternative-people" contains the text "08654999999"

        # EDIT LEGAL ENTITIES

        When I click on the button "form#par-flow-transition-coordinator-details #edit-legal-entity a.flow-link"
        And I add "Co Mart Change" to the inputfield "#edit-registered-name"
        And I select the option with the text "Limited Company" for element "#edit-legal-entity-type"
        And I add "987654321" to the inputfield "#edit-company-house-no"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-legal-entity" contains the text "Co Mart Change"
        And I expect that element "#edit-legal-entity" contains the text "987654321"
        And I expect that element "#edit-legal-entity" contains the text "Limited Company"

        # ADD LEGAL ENTITIES

        When I click on the link "add another legal entity"
        And I add "Another Legal Entity" to the inputfield "#edit-registered-name"
        And I select the option with the text "Sole Trader" for element "#edit-legal-entity-type"
        And I click on the button "#edit-save"
        Then I expect that element "#par-flow-transition-coordinator-details" contains the text "Another Legal Entity"
        And I expect that element "#par-flow-transition-coordinator-details" contains the text "Sole Trader"

        # CHANGE NAME AND SUMMARY

        Then I expect that element "#par-flow-transition-coordinator-details" contains the text "About the business"
        And I expect that element "#par-flow-transition-coordinator-details" contains the text "Registered address"
        And I expect that element "#par-flow-transition-coordinator-details" contains the text "Legal Entities"
        And I expect that element "#par-flow-transition-coordinator-details" contains the text "Trading Names"
        When I click on the link "edit"
        And I add "Change to the about association details section" to the inputfield "#edit-about-business"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-about-business" contains the text "Change to the about association details section"
        When I click on the button "form#par-flow-transition-coordinator-details #edit-0.js-form-item a.flow-link"
        And I add "Trading Name Change" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then I expect that element "#par-flow-transition-coordinator-details" contains the text "Trading Name Change"
        When I click on the link "add another trading name"
        And I add "Trading Name Add" to the inputfield "#edit-trading-name"
        And I click on the button "#edit-save"
        Then I expect that element "#par-flow-transition-coordinator-details" contains the text "Trading Name Add"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-save"

        # PARTNERSHIP DASHBOARD

        Then I expect that element "#block-par-theme-content" contains the text "Confirmed by the Organisation"
        And I expect that element ".table-scroll-wrapper" not contains the text "Invite the business to confirm their details"
        And I expect that element ".table-scroll-wrapper" not contains the text "Review and confirm your inspection plan"
        And I expect that element ".table-scroll-wrapper" not contains the text "Review and confirm your documentation"


