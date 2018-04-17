@ci
Feature: Enforcement Officer - Enforcement Notice Process

    Scenario Outline: Enforcement Officer - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"

        # CREATE ENFORCEMENT NOTIFICATION

        When I create new valid enforcement notification "<Notification Title>" for organisation "<Organisation>"

        # CHECK ENFORCEMENT NOTIFICATION

        And I check that EO can see valid enforcement notification "<Notification Title>"

        And the element ".table-scroll-wrapper" does not contain the text "Organisation For Direct Partnership"

        # CHECK ENFORCEMENT NOTIFICATION EMAILS

        Then the enforcement notification emails confirmations for "<PARUser>" are processed for enforcement notification "<Notification Title>"

        Examples:
            | Notification Title   | Organisation   | PARUser                   |
            | Enforcement notice 1 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 2 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 3 | Charlie's Cafe | par_authority@example.com |
            | Enforcement notice 4 | Charlie's Cafe | par_authority@example.com |