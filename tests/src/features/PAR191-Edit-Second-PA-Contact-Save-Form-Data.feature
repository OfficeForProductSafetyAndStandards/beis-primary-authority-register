@ci
Feature: Edit Second Primary Authority Contact: Save form data	- As a Primary Authority Officer
I need to be able to edit the field 'Second Primary Authority Contact' in the existing partnership details;
So that the correct details are taken forward into the new PAR

    # Background:
    #     Given I open the url "/user/login"
    #     And I add "testuser" to the inputfield "#edit-name"
    #     And I add "testpwd" to the inputfield "#edit-pass"
    #     When I press "#edit-submit"
    #     Then I expect that element ".error-message" is not visible        
    
    Scenario: Edit Second Primary Authority Contact: Save form data
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details/edit-contact/3"
        Then the element "h1" contains the text "Edit the details for the contact at the Primary Authority"
        # And I add "Mr" to the inputfield "#edit-salutation"
        And I add "Grover" to the inputfield "#edit-person-name"
        And I add "9876543210" to the inputfield "#edit-work-phone"
        # And I add "099999999" to the inputfield "#edit-mobile-phone"
        And I add "grover@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        # Then the element "#edit-second-section" contains the text "Mr"
        And the element "#edit-third-section" contains the text "Grover"
        And the element "#edit-third-section" contains the text "9876543210"
        # And the element "#edit-third-section" contains the text "099999999"
        And the element "#edit-third-section" contains the text "grover@example.com"

