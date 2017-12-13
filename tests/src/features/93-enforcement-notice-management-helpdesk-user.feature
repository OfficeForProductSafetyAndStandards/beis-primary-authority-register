@ci
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # PARTNERSHIPS DASHBOARD
        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I click on the button "a*=Dashboard"
        And I click on the link "See enforcement notifications"
        And I select the option with the text "Desc" for element "#edit-sort-order"
        And I click on the button "#edit-submit-par-user-enforcements"

#        # APPROVAL FORM

        And I click on the link "Title of enforcement notice Two"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then I expect that element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
#       Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement allowed"

#        # BLOCK

#        When I click on the button "a*=Dashboard"
#        And I click on the link "See enforcement notifications"
#        And I select the option with the text "Desc" for element "#edit-sort-order"
#        And I click on the button "#edit-submit-par-user-enforcements"
#        And I click on the link "Title of enforcement notice Two"
#        And I click on the radio "#edit-actions-0-primary-authority-status-blocked"
#        And I click on the button "#edit-actions-next"
#        Then I expect that element ".error-summary" does exist
#        And I scroll to element "#edit-actions-0-primary-authority-notes"
#        When I add "Some notes about why enforcement action blocked" to the inputfield "#edit-actions-0-primary-authority-notes"
#        And I click on the button "#edit-actions-next"
#        Then I expect that element "h1" contains the text "Confirm Enforcement Notice"
#        And I click on the button "#edit-actions-next"
#        Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement blocked"

        # REFER FORM

#        When I click on the button "a*=Dashboard"
#        And I click on the link "See enforcement notifications"
#        And I select the option with the text "Desc" for element "#edit-sort-order"
#        And I click on the button "#edit-submit-par-user-enforcements"
#        And I click on the link "Title of enforcement notice Three"
#        And I click on the radio "#edit-actions-0-primary-authority-status-referred"
#        When I add "Some notes about why enforcement action referred" to the inputfield "#edit-actions-0-referral-notes"
#        And I click on the button "#edit-actions-next"
#        Then I expect that element "h1" contains the text "Confirm Enforcement Notice"
#        And I click on the button "#edit-actions-next"
##        Then I expect that element "#par-rd-help-desk-approve" contains the text "Enforcement referred"
