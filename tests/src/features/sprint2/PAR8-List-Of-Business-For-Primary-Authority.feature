@Pending
Feature: As a Business User
    I want to be able to see, and select from, a list of my current Partnerships
    I can* review and confirm partnership details

    Background:
        Given I open the url "/login"
        And I add "BusinessUser" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

    Scenario: Create New Partnership
        Given I press "Continue"
        And I click on the checkbox "#toc"
        And I press "Continue"
        Then I expect that element "h1" contains the text "You need to review and confirm the following partnerships"
        When I click on the radio "#your-business-details"
        And I press "Continue"
        Then I expect that element "h1" contains the text "Review and confirm your partnership information"
