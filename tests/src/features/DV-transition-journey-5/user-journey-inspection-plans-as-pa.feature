@ci @journey5
Feature: Helpdesk As Primary Authority - Inspection Plans

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Helpdesk As Primary Authority - Inspection Plans
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

        # INSPECTION PLANS

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Review and confirm your inspection plan"
#        Then I expect that element "#edit-document-list" contains the text "Inspection Plan"
#        And I click on the checkbox ".form-checkbox"
        And I click on the button "#edit-next"

        # CHECK INSPECTION PLAN CONFIRMEDs

#        And I scroll to element ".table-scroll-wrapper"
#        When I click on the link "Review and confirm your inspection plan"
#        Then I expect that element ".form-checkbox" is not enabled
#        When I click on the button "#edit-next"

        # PARTNERSHIPS DASHBOARD

        And I click on the link "Go back to your partnerships"
        Then I expect that element "h1" contains the text "List of Partnerships"
        And I click on the link "Log out"
