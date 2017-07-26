@ci
Feature:Create a view structured as illustrated in the attached image.

    Background:
        Given I open the url "/user/login"
        And I add "testuser" to the inputfield "#edit-name"
        And I add "testpwd" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships"
        # Then the element "h1" contains the text "Updating the Primary Authority Register"
        Then I expect that element "#view-organisation-table-column" is visible    

