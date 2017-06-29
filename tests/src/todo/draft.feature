@Pending
Feature: As a Primary Authority Officer,
    I need to be able to confirm a Written Summary of Partnership Agreement (Direct business) has been agreed,
    So that I can continue my Partnership under the new regulations.

    Background:
        Given I open the url "/login"
        And I add "BusinessUser" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Thank you for registering"

    Scenario:
        Given I press "Continue"
        Then the element "#toc-title" contains the text "I confirm that my business agrees to the new terms and conditions"
        And I click on the checkbox "#toc"
        When I press "Confirm"
        Then I expect that element "#your-business-details" is visible
        And I expect that element "#your-additional-premises" is visible
        When I click on the radio "#your-business-details"
        And I press "Continue"
        Then I expect that element "#review-and-confirm-partnership-information" contains the text "Review and confirm your partnership information"
        When I click on the checkbox "#confirm-partnership-information"
        And I press "Continue"
        When I click on the radio "#your-additional-premises"
        And I press "Continue"
        And I click on the checkbox "#select-all-premises"
        And I press "Confirm"
        Then I expect that element "<string>" is visible
