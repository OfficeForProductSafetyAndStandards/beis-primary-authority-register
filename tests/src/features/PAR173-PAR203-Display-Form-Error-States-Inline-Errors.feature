@ci
Feature: As a PAR user
    I need to know whenever I submit an invalid form
    So that I can correct my form submission

    Background:
    Given I open url "/login"
    And I am logged in as PAR user "testuser" with password "testpwd"
    
    Scenario: Error display check
        Given I open the url "/styleguide/forms"
#        When I submit the form "#par-styleguide-form-controller"
        When I click on the button "#edit-next"
        And I wait on element ".error-message" for 1000ms to be visible
        Then I should see "6" occurrences of validation messages
        And the element ".error-summary-list" is visible
