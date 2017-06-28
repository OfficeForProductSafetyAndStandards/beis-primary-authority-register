@Pending
Feature: As a Primary Authority Officer,
    I need to be able to see the main contact for the business, and their contact details,
    So that I can contact them.

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthority" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

