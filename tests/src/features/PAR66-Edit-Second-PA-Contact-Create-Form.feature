@ci
Feature: Edit Second Primary Authority Contact: Create form	- As a Primary Authority Officer
    I need to be able to edit the field 'Second Primary Authority Contact' in the existing partnership details;
    So that the correct details are taken forward into the new PAR
    
    Background:
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: Edit Second Primary Authority Contact: Create form
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details/edit-contact/3"
        Then the element "h1" contains the text "Edit the details for the contact at the Primary Authority"