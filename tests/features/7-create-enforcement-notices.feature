Feature: Enforcement Officer - Enforcement Notice Process

    @ci
    Scenario Outline: Enforcement Officer - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"

        # CREATE ENFORCEMENT NOTIFICATION

        When I create new valid enforcement notification "<Notification Title>" for organisation "<Organisation>"

        # CHECK ENFORCEMENT NOTIFICATION EMAILS

        Then the "enforcement creation" email confirmations for "<PARUser>" are processed

        Examples:
            | Notification Title   | Organisation   | PARUser                   |
            | Enforcement notice 1 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 2 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 3 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 4 | Charlie's Cafe | par_authority@example.com |
