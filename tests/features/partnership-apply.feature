Feature: Apply for a new partnership

    Background:
        Given I am logged in as "par_authority@example.com"

    @ci @direct
    Scenario: Apply for a direct partnership with a new business
        When I apply for a direct partnership
        And I enter information about the partnership
        And I enter the business name "Business-for-partnership-application"
        And I enter the business address
        And I enter the contact details for the business
        And I invite the business contact
        And I review the partnership application between "Lower East Side Borough Council" and "Business-for-partnership-application"

    @ci @direct
    Scenario: Apply for a direct partnership with an existing business
        When I apply for a direct partnership
        And I enter information about the partnership
        And I enter the business name "Sesame Street Farming"
        And I choose an existing business
        And I review the partnership application between "Lower East Side Borough Council" and "Business-for-partnership-application"

    @ci @coordinated
    Scenario: New coordinated Partnership
        When I apply for a coordinated partnership
        And I enter information about the partnership
        And I enter the business name "Coordinator-for-partnership-application"
        And I enter the business address
        And I enter the contact details for the business
        And I invite the business contact
        And I review the partnership application between "Lower East Side Borough Council" and "Coordinator-for-partnership-application"
