@ci
Feature: Create users

  Scenario: Create
    Given I am logged in as "par_admin@example.com"
    And I open the url "/admin/people/create"
    And I click on the checkbox "#edit-roles-par-authority"
    And I add "par_authority@example.com" to the inputfield "#edit-mail"
    And I add "TestPassword" to the inputfield "#edit-pass-pass1"
    And I add "TestPassword" to the inputfield "#edit-pass-pass2"
    When I click on the button "#edit-submit"
    Then the element ".messages" is visible
    When I open the url "/admin/people/create"
    And I click on the checkbox "#edit-roles-par-organisation"
    And I add "par_business@example.com" to the inputfield "#edit-mail"
    And I add "TestPassword" to the inputfield "#edit-pass-pass1"
    And I add "TestPassword" to the inputfield "#edit-pass-pass2"
    And I click on the button "#edit-submit"
    Then the element ".messages" is visible
    When I open the url "/admin/people/create"
    And I click on the checkbox "#edit-roles-par-helpdesk"
    And I add "par_helpdesk@example.com" to the inputfield "#edit-mail"
    And I add "TestPassword" to the inputfield "#edit-pass-pass1"
    And I add "TestPassword" to the inputfield "#edit-pass-pass2"
    And I click on the button "#edit-submit"
    Then the element ".messages" is visible
