@Pending
Feature: As a PAR user,	
I need to see the standard GDS Footer across the site 
so that I know I'm on the Primary Authority Register

    Background:
        Given I open the url "/login"
        Then I expect that element "#logged-in-footer" contains the text "Helpdesk telephone number"

    Scenario Outline: Valid Login  Id
        Given I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-footer" contains the text "Helpdesk telephone number"

        Examples:
            | user id                 | username  | password  |
            | ValidPrimaryAuthorityId | Par User1 | Password1 |