@ci
Feature: Edit About the Partnership: Load form data - As a Primary Authority Officer
    I need to be able to edit the field 'About the Partnership' in the existing partnership details
    So that the correct details are taken forward into the new PAR
    
    Background:
        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: Edit About the Partnership: Load form data
        Given I click on the button "Continue to your Partnerships"
        And I click on the business "ABCD Mart"
        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        And I click on the link "Review and confirm your partnership details"
        And I click on the link "edit"
        And I add "test change information about the partnership" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"
        Then the element "#edit-first-section" contains the text "test change information about the partnership"
        
    
