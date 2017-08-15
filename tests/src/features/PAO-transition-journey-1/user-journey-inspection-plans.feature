@ci @journey1
Feature: PAR User - Inspection Plans

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: User Journey 1 - Inspection Plans
        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"

        # WELCOME SCREEN

        Then I expect that element ".error-message" is not visible
        And I expect that element "#block-sitewidehelpdeskmessage" contains the text "0121 345 1201"
        When I click on the button ".button-start"

        # PARTNERSHIPS DASHBOARD

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "ABCD Mart"

        # TERMS AND CONDITIONS SCREEN
        Then I expect that element ".par-flow-transition-partnership-details-terms" contains the text "Please Review the new Primary Authority terms and conditions and confirm that you agree with them"
        When I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"

        # PARTNERSHIP DETAILS SCREEN

        When I scroll to element ".table-scroll-wrapper"
        And I click on the link "Review and confirm your partnership details"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"

        # INSPECTION PLANS

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Review and confirm your inspection plan"
        Then I expect that element "#edit-document-list" contains the text "Inspection Plan"
        And I click on the checkbox ".form-checkbox"
        And I click on the button "#edit-next"

        # CHECK INSPECTION PLAN CONFIRMED

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Review and confirm your inspection plan"
        Then I expect that element ".form-checkbox" is not enabled
        When I click on the button "#edit-next"

        # PARTNERSHIPS DASHBOARD

        And I click on the link "Go back to your partnerships"
        Then I expect that element "h1" contains the text "List of Partnerships"
        And I click on the link "Log out"
