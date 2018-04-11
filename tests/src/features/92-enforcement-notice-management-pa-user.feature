@ci @Bug @PAR1013 @PAR1049
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # APPROVAL FORM

        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I click on the button "a=Dashboard"
        And I click on the link "See enforcement notifications sent"
        Then I expect that element ".heading-secondary" contains the text "Enforcement Notifications"
        Then the element ".heading-small" contains the text "Your enforcement notifications"
        And I click on the link "Title of enforcement notice Four"
        Then the element "#edit-actions-0-primary-authority-status--wrapper h3.heading-medium" contains the text "Decide to allow or block this action, or refer this action to another Primary Authority"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then I expect that element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
        And I click on the button "a=Dashboard"
        And I click on the link "See enforcement notifications sent"
        Then the element ".cols-6" contains the text "Title of enforcement notice Four"

        # BLOCK FORM

        And I click on the link "Title of enforcement notice Three"
        And I click on the radio "#edit-actions-0-primary-authority-status-blocked"
        When I add "Some notes about why enforcement action blocked" to the inputfield "#edit-actions-0-primary-authority-notes"
        And I click on the button "#edit-actions-next"
#        Then I expect that element ".heading-xlarge" contains the text "Confirm Enforcement Notice"
        And I click on the button "#edit-actions-next"
        And I click on the button "a=Dashboard"
        And I click on the link "See enforcement notifications sent"
        Then the element ".cols-6" contains the text "Title of enforcement notice Three"

        # REFER FORM

        And I select the option with the text "Date Desc" for element "#edit-sort-bef-combine"
        And I click on the button "#edit-submit-par-user-enforcement-list"
        And I click on the link "Title of enforcement notice One"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Make a decision"
        Then I expect that element "h1.heading-xlarge" contains the text "Proposed enforcement action(s)"
        And I click on the radio "#edit-actions-0-primary-authority-status-referred"
        When I add "Some notes about why enforcement action referred" to the inputfield "#edit-actions-0-referral-notes"
        And I click on the button "#edit-actions-next"
        And I click on the radio ".option=Upper West Side Borough Council"
        And I click on the button "#edit-next"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then I expect that element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
        And I click on the link "Log out"

        # CHECK PAR ENFORCEMENT OFFICER VIEW

        Given I open the url "/user/login"
        And I add "par_enforcement_officer@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I click on the link "See enforcement notifications sent"
        Then I expect that element "a=Title of enforcement notice One" does exist
        And I expect that element "a=Title of enforcement notice Three" does exist
        And I expect that element "a=Title of enforcement notice Four" does exist
        And I expect that element "a=Title of enforcement notice 2" does not exist

