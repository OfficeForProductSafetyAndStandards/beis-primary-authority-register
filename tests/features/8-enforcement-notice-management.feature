@ci @Bug @PAR1013 @PAR1049
Feature: Enforcement notice management

    Background:
        Given I am logged in as "par_authority@example.com"
        And I click the link text "See enforcement notifications sent"
        Then the element ".heading-secondary" contains the text "Enforcement Notifications"
    
    Scenario: Enforcement notice management

        # APPROVE FORM

        And I successfully approve enforcement notice "Enforcement notice 4"

        # BLOCK FORM

        And I successfully block enforcement notice "Enforcement notice 3"

        # REFER FORM

        And I successfully refer enforcement notice "Enforcement notice 1"

        # CHECK PAR ENFORCEMENT OFFICER VIEW

        And I click the link text "Log out"
        Given I open the path "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I click the link text "See enforcement notifications received"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 4"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 1"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 3"
        # And the element ".table-scroll-wrapper" does not contain the text "Enforcement notice 2"

    Scenario: Check view of an unapproved enforcement notice approval 

        # APPROVAL FORM

        And I successfully approve enforcement notice "Enforcement notice 2"

        # CHECK PAR ENFORCEMENT OFFICER VIEW

        When I click the link text "Log out"
        Given I am logged in as "par_enforcement_officer@example.com"
        When I click the link text "See enforcement notifications sent"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 2"