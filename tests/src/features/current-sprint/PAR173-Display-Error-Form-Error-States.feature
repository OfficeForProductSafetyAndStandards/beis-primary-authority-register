@Pending
Feature: As a PAR user
I need to know whenever I submit an invalid form
So that I can correct my form submission

    Scenario Outline: Error display check
        Given I open the url "/login"
        When I press "Login"
        Then I expect that element "#username-error" is visible
        And I expect that element "#password-error" is visible
        And I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        When I press "Login"
        Then I expect that element "#username-error" is visible
        And I expect that element "#password-error" is visible

        Examples:
            | user id | password |
            | a       | a        |
