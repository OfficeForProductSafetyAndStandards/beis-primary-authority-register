
Feature: Enforcement notice management

    Background:

    
    @ci @PAR1013 @PAR1049 @enforcementnotices
    Scenario: Enforcement notice management

        Given I am logged in as "par_authority@example.com"
        And I click the link with text "See your enforcement notices"
        Then the element "h1.heading-xlarge" contains the text "Enforcements"

        # APPROVE FORM

        And I successfully approve enforcement notice "Enforcement notice 4"

        # BLOCK FORM

        And I successfully block enforcement notice "Enforcement notice 3"

        # REFER FORM

        # And I successfully refer enforcement notice "Enforcement notice 1"

        # CHECK PAR ENFORCEMENT OFFICER VIEW

        And I click the link text "Sign out"
        Given I open the path "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I click the link with text "See your enforcement notices"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 4"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 1"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 3"
        # And the element ".table-scroll-wrapper" does not contain the text "Enforcement notice 2"



    # @ci @enforcementnotices
    # Scenario: Edit EN Before Submission
 
@enforcementnotices
    Scenario: Check view of an unapproved enforcement notice approval for HD User

        Given I am logged in as "par_helpdesk@example.com"
        And I click the link text "Dashboard"
        And I click the link with text "See your enforcement notices"
        Then the element "h1.heading-xlarge" contains the text "Enforcements"

        # APPROVAL FORM

        And I successfully approve enforcement notice "Enforcement notice 2"

        # CHECK PAR AUTHORITY VIEW

@enforcementnotices @ci
    Scenario: Check view of approved and unapproved EN records

        Given I am logged in as "par_authority@example.com"
        When I click the link with text "See your enforcement notices"
        And I click the link text "Enforcement notice 2"
        Then the element "h1.heading-xlarge" contains the text "Respond to notice of enforcement action"
        When I click the link text "Dashboard"
        And I click the link with text "See your enforcement notices"
        And I click the link text "Enforcement notice 4"
        Then the element "h1.heading-xlarge" contains the text "View notification of enforcement action received from"


@enforcementnotices @ci
    Scenario: Check view of EN's for Enforcement Officer User

        Given I am logged in as "par_enforcement_officer@example.com"
        When I click the link with text "See your enforcement notices"
        And I click the link text "Enforcement notice 1"
        Then the element "#block-par-theme-content" contains the text "Grover Muppet"
        Then the element "#block-par-theme-content" contains the text "01723456789"
        Then the element "#block-par-theme-content" contains the text "par_enforcement_officer@example.com"
