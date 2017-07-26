@ci
Feature:  As a Primary Authority Officer,	
I need to be able to agree to the Primary Authority Terms and Conditions (for each Partnership),	
so that my Partnerships remain valid after 1st October.

    Background:
        Given I open the url "/user/login"
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details"
        And I scroll to element "#edit-confirmation"
        Then I click on the checkbox "#edit-confirmation"
