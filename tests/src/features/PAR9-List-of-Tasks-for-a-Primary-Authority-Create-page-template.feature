@Pending
Feature: As a Primary Authority Officer, I need to be able to see, and select from, a list of tasks for each Partnership, so that I can carry out the tasks required for Data Validation.

    Background:
        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: View List of tasks for each Partnership
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1"
        Then the element ".flow-link" contains the text "Review and confirm your partnership details"
        