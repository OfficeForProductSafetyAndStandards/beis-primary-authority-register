@Pending
Feature: As a Business user,
    I need to be able to edit my business details,
    So that the correct business details are taken into the new PAR.

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthority" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then the element "#logged-in-header" contains the text "Logged in"

