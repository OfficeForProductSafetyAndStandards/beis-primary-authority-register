@ci
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # CHECK AS ENFORCEMENT OFFICER

        Given I open the url "/user/login"
        And I add "par_enforcement_officer@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element "#block-par-theme-content" contains the text "Search for a partnership"
        When I click on the link "See enforcement notifications sent"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Enforcement Notifications"
        Then I expect that element "h1.heading-xlarge" contains the text "Sent"
        Then the element "#views-exposed-form-par-user-enforcement-list-enforcement-notices-sent" not contains the text "Title of enforcement notice"
        And I expect that element "a*=Title of enforcement notice One" does not exist
        And I expect that element "a*=Title of enforcement notice Three" does not exist
        And I expect that element "a*=Title of enforcement notice Four" does not exist
        And I expect that element "a*=Added Enforcement Action" does not exist
        And I select the option with the text "Date Desc" for element "#edit-sort-bef-combine"
        And I click on the button "#edit-submit-par-user-enforcements"
        And I click on the link "Log out"

        # PRIMARY AUTHORITY OFFICER DASHBOARD

        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I click on the button "a*=Dashboard"
        And I click on the link "See enforcement notifications received"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Enforcement Notifications"
        Then I expect that element "h1.heading-xlarge" contains the text "Received"

        # APPROVE NOTICE

        And I click on the link "Title of enforcement notice Four"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Make a decision"
        Then I expect that element "h1.heading-xlarge" contains the text "Proposed enforcement action(s)"
        Then the element "#edit-actions-0-primary-authority-status--wrapper h3.heading-medium" contains the text "Decide to allow or block this action, or refer this action to another Primary Authority"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then I expect that element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
        And I click on the button "a*=Dashboard"
        And I click on the link "See enforcement notifications received"
        Then the element "table" contains the text "Title of enforcement notice Four"

        # BLOCK NOTICE

        And I click on the link "Title of enforcement notice Three"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Make a decision"
        Then I expect that element "h1.heading-xlarge" contains the text "Proposed enforcement action(s)"
        And I click on the radio "#edit-actions-0-primary-authority-status-blocked"
        When I add "Some notes about why enforcement action blocked" to the inputfield "#edit-actions-0-primary-authority-notes"
        And I click on the button "#edit-actions-next"

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then I expect that element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
        And I click on the button "a*=Dashboard"
        And I click on the link "See enforcement notifications received"
        Then the element "table" contains the text "Title of enforcement notice Three"

        # REFER NOTICE

        And I select the option with the text "Date Desc" for element "#edit-sort-bef-combine"
        And I click on the button "#edit-submit-par-user-enforcement-list"
        And I click on the link "Title of enforcement notice One"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Make a decision"
        Then I expect that element "h1.heading-xlarge" contains the text "Proposed enforcement action(s)"
        And I click on the radio "#edit-actions-0-primary-authority-status-referred"
        When I add "Some notes about why enforcement action referred" to the inputfield "#edit-actions-0-referral-notes"
        And I click on the button "#edit-actions-next"

        And I click on the radio ".option*=Upper West Side Borough Council"
        And I click on the button "#edit-next"

        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then I expect that element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"

        And I click on the link "Log out"

        # CHECK PAR ENFORCEMENT OFFICER VIEW

        Given I am logged in as "par_enforcement_officer@example.com"
        When I click on the link "See enforcement notifications sent"
        Then I expect that element "a*=Title of enforcement notice One" does exist
        And I expect that element "a*=Added Enforcement Action" does exist
        And I expect that element "a*=Title of enforcement notice Three" does exist
        And I expect that element "a*=Title of enforcement notice Four" does exist
