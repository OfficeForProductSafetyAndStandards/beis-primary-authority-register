@Pending
Feature: View current and pending Partnerships

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthorityOfficer" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

    Scenario: Create New Partnership
        Given I open the url "/manage-partnerships"
        And I click on the radio "#view-current=pending-partnerships"
        And I press "Continue"
        Then I expect that element "#partnership-results" is visible
        When I click on the link "#partnership-1"
        Then I expect that element "#partnership-1-details" is visible
