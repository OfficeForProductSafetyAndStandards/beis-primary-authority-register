@Pending
Feature: As RD,
    I need to be able to see a list of Business Details,
    So that I can contact them if required

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthority" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then the element "#logged-in-header" contains the text "Logged in"

