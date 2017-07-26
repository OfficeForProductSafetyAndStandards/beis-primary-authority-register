@ci
Feature:  As a Primary Authority Officer,	
I need to be able to agree to the Primary Authority Terms and Conditions (for each Partnership),	
so that my Partnerships remain valid after 1st October.

    Background:
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I press "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships/1"
        Then the element "h1" contains the text "Updating the Primary Authority Register"
        When I click the link "#partnership-1"
        Then I expect that element "#accept-terms-and-conditions" is visible
