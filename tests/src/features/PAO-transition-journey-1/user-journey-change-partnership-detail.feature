@ci @journey1
Feature: PAR User - Change Partnership Details

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: User Journey 1 - Change partnership details
        # HOMEPAGE
        Given I open the url "/user/login"

        # LOGIN SCREEN

        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"

        # WELCOME SCREEN

        Then I expect that element ".error-message" is not visible
        When I click on the button ".button-start"

        # PARTNERSHIPS DASHBOARD

        And I scroll to element "#views-exposed-form-par-data-transition-journey-1-step-1-dv-journey-1-step-1"
        When I click on the link "ABCD Mart"

        # TERMS AND CONDITIONS SCREEN

        Then the element ".par-flow-transition-partnership-details-terms" contains the text "Please Review the new Primary Authority terms and conditions and confirm that you agree with them"
        When I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        And I scroll to element ".table-scroll-wrapper"
        And I click on the link "Review and confirm your partnership details"

        # REVIEW PARTNERSHIPS DETAILS

        And I click on the link "edit"
        And I add "test partnership info change" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"
        Then the element "#edit-first-section" contains the text "test partnership info change"
        When I click on the button "form#par-flow-transition-partnership-details-overview .authority-alternative-contact a.flow-link"
        And I add "Animal" to the inputfield "#edit-first-name"
        And I add "the Muppet" to the inputfield "#edit-last-name"
        And I add "91723456789" to the inputfield "#edit-work-phone"
        And I add "9777777777" to the inputfield "#edit-mobile-phone"
        And I add "par_authority_animal@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        Then the element "#edit-authority-contacts" contains the text "Animal"
        Then the element "#edit-authority-contacts" contains the text "the Muppet"
        And the element "#edit-authority-contacts" contains the text "par_authority_animal@example.com"
        And the element "#edit-authority-contacts" contains the text "91723456789"
        And the element "#edit-authority-contacts" contains the text "9777777777"
        When I click on the button "form#par-flow-transition-partnership-details-overview .authority-alternative-contact-0 a.flow-link"
        And I add "Miss" to the inputfield "#edit-first-name"
        And I add "Piggy" to the inputfield "#edit-last-name"
        And I add "par_authority_piggy@example.com" to the inputfield "#edit-email"
        And I add "917234567899" to the inputfield "#edit-work-phone"
        And I add "97777777779" to the inputfield "#edit-mobile-phone"
        When I click on the button "#edit-next"
        Then the element ".authority-alternative-contact-0" contains the text "Miss"
        Then the element ".authority-alternative-contact-0" contains the text "Piggy"
        Then the element ".authority-alternative-contact-0" contains the text "par_authority_piggy@example.com"
        Then the element ".authority-alternative-contact-0" contains the text "917234567899"
        Then the element ".authority-alternative-contact-0" contains the text "97777777779"
        When I click on the button "form#par-flow-transition-partnership-details-overview #edit-organisation-contacts a.flow-link"
        And I add "Fozzie" to the inputfield "#edit-first-name"
        And I add "Bear" to the inputfield "#edit-last-name"
        And I add "91723456789" to the inputfield "#edit-work-phone"
        And I add "9777777777" to the inputfield "#edit-mobile-phone"
        And I add "par_business_fozzie@example.com" to the inputfield "#edit-email"
        And I click on the button "#edit-next"
        Then the element "#edit-organisation-contacts" contains the text "Fozzie"
        Then the element "#edit-organisation-contacts" contains the text "Bear"
        And the element "#edit-organisation-contacts" contains the text "par_business_fozzie@example.com"
        And the element "#edit-organisation-contacts" contains the text "91723456789"
        And the element "#edit-organisation-contacts" contains the text "9777777777"
        When I click on the button "form#par-flow-transition-partnership-details-overview .organisation-alternative-contacts-1 a.flow-link"
        And I add "917234567899" to the inputfield "#edit-work-phone"
        And I add "97777777779" to the inputfield "#edit-mobile-phone"
        And I add "Pepe" to the inputfield "#edit-first-name"
        And I add "the King Prawn" to the inputfield "#edit-last-name"
        And I add "par_business_pepe@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        Then the element ".organisation-alternative-contacts-1" contains the text "par_business_pepe@example.com"
        And the element ".organisation-alternative-contacts-1" contains the text "Pepe"
        And the element ".organisation-alternative-contacts-1" contains the text "the King Prawn"
        And the element ".organisation-alternative-contacts-1" contains the text "917234567899"
        And the element ".organisation-alternative-contacts-1" contains the text "97777777779"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"
        Then the element "#block-par-theme-content" contains the text "Confirmed by the Authority"
        When I click on the link "Go back to your partnerships"
        Then the element "h1" contains the text "List of Partnerships"
        And I click on the link "Log out"
