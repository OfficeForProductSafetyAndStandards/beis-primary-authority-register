@ci
Feature: As a PAR user,
    I need the standard GDS Header,
    so that I know I'm on the Primary Authority Register.

    Background:
    Given I open url "/login"
    And I am logged in as PAR user "testuser" with password "testpwd"
    
    Scenario: Valid Login  Id
        Given I open the url "/"
        When I click on the link "Log in"
        Then the element "#edit-name" is visible
        And the element "#edit-pass" is visible
