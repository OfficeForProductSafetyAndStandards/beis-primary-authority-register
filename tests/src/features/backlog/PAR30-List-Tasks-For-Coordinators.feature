@Pending
Feature: As the Coordinator,
    I need to be able to see landing page for my co-ordinated Partnership,
    so that I can access the tasks required of me.

    Background:
        Given I open the url "/login"
        And I add "Coordinator" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then the element "#logged-in-header" contains the text "Thank you for registering"


    Scenario:
        Given I press "Continue"
        Then the element "#toc-title" contains the text "I confirm that my business agrees to the new terms and conditions"
        And I click on the checkbox "#toc"
        When I press "Confirm"
        Then the element "#your-business-details" is visible
        And the element "#your-members-directory" is visible

