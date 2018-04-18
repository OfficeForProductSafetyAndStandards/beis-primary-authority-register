Feature: Enforcement Officer - Enforcement Notice Process

    @ci
    Scenario Outline: Enforcement Officer - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"

        # CREATE ENFORCEMENT NOTIFICATION

        When I create new valid enforcement notification "<Notification Title>" for organisation "<Organisation>"

        # CHECK ENFORCEMENT NOTIFICATION EMAILS

        Then the "enforcement creation" emails confirmations for "<PARUser>" are processed

        Examples:
            | Notification Title   | Organisation   | PARUser                   |
            | Enforcement notice 1 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 2 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 3 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 4 | Charlie's Cafe | par_authority@example.com |


    @pending
    Scenario Outline: Enforcement Officer With More Than One Authority - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "eo_more_than_one_authority@example.com"
        # When I enforce a direct partnership
        # Then I should get the 'Choose which authority to act on behalf of' screen

    @pending
    Scenario Outline: Enforcement Officer With One Authority - Issue enforcement notice On Direct Partnership

        #LOGIN
        
        Given I am logged in as "eo_one_authority@example.com"
        # When I enforce a direct partnership
        # Then I should NOT get the 'Choose which authority to act on behalf of' screen

    @pending
    Scenario Outline: Enforcement Officer With One Authority - Issue enforcement notice On Coordinated Partnership

        #LOGIN
        
        Given I am logged in as "eo_one_authority@example.com"
        And I go to partnership detail page for my partnership "Organisation For Coordinated Partnership"
        # When I enforce a coordinated partnership that has at least two members
        # Then I should get the 'Choose the member to enforce' screen

    @pending
    Scenario Outline: Enforcement Officer With One Authority - Issue enforcement notice On Coordinated Partnership

        #LOGIN
        
        Given I am logged in as "eo_one_authority@example.com"
        # When I enforce a coordinated partnership that has no members
        # Then I should get the 'Choose Legal Entity to enforce' screen (after entering EO's contact details)

    @pending
    Scenario Outline: HD User - Issue enforcement notice On Direct Partnership

        #LOGIN
        
        Given I am logged in as "eo_one_authority@example.com"
        # When I enforce a direct partnership
        # Then I should get the 'Choose Legal Entity to enforce' screen (after entering EO's contact details)