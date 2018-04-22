Feature: Business User - Complete organisation details

    @ci
    Scenario: Business User - Complete organisation details

        # LOGIN
        
        Given I am logged in as "par_business@example.com"

        # COMPLETE PARTNERSHIP ORGANISATION DETAIL

        And I complete the partnership details for direct partnership "Organisation For Direct Partnership"
