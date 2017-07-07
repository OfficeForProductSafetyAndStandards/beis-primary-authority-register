@Pending
Feature: As a PAR user,	
I need the standard GDS Header,	
so that I know I'm on the Primary Authority Register.

    Background:
        Given I open the url "/login"
        And I expect that element "#logged-out-header" contains the text "Log in"

    Scenario Outline: Valid Login  Id
        And I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "<user id>"
        And I expect that element "#logged-in-header" contains the text "Logout"

        Examples:
            | user id                 | username  | password  |
            | ValidPrimaryAuthorityId | Par User1 | Password1 |