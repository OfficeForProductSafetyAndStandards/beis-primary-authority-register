Feature: Enforcement Officer - Enforcement Notice Process

    @ci @enforcementnotices
    Scenario Outline: Enforcement Officer - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"

        # CREATE ENFORCEMENT NOTIFICATION

        When I create new valid enforcement notification "<Notification Title>" for organisation "<Organisation>"

        # CHECK ENFORCEMENT NOTIFICATION EMAILS

        Then the "enforcement creation" email confirmations for "<PARUser>" are processed

    Examples:
        | Notification Title      | Organisation   | PARUser                   |
        | Enforcement notice 1    | Charlie's Cafe | par_authority@example.com |
        | Enforcement notice 2    | Charlie's Cafe | par_authority@example.com |
        | Enforcement notice 3    | Charlie's Cafe | par_authority@example.com |
        | Enforcement notice 4    | Charlie's Cafe | par_authority@example.com |


    Scenario: Issue enforcement notice on Coordinated Partnership with no members

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"
        And I click the link text "Search for a partnership"
        When I add "Charity Retail Association" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        And I click the link text "Partnership between Salford City Council and Charity Retail Association"
        When I click the link text "Send a notification of a proposed enforcement action"
        And I click on authority selection if available
        When I click on the button "#edit-next"
        And the element "#par-enforce-organisation" contains the text "Sorry but there are no members for this organisation."
        When I click on the button "#edit-next"
        And the element "#par-enforce-organisation" contains the text "Enforcement notification cannot be created because no member organisation exists in the partnership. Please contact the help desk."
