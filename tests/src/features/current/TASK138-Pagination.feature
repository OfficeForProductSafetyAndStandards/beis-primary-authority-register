@Pending
Feature: As a PAR user
    I need pagination system
    In order to break down information into easier views

    Background:
        Given I open the url "/login"
        And I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

    Scenario Outline: Valid Login  Id
        Given I am  I open the url "/login"
        Then I expect that element "#pagination" contains the text "Pages:"
        Then I expect that element "#page-10" is visible
        When I click on the radio "#page-2"
        Then I expect that element "#page-2-highlighted" is visible

        Examples:
            | user id                 | password  |
            | ValidPrimaryAuthorityId | Password1 |
