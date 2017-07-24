@ci
Feature: Edit About the Partnership: Save form data	- As a Primary Authority Officer
    I need to be able to edit the field 'About the Partnership' in the existing partnership details
    So that the correct details are taken forward into the new PAR

    Background:
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I press "Login"
        And I expect that element ".error-message" is not visible

    Scenario: Edit About the Partnership: Save form data
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details/edit-about"
        And the element "h1" contains the text "Edit the information about the Partnership"
        And I add "test change" to the inputfield "#edit-about-partnership"
        When I click on the button "#edit-next"
        Then the element "h1" contains the text "Viewing/confirming partnership details"

