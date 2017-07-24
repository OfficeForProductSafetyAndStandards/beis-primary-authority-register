@Pending
Feature: As a Primary Authority Officer,
    I need to be able to see a list of my existing partnership details including;
    -About the Partnership
    -Main Primary Authority Contact
    -Secondary Primary Authority Contact
    -Business Contact Name
    -Business Contact email
    So that I can review my partnership details


    Background:
        Given I open url "/login"
        And I am logged in as PAR user "testuser" with password "testpwd"
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
        When I click on the link "#partnership-1"
        Then the element "#partnership-details" is visible

