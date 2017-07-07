@Pending
Feature: As a business user,
    I need to be able to see a list of my business details,
    So that I can confirm they are still valid for the new PAR.

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthority" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

