@ci
Feature: Capture the state of a section in an entity; ie., whether or not an entity has been confirmed.

    Background:
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I press "#edit-submit"
        Then I expect that element ".error-message" is not visible    

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details"
        Then the element "h1" contains the text "Viewing/confirming partnership details"
        Then I expect that element "#partnership-status" is visible
