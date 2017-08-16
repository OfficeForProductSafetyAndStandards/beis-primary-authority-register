@ci @journey1
Feature: PAR User - Send Invite

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: User Journey 1 - Send invitiation to business
        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"

        # WELCOME SCREEN

        Then I expect that element ".error-message" is not visible
        And I expect that element "#block-par-theme-content" contains the text "Review and confirm your data by"
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

        # PARTERSHIP TASKS SCREEN

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Invite the business to confirm their details"

        # BUSINESS EMAIL INVITATION

        And I add "Test change meassage body [invite:invite-accept-link]" to the inputfield "#edit-email-body"
        And I add "Test change meassage subject" to the inputfield "#edit-email-body"
        When I press "Send Invitation"
#        Then I expect that element "h1" contains the text "Updating the Primary Authority Register"
#        And I expect that element "#edit-email-subject" contains the text "Test change subject line"
#        And I expect that element "#edit-email-body" contains the text "Test change meassage body [invite:invite-accept-link]"
#
#        # PARTERSHIP TASKS SCREEN
#
#        When I click on the link "Go back to your partnerships"
#        Then I expect that element "h1" contains the text "List of Partnerships"
#        And I click on the link "Log out"
