@ci @userjourney1
Feature: User Journey 1 (happy path)

Background:
    Given I open the url "/user/login"
    And I add "par_authority@example.com" to the inputfield "#edit-name"
    And I add "TestPassword" to the inputfield "#edit-pass"
    And I click on the button "#edit-submit"
    And I open the url "/admin/par-data-test-reset"
    And I click on the link "Log out"

Scenario: User Journey 1 - Documentation   
    # HOMEPAGE 
    Given I open the url "/user/login"
    # LOGIN SCREEN
    And I add "par_authority@example.com" to the inputfield "#edit-name"
    And I add "TestPassword" to the inputfield "#edit-pass"
    When I click on the button "#edit-submit"
    # WELCOME SCREEN
    Then I expect that element ".error-message" is not visible
    When I click on the button ".button-start"
    # PARTNERSHIPS DASHBOARD
    And I click on the link "ABCD Mart"
    # TERMS AND CONDITIONS SCREEN
    And I click on the checkbox "#edit-terms-conditions"
    And I click on the button "#edit-next"
    # DOCUMENTATION
    When I click on the link "Review and confirm your documentation for ABCD Mart"
    And I click on the link "edit"
    And I click on the radio "#edit-document-type-authority-advice"
    And I click on the checkbox "#edit-regulatory-functions-business-advice"
    And I click on the checkbox "#edit-regulatory-functions-authority-advice"
    And I click on the checkbox "#edit-regulatory-functions-background-information"
    When I click on the button "#edit-next"
    # Then the element ".placeholder" not contains the text "Error"
    # When I click on the link "Go back to your partnerships"
    # Then the element "h1" contains the text "List of Partnerships for a Primary Authority"
    # And I click on the link "Log out"
