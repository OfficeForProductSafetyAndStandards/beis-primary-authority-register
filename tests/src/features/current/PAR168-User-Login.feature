@Pending
Feature: As a Business user,
    I need to be able to edit my business details,
    So that the correct business details are taken into the new PAR.

    Background:
        Given I open the url "/login"

    Scenario Outline: Valid Login  Id
        And I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

        Examples:
            | user id                 | password  |
            | ValidPrimaryAuthorityId | Password1 |

    Scenario Outline: Invalid Login Id
        And I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#username-label" contains the text "Error"

        Examples:
            | user id                   | password  |
            | InvalidPrimaryAuthorityId | Password1 |

    Scenario Outline: Incorrect password
        Given I add "<user id>" to the inputfield "#username"
        And I add "<invalid password>" to the inputfield "#password"
        When I press "Login"
        Then I expect that element "#password-label" contains the text "Error"
        When I add "<valid password>" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

        Examples:
            | user id                 | invalid password | valid password |
            | ValidPrimaryAuthorityId | Password2        | Password1      |
