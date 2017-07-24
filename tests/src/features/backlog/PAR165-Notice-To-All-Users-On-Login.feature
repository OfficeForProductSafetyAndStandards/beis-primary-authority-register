@Pending
Feature: As RD,
I want to display a notice to all users when they log in
So that users are reminded about key data validation deadlines

    Background:
        Given I open the url "/login"

    Scenario Outline: Valid Login  Id
        And I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then the element "#list-deadlines" is visible

        Examples:
            | user id                 | password  |
            | ValidPrimaryAuthorityId | Password1 |

