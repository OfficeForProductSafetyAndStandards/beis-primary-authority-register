@Pending
Feature: As a PAR user,
I need to be able to access the Primary Authority Terms and Conditions in the footer across the site,
So that I can see the Primary Authority Terms and Conditions

    Background:
        Given I open the url "/login"
        And the element "#logged-out-footer" contains the text "Primary Authority Terms and Conditions"

    Scenario Outline: Valid Login  Id
        And I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        And the element "#logged-in-footer" contains the text "Primary Authority Terms and Conditions"

        Examples:
            | user id                 | username  | password  |
            | ValidPrimaryAuthorityId | Par User1 | Password1 |
