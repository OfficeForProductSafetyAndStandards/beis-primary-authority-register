@ci
Feature: As a Primary Authority Officer, I need to be able to see a list of my existing partnership details including| About the Partnership, Main Primary Authority Contact, Secondary Primary Authority Contact, Business Contact Name, Business Contact email, So that I can review my partnership details|

    Background:
        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: List Partnership Details: List Regulatory Areas
        Given I open the url "/dv/primary-authority-partnerships/1/details"
        Then the element "h1" contains the text "Viewing/confirming partnership details"
