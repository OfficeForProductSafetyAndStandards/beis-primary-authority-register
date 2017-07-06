@Pending
Feature: As a Primary Authority Officer,
    I need to be able to confirm a Written Summary of Partnership Agreement (Direct business) has been agreed,
    So that I can continue my Partnership under the new regulations.

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthority" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Thank you for registering"

    Scenario:
        Given I press "Continue"
        Then the element "#toc-title" contains the text "I confirm that my business agrees to the new terms and conditions"
        And I click on the radio "#partnership-1"
        When I press "Continue"
        Then I expect that element "#toc-summary" contains the text "A written summary of partnership arrangements has been agreed with the business"

