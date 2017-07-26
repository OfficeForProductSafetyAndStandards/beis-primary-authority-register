@ci
Feature: Edit Main Primary Authority Contact: Create form - As a Primary Authority Officer
    I need to be able to edit the field 'Main Primary Authority Contact' in the existing partnership details;
    So that the correct details are taken forward into the new PAR

    Background:
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details/edit-contact/1"
        And I add "Grover" to the inputfield "#edit-person-name"
        And I add "9876543210" to the inputfield "#edit-work-phone"
        And I add "grover@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        And the element "#edit-second-section" contains the text "Grover"
        And the element "#edit-second-section" contains the text "9876543210"
        And the element "#edit-second-section" contains the text "grover@example.com"
