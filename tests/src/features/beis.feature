@ci
Feature: Check site homepage

    Scenario: Check site homepage
        Given I open the site "/"
        Then the title is "Welcome to Regulatory Authority | Regulatory Authority"