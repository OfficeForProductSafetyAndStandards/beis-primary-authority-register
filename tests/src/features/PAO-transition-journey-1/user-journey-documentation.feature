@ci @journey1
Feature: PAR User - Documentation

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: User Journey 1 - Documentation
        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"

        # WELCOME SCREEN

        Then I expect that element ".error-message" is not visible
        When I click on the button ".button-start"

        # PARTNERSHIPS DASHBOARD

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "ABCD Mart"

        # TERMS AND CONDITIONS SCREEN

        Then I expect that element ".par-flow-transition-partnership-details-terms" contains the text "Please Review the new Primary Authority terms and conditions and confirm that you agree with them"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" contains the text "You must agree to the new terms and conditions"
        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"

        # PARTNERSHIP DETAILS SCREEN

        When I scroll to element ".table-scroll-wrapper"
        And I click on the link "Review and confirm your partnership details"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"

        # DOCUMENTATION

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Review and confirm your documentation for ABCD Mart"
        And I click on the link "edit"
        And I click on the radio "#edit-document-type-authority-advice"
        And I click on the checkbox ".form-label*=Cookie control"
        When I click on the button "#edit-next"
        Then I expect that element ".table-scroll-wrapper" contains the text "✔"
        And the element ".table-scroll-wrapper" contains the text "Cookie control"
        When I click on the link "Save"
        Then I expect that element ".table-scroll-wrapper" contains the text "50%"
        When I click on the link "Review and confirm your documentation for ABCD Mart"
        And I click on the link "edit"
        And I click on the radio "#edit-document-type-authority-advice"
        And I click on the checkbox ".form-label*=Alphabet learning"
        When I click on the button "#edit-next"
        Then I expect that element ".table-scroll-wrapper" contains the text "✔"
        And I expect that element ".table-scroll-wrapper" contains the text "Cookie control, Alphabet learning"
        When I click on the link "Save"
        Then I expect that element ".table-scroll-wrapper" contains the text "100%"
        And I click on the link "Log out"
