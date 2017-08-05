@Pending
Feature:  Edit Main Primary Authority Contact: Save form data - As a Primary Authority Officer
    I need to be able to edit the field 'Main Primary Authority Contact' in the existing partnership details;
    So that the correct details are taken forward into the new PAR

    Background:
        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships/1/details"
        And I click on the link "a=*Edit" in the page area "#edit-authority-contacts"
        Then the element "h1" contains the text "Updating the Primary Authority Register"
        And I add "Grover" to the inputfield "#edit-first-name"
        And I add "the Muppet" to the inputfield "#edit-last-name"
        And I add "9876543210" to the inputfield "#edit-work-phone"
        And I add "grover@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        And the element "#edit-authority-contacts" contains the text "Grover"
        And the element "#edit-authority-contacts" contains the text "9876543210"
        And the element "#edit-authority-contacts" contains the text "grover@example.com"
