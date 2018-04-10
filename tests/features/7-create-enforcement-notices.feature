@ci
Feature: Enforcement Officer/Coordinator - Enforcement Notice Process

    Scenario Outline: Enforcement Officer/Coordinator - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"

        # CREATE ENFORCEMENT NOTIFICATION

        When I create new valid enforcement notication "<Notification Title>" for organisation "<Organisation>"

        # CHECK ENFORCEMENT NOTIFICATION

        And I check that EO can see valid enforcement notification "<Notification Title>"

        # CHECK ENFORCEMENT NOTIFICATION EMAILS

        Then the EN emails confirmations for "<PARUser>" are processed for enforcement notication "<Notification Title>"

        Examples:
            | Notification Title        | Organisation   | PARUser                      |
            | Enforcement notice one    | Charlie's Cafe | par_authority@example.com    |
            | Enforcement notice two    | Charlie's Cafe | par_authority@example.com    |
            | Enforcement notice three  | Charlie's Cafe | par_authority@example.com    |
            | Enforcement notice four   | Charlie's Cafe | par_authority@example.com    |