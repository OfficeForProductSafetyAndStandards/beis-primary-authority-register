@Pending
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # PARTNERSHIPS DASHBOARD

        Given I reset the test data
        And I am logged in as "par_helpdesk@example.com"
        When I click on the button "a*=Dashboard"
        And I click on the link "See enforcement notifications"
        And I select the option with the text "Desc" for element "#edit-sort-order"
        And I click on the button "#edit-submit-par-user-enforcements"

#        # APPROVAL FORM

        And I click on the link "Title of enforcement notice One"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"
        Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement allowed"

#        # BLOCK

        When I click on the button "a*=Dashboard"
        And I click on the link "See enforcement notifications"
        And I select the option with the text "Desc" for element "#edit-sort-order"
        And I click on the button "#edit-submit-par-user-enforcements"
        And I click on the link "Title of enforcement notice Two"
        And I click on the radio "#edit-actions-0-primary-authority-status-blocked"
        When I add "Some notes about why enforcement action blocked" to the inputfield "#edit-actions-0-primary-authority-notes"
        And I click on the button "#edit-actions-next"
        Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement blocked"

        # REFER FORM

        When I click on the button "a*=Dashboard"
        And I click on the link "See enforcement notifications"
        And I select the option with the text "Desc" for element "#edit-sort-order"
        And I click on the button "#edit-submit-par-user-enforcements"
        And I click on the link "Title of enforcement notice Two"
        And I click on the radio "#edit-actions-0-primary-authority-status-refer"
        When I add "Some notes about why enforcement action referred" to the inputfield "#edit-actions-0-referral-notes"
        And I click on the button "#edit-actions-next"
        Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement referred"
