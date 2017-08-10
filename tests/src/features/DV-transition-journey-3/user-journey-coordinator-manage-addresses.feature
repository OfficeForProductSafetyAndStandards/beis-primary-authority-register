@ci @journey3
Feature: Coordinator User - Manage Addresses

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: Manage business name and summary
        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I add "par_coordinator@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible
        When I click on the button ".button-start"

        # PARTNERSHIPS DASHBOARD
        And I scroll to element ".table-scroll-wrapper"
        And I click on the link "Co Mart"

        # TERMS AND CONDITIONS SCREEN

        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Review and confirm your business details"
        Then the element "#edit-about-business" contains the text "About the business"
        And the element "#edit-legal-entity" contains the text "Legal Entities"
        And the element "#edit-primary-contact" contains the text "Main business contact"
        And the element "#edit-0" contains the text "Trading Names"

        # EDIT REGISTERED ADDRESS

        And I click on the button "form#par-flow-transition-business-details #edit-registered-address a.flow-link"
        And I add "Change Postcode" to the inputfield "#edit-postcode"
        And I add "1 Change Road" to the inputfield "#edit-address-line1"
        And I add "A Change" to the inputfield "#edit-address-line2"
        And I add "Change Town" to the inputfield "#edit-town-city"
        And I add "Change County" to the inputfield "#edit-county"
        And I select the option with the text "Wales" for element "#edit-country"
        And I click on the button "#edit-next"
        Then the element "span.address-line1" contains the text "1 Change Road"
        Then the element "span.address-line2" contains the text "A Change"
        Then the element "span.locality" contains the text "Change Town"
        Then the element "span.postal-code" contains the text "Change Postcode"
#        Then the element "span.country" contains the text "Change Country"

        # EDIT MAIN BUSINESS CONTACT

        When I click on the button "form#par-flow-transition-business-details #edit-primary-contact a.flow-link"
        And I add "Jim" to the inputfield "#edit-first-name"
        And I add "Henson" to the inputfield "#edit-last-name"
        And I add "999999999" to the inputfield "#edit-work-phone"
        And I add "1111111111111" to the inputfield "#edit-mobile-phone"
        And I add "02079999999" to the inputfield "#edit-work-phone"
        And I add "078659999999" to the inputfield "#edit-mobile-phone"
        And I add "par_business_change@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-next"
        Then the element "#edit-primary-contact" contains the text "Jim"
        Then the element "#edit-primary-contact" contains the text "Henson"
        And the element "#edit-primary-contact" contains the text "par_business_change@example.com"
        And the element "#edit-primary-contact" contains the text "2079999999"
        And the element "#edit-primary-contact" contains the text "78659999999"

        # EDIT ALTERNATE CONTACT

        When I click on the button "form#par-flow-transition-business-details #edit-primary-contact #edit-alternative-people a.flow-link"
        And I add "Frank" to the inputfield "#edit-first-name"
        And I add "Oz" to the inputfield "#edit-last-name"
        And I add "01723999999" to the inputfield "#edit-work-phone"
        And I add "08654999999" to the inputfield "#edit-mobile-phone"
        And I add "par_business_change@example.com" to the inputfield "#edit-email"
        And I click on the radio "#edit-preferred-contact-communication-mobile"
        And I click on the button "#edit-next"
        Then the element "#edit-alternative-people" contains the text "Frank"
        Then the element "#edit-alternative-people" contains the text "Oz"
        Then the element "#edit-alternative-people" contains the text "par_business_change@example.com"
        Then the element "#edit-alternative-people" contains the text "01723999999"
        Then the element "#edit-alternative-people" contains the text "08654999999"

        # EDIT LEGAL ENTITIES

        When I click on the button "form#par-flow-transition-business-details #edit-legal-entity a.flow-link"
        And I add "ABCD Mart Change" to the inputfield "#edit-registered-name"
        And I select the option with the text "Public Limited Company" for element "#edit-legal-entity-type"
        And I add "987654321" to the inputfield "#edit-company-house-no"
        And I click on the button "#edit-next"
        Then the element "#edit-legal-entity div" contains the text "ABCD Mart Change"
        Then the element "#edit-legal-entity" contains the text "987654321"
        Then the element "#edit-legal-entity" contains the text "Public Limited Company"

        # ADD LEGAL ENTITIES

        When I click on the link "add another legal entity"
        And I add "Another Legal Entity" to the inputfield "#edit-registered-name"
        And I select the option with the text "Sole Trader" for element "#edit-legal-entity-type"
        And I add "1234567890" to the inputfield "#edit-company-house-no"
        And I click on the button "#edit-next"
        Then the element "#edit-alternative" contains the text "Another Legal Entity"
        Then the element "#edit-alternative" contains the text "1234567890"
        Then the element "#edit-alternative" contains the text "Sole Trader"
