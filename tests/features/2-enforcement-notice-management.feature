
Feature: Enforcement notice review

    @ci @smoke
    Scenario: Approve an enforcement notice
        Given I am logged in as "par_authority@example.com"
        When I click the link with text "See your enforcement notices"
        Then the element "h1.heading-xlarge" contains the text "Enforcements"
        And I successfully approve enforcement notice "Enforcement notice 4"

    @ci @smoke
    Scenario: Block an enforcement notice
        Given I am logged in as "par_authority@example.com"
        When I click the link with text "See your enforcement notices"
        Then the element "h1.heading-xlarge" contains the text "Enforcements"
        And I successfully block enforcement notice "Enforcement notice 3"

    @ci
    Scenario: Refer an enforcement notice
        Given I am logged in as "par_authority@example.com"
        When I click the link with text "See your enforcement notices"
        Then the element "h1.heading-xlarge" contains the text "Enforcements"
        And I successfully refer enforcement notice "Enforcement notice 1" to "Lower East Side Borough Council"

    @ci
    Scenario: Enforcement notices against organisations without active partnerships with another authority can be referred
        Given I am logged in as "par_authority@example.com"
        When I click the link with text "See your enforcement notices"
        And I click the link text "Enforcement notice 5"
        Then the element "h1.heading-xlarge" contains the text "Respond to notice of enforcement action"
        And the element "#edit-par-component-enforcement-action-review-0-primary-authority-status-referred" does not exist

    @ci @smoke
    Scenario: View reviewed enforcement notices
        Given I am logged in as "par_authority@example.com"
        When I click the link with text "See your enforcement notices"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 3"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 4"
        And I click the link text "Enforcement notice 3"
        Then the element "h1.heading-xlarge" contains the text "View notification of enforcement action received from"
        And the element ".component-enforcement-send-warning" does not contain the text "Please note that this enforcement notice has been approved."
        When I click the link text "Dashboard"
        And I click the link with text "See your enforcement notices"
        And I click the link text "Enforcement notice 4"
        Then the element "h1.heading-xlarge" contains the text "View notification of enforcement action received from"
        And the element ".component-enforcement-send-warning" contains the text "Please note that this enforcement notice has been approved."

    @ci
    Scenario: Check un-reviewed enforcement notices
        Given I am logged in as "par_authority@example.com"
        When I click the link with text "See your enforcement notices"
        And the element ".table-scroll-wrapper" contains the text "Enforcement notice 3"
        When I click the link text "Enforcement notice 2"
        Then the element "h1.heading-xlarge" contains the text "Respond to notice of enforcement action"

    @ci
    Scenario: Check enforcement officer's details are recorded on the enforcement
        Given I am logged in as "par_enforcement_officer@example.com"
        When I click the link with text "See your enforcement notices"
        And I click the link text "Enforcement notice 4"
        Then the element ".component-enforcement-full-summary .enforcement-officer" contains the text "Grover Muppet"
        Then the element ".component-enforcement-full-summary .enforcement-officer" contains the text "01723456789"
        Then the element ".component-enforcement-full-summary .enforcement-officer" contains the text "par_enforcement_officer@example.com"
        Then the element ".component-enforcement-full-summary .authority-officer" contains the text "par_enforcement_officer@example.com"
