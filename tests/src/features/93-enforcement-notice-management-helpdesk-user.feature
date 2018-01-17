@ci
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # PARTNERSHIPS DASHBOARD
        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I click on the button "a*=Dashboard"
        And I click on the link "See enforcement notifications received"
        And I select the option with the text "Date Desc" for element "#edit-sort-bef-combine"
        And I click on the button "#edit-submit-par-user-enforcement-list"

#        # APPROVAL FORM

        And I click on the link "Title of enforcement notice Two"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then I expect that element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
#       Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement allowed"
