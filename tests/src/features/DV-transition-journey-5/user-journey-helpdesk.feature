@ci @journey5
Feature: Helpdesk - Dashboard

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
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then the element "#block-par-theme-account-menu" contains the text "Log out"

        # WELCOME SCREEN

#       When I click on the button "Dashboard"
        When I open the url "/dv/rd-dashboard"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        Then the element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the link "List of tasks"
        Then the element ".table-scroll-wrapper" contains the text "Review and confirm your partnership details"
        When I open the url "/dv/rd-dashboard"
#        When I click on the link "td.views-field-nothing-1 a"
#        Then the element ".table-scroll-wrapper" contains the text "Review and confirm your business details"
#        When I open the url "/dv/rd-dashboard"
        And I click on the link "Download as CSV"

#        Then I expect that element ".error-message" is not visible
#        When I click on the button ".button-start"
#        And I scroll to element ".table-scroll-wrapper"
#        And I click on the link "ABCD Mart"
#
#        # TERMS AND CONDITIONS SCREEN
#
#        And I click on the checkbox "#edit-terms-conditions"
#        And I click on the button "#edit-next"
#
#        # PARTNERSHIP TASKS SCREEN
#
#        And I scroll to element ".table-scroll-wrapper"
#        When I click on the link "Invite the business to confirm their details"
#
#        # BUSINESS EMAIL INVITATION
#
#        And I add "Test change meassage body [invite:invite-accept-link]" to the inputfield "#edit-email-body"
#        And I add "Test change meassage subject" to the inputfield "#edit-email-body"
#        And I press "Send Invitation"
#    # Then the element ".placeholder" not contains the text "Error"
#    # # PARTERSHIP TASKS SCREEN
#    # Then the element "h1" contains the text "Updating the Primary Authority Register"
#    # Then the element "#edit-email-subject" contains the text "Test change subject line"
#    # Then the element "#edit-email-body" contains the text "Test change meassage body [invite:invite-accept-link]"
#    # When I click on the link "Go back to your partnerships"
#    # Then the element "h1" contains the text "List of Partnerships for a Primary Authority"
#    # And I click on the link "Log out"
