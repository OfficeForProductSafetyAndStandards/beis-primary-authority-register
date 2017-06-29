@Pending @journey3
Feature: As the (coordinated) Business User,
    I need to be able to see landing page for my co-ordinated Partnership,
    so that I can access the tasks required of me.

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
        And I expect that element "#your-members-directory" is visible

