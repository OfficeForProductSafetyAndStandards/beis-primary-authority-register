@ci
Feature: List of Tasks for a Primary Authority: Create page instance

    Background:
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: CList of Tasks for a Primary Authority: Create page instanceapture the state of a section in an entity
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1"
        Then the element "h1" contains the text "Updating the Primary Authority Register"
