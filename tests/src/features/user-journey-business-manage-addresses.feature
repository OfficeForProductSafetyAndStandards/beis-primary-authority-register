@ci @journey3
Feature: As the (coordinated) Business User,
    I need to be able to see landing page for my co-ordinated Partnership,
    so that I can access the tasks required of me.

Background:
    Given I open the url "/user/login"
    And I add "dadmin" to the inputfield "#edit-name"
    And I add "password" to the inputfield "#edit-pass"
    And I click on the button "#edit-submit"
    And I open the url "/admin/par-data-test-reset"
    And I open the url "/user/logout"

    Scenario: Manage business related addresses
        Given I open the url "/user/login"
        And I add "par_business@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible
        When I click on the button ".button-start"
        # PARTNERSHIPS DASHBOARD
        And I click on the link "ABCD Mart"
        # TERMS AND CONDITIONS SCREEN
        And I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        Then the element "h3" contains the text "Main contact at the Authority"
        When I click on the link "Review and confirm your business details"
