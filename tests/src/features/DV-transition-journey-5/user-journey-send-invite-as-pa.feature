@ci @journey5

Feature: Helpdesk As Primary Authority - Send invitiation to business

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Helpdesk As Primary Authority - Send invitiation to business
        # LOGIN SCREEN

        Given I am logged in as "par_helpdesk@example.com"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        When I click on the link "Dashboard"
        Then I expect that element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the button "td.views-field.views-field-authority-name a"
        Then I expect that element ".table-scroll-wrapper" contains the text "Review and confirm your partnership details"
        When I click on the link "Review and confirm your partnership details"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"
        And I click on the link "Dashboard"

        # PARTNERSHIP DETAILS

        Then I expect that element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the button "td.views-field.views-field-authority-name a"

        # PARTNERSHIP TASKS SCREEN

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
#        # PARTNERSHIP TASKS SCREEN
#
#        When I click on the link "Go back to your partnerships"
#        Then I expect that element "h1" contains the text "List of Partnerships"
#        And I click on the link "Log out"
