@Pending
Feature: As a Primary Authority Officer
    I need to be able to see, and select from, a list of tasks for each Partnership
    so that I can carry out the tasks required for Data Validation.


    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthority" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

    Scenario: Create New Partnership
        Given I press "Continue"
        And I click on the checkbox "#toc"
        And I press "Continue"
        Then I expect that element "h1" contains the text "Review and confirm your partnership information"
        When I click on the radio "#your-business-details"
