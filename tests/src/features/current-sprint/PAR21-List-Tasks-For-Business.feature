@Pending @journey3
Feature: As the (coordinated) Business User,
    I need to be able to see landing page for my co-ordinated Partnership,
    so that I can access the tasks required of me.

    Background:
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible


    Scenario:
        Given I press "Continue"
        Then the element "#toc-title" contains the text "I confirm that my business agrees to the new terms and conditions"
        And I click on the checkbox "#toc"
        When I press "Confirm"
        Then the element "#your-business-details" is visible
        And the element "#your-members-directory" is visible

