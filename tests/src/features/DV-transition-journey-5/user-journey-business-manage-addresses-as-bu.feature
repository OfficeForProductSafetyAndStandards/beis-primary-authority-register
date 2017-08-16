@ci
Feature: Helpdesk As Business User - Manage Addresses

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: Helpdesk As Business User - Manage Addresses
        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element "#block-par-theme-account-menu" contains the text "Log out"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        When I click on the link "Dashboard"
#        When I open the url "/dv/rd-dashboard"

        # PARTNERSHIP DETAILS

        Then I expect that element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the button "td.views-field.views-field-organisation-name a"
        When I click on the link "Review and confirm your business details"
        Then I expect that element "#edit-about-business" contains the text "About the business"
        And I expect that element "#edit-legal-entity" contains the text "Legal Entities"
        And I expect that element "#edit-primary-contact" contains the text "Main business contact"
        And I expect that element "#edit-0" contains the text "Trading Names"

        # EDIT REGISTERED ADDRESS

        When  I click on the button "form#par-flow-transition-business-details #edit-registered-address a.flow-link"
        And I clear the inputfield "#edit-address-line1"
        And I clear the inputfield "#edit-address-line2"
        And I clear the inputfield "#edit-town-city"
        And I clear the inputfield "#edit-postcode"
        And I clear the inputfield "#edit-county"
        And I click on the button "#edit-next"
#        Then I expect that element "input:focus" is visible
        And I add "change" to the inputfield "#edit-postcode"
        And I click on the button "#edit-next"
#        Then I expect that element "input:focus" is visible
        And I add "1 Change St" to the inputfield "#edit-address-line1"
        And I click on the button "#edit-next"
#        Then I expect that element "input:focus" is visible
        And I add "New Change" to the inputfield "#edit-address-line2"
        And I click on the button "#edit-next"
#        Then I expect that element "input:focus" is visible
        And I add "New Change State" to the inputfield "#edit-town-city"
        And I click on the button "#edit-next"
#        Then I expect that element "input:focus" is visible
        And I add "US-CH" to the inputfield "#edit-county"
        And I select the option with the text "Wales" for element "#edit-country"
        When I click on the button "#edit-next"
        Then I expect that element "span.address-line1" contains the text "1 Change St"
        And I expect that element "span.address-line2" contains the text "New Change"
        And I expect that element "span.locality" contains the text "New Change State"
        And I expect that element "span.postal-code" contains the text "change"
        And I expect that element "#edit-registered-address" contains the text "Wales"

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
        And I add "ABCD Mart Change" to the inputfield "#edit-registered-name"
        And I select the option with the text "Limited Company" for element "#edit-legal-entity-type"
        And I add "987654321" to the inputfield "#edit-company-house-no"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-legal-entity div" contains the text "ABCD Mart Change"
        And I expect that element "#edit-legal-entity" contains the text "987654321"
        And I expect that element "#edit-legal-entity" contains the text "Limited Company"

        # ADD LEGAL ENTITIES

        When I click on the link "add another legal entity"
        And I click on the button "#edit-next"
#        Then I expect that element "input:focus" is visible
        And I add "Another Legal Entity" to the inputfield "#edit-registered-name"
#        Then I expect that element "input:focus" is visible
        And I select the option with the text "Sole Trader" for element "#edit-legal-entity-type"
#        Then I expect that element "input:focus" is visible
        And I click on the button "#edit-next"
        Then I expect that element "#edit-alternative" contains the text "Another Legal Entity"
        And I expect that element "#edit-alternative" contains the text "Sole Trader"
