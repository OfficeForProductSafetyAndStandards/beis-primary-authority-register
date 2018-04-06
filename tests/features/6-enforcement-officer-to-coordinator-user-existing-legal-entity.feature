@ci
Feature: Enforcement Officer/Coordinator - Enforcement Notice Process

    Scenario: Enforcement Officer/Coordinator - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"

        # CREATE ENFORCEMENT NOTIFICATION

        And I create new valid enforcement notication "3" for organisation "Charlie's Cafe"

        # CHECK ENFORCEMENT NOTIFICATION

        Then I check that EO can see valid enforcement notication "3"
