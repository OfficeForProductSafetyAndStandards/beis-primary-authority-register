@Pending
Feature: As a PAR user,	
I need to be able to see the telephone number for the RD Helpdesk in the Header across the site
So that I can contact the Helpdesk if I require assistance.

    Background:
        Given I open the url "/login"
        And I expect that element "#logged-in-header" contains the text "Helpdesk telephone number"

    Scenario Outline: Valid Login  Id
        Given I add "<user id>" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        When I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Helpdesk telephone number"

        Examples:
            | user id                 | username  | password  |
            | ValidPrimaryAuthorityId | Par User1 | Password1 |