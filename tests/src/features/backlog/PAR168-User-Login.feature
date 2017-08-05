@Pending
Feature: As a PAR user
    I need a login
    so that I can access PAR

    Background:
        Given I open the url "/login"

    Scenario Outline: Valid Login  Id
        And I add "<user id>" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I press "Log in"
        Then the element "#logged-in-header" contains the text "Logged in"

        Examples:
            | user id                 | password  |
            | ValidPrimaryAuthorityId | Password1 |

    Scenario Outline: Invalid Login Id
        And I add "<user id>" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I press "Log in"
        Then the element "#username-label" contains the text "Error"

        Examples:
            | user id                   | password  |
            | InvalidPrimaryAuthorityId | Password1 |

    Scenario Outline: Incorrect password
        And I add "<user id>" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        When I press "Login"
        Then the element "#password-label" contains the text "Error"
        When I add "<valid password>" to the inputfield "#password"
        And I press "Log in"
        Then the element "#logged-in-header" contains the text "Logged in"

        Examples:
            | user id                 | invalid password | valid password |
            | ValidPrimaryAuthorityId | Password2        | Password1      |
