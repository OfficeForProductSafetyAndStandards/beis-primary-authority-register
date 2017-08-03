@ci
Feature: Check site homepage

    Scenario: Check site homepage
        Given I open the site "/"
        And I check the homepage response code is 200
        Then the title is "Welcome to Primary Authority Register | Primary Authority Register"
