@ci
Feature: Business confirming Terms and Conditions

    Background:
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: User Journey Flow
        Given I open the url "/dv/primary-authority-partnerships/1"
        And I click on the link "Save and continue"
        When I click on the link "Review & confirm"
        Then the element "#par-flow-transition-partnership-details-terms" contains the text "Please Review the new Primary Authority terms and conditions and confirm that you agree with them."
   