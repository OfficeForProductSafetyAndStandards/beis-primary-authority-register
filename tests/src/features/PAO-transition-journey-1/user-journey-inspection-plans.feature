@ci @journey1
Feature: User Journey 1 (happy path)

    Background:
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
        And the element "#block-sitewidehelpdeskmessage" contains the text "0121 345 1201"
        When I click on the button ".button-start"

        # PARTNERSHIPS DASHBOARD

        And I click on the link "ABCD Mart"

        # TERMS AND CONDITIONS SCREEN

        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"

        # INSPECTION PLANS

        When I click on the link "Review and confirm your inspection plan"
        Then the element "#edit-document-list" contains the text "Inspection Plan"
        And I click on the checkbox ".form-checkbox"
        And I click on the button "#edit-next"
        When I click on the link "Go back to your partnerships"
        Then the element "h1" contains the text "List of Partnerships for a Primary Authority"
        And I click on the link "Log out"
