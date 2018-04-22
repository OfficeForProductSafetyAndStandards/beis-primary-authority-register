@ci @Bug @PAR1013 @PAR1049
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # APPROVE FORM
        
        Given I am logged in as "par_authority@example.com"
        And I click the link text "See enforcement notifications sent"
        Then the element ".heading-secondary" contains the text "Enforcement Notifications"
        And I click the link text "Enforcement notice 4"
        Then the element "#edit-actions-0-primary-authority-status--wrapper h3.heading-medium" contains the text "Decide to allow or block this action, or refer this action to another Primary Authority"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then the element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
        And I click the link text "Dashboard"
        And I click the link text "See enforcement notifications sent"
        Then the element ".cols-6" contains the text "Enforcement notice 4"

        # BLOCK FORM

        And I click the link text "Enforcement notice 3"
        And I click on the radio "#edit-actions-0-primary-authority-status-blocked"
        When I add "Some notes about why enforcement action blocked" to the inputfield "#edit-actions-0-primary-authority-notes"
        And I click on the button "#edit-actions-next"
#        Then the element ".heading-xlarge" contains the text "Confirm Enforcement Notice"
        And I click on the button "#edit-actions-next"
        And I click the link text "Dashboard"
        And I click the link text "See enforcement notifications sent"
        Then the element ".cols-6" contains the text "Enforcement notice 3"

        # REFER FORM

        And I select the option with the value "notice_date DESC" for element "#edit-sort-bef-combine"
        And I click on the button "#edit-submit-par-user-enforcement-list"
        And I click the link text "Enforcement notice 1"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Make a decision"
        Then the element "h1.heading-xlarge" contains the text "Proposed enforcement action(s)"
        And I click on the radio "#edit-actions-0-primary-authority-status-referred"
        When I add "Some notes about why enforcement action referred" to the inputfield "#edit-actions-0-referral-notes"
        And I click on the button "#edit-actions-next"
        And I click on the radio ".form-radio"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Confirmation"
        Then the element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
        And I click the link text "Log out"

        # CHECK PAR ENFORCEMENT OFFICER VIEW

        Given I open the path "/user/login"
        And I add "par_enforcement_officer@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I click the link text "See enforcement notifications sent"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 4"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 1"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 3"
        # And the element ".table-scroll-wrapper" does not contain the text "Enforcement notice 2"

    Scenario: Enforcement notice approval 

        #LOGIN
        
        Given I am logged in as "par_helpdesk@example.com"
        When I click the link text "Dashboard"
        And I click the link text "See enforcement notifications received"
        And I select the option with the value "notice_date DESC" for element "#edit-sort-bef-combine"
        And I click on the button "#edit-submit-par-user-enforcement-list"

        # APPROVAL FORM

        And I click the link text "Enforcement notice 2"
        And I click on the radio "#edit-actions-0-primary-authority-status-approved"
        And I click on the button "#edit-actions-next"
        Then the element ".heading-secondary" contains the text "Confirmation"
        Then the element "h1.heading-xlarge" contains the text "Enforcement action decision"
        And I click on the button "#edit-actions-next"
        # Then the element "#par-rd-help-desk-approve" contains the text "Enforcement allowed"
        And I click the link text "Log out"

        # CHECK PAR ENFORCEMENT OFFICER VIEW

        Given I am logged in as "par_enforcement_officer@example.com"
        When I click the link text "See enforcement notifications sent"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 2"