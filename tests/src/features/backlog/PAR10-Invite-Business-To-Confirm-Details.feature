@Pending
Feature: As a Primary Authority Officer
    I want to be able to invite the business to confirm their details
    so that the business information is up to date in the new PAR

    Background:
        Given I open the url "/login"
        And I add "PrimaryAuthority" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then the element "#logged-in-header" contains the text "Thank you for registering"

    Scenario: Create New Partnership
        Given I press "Continue"
        And I click on the checkbox "#toc"
        And I press "Continue"
        Then the element "h1" contains the text "You need to review and confirm the following partnerships"
        And the element "#your-partnerships" does exist
        And the element "#partnership-status" does exist
        When I click on the radio "#partnership-1"
        And I press "Continue"

        When I click on the checkbox "#partnership-arrangement-confirm"
        And I press "Continue"
        And I click on the checkbox "#invite-business-to-confirm-their-details"
        And I press "Continue"
        Then the element "#message-subject" contains the text "Important updates to the Primary Authority Register"
        When I press "Send invite"
        Then I expect that checkbox "#invite-business-to-confirm-their-details" is checked
