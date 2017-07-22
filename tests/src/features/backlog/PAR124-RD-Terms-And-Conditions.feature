@Pending
Feature: As a Business User,
I need to be able to agree to the RD Terms and Conditions,
So that I can confirm the new conditions for all my partnerships.


    Background:
        Given I open the url "/login"
        And I add "BusinessUser" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then the element "#logged-in-header" contains the text "Thank you for registering"

    Scenario:
        Given I press "Continue"
        Then the element "#toc-title" contains the text "I confirm that my business agrees to the new terms and conditions"
        And the element "#toc" is visible
