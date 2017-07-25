@ci
Feature: As a PAR user,
    I need the standard GDS Header,
    so that I know I'm on the Primary Authority Register.
    
    Scenario: Valid Login  Id
        Given I open the url "/user/login"
        Then the element "#edit-name" is visible
        And the element "#edit-pass" is visible
