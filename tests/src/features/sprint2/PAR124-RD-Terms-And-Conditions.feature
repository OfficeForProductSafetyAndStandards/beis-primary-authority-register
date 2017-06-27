@Pending
Feature: As a Business User,
I need to be able to agree to the RD Terms and Conditions,
So that I can confirm the new conditions for all my partnerships.

    Background:
        Given I open the url "/login"
        And I add "BusinessUser" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

    Scenario: Create New Partnership
        Given I press "Continue"
        Then I expect that element "#toc-text" contains the text "toc text"
        When I click on the checkbox "#toc"
        And I press "Continue"
