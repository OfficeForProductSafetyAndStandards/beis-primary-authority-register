@ci
Feature: As a PAR user,
    I need the standard GDS Header,
    so that I know I'm on the Primary Authority Register.

    Scenario: Valid Login  Id
        Given I open the url "/"
        When I click on the link "Log in"
        Then I expect that element "#edit-name" is visible
        And I expect that element "#edit-pass" is visible
