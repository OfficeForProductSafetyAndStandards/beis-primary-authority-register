@ci @test
Feature: Business User - Complete organisation details

    Scenario: Business User - Complete organisation details
        Given I am logged in as "par_business@example.com"
        # COMPLETE PARTNERSHIP ORGANISATION DETAIL
        And I complete the organisation details for direct partnership "Organisation For Direct Partnership 8"
