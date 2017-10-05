@ci @Pending
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # LOGIN SCREEN

        Given I am logged in as "par_admin@example.com"
        And I reset the test data
        And I open the url "/admin/people"
        And I select the option with the value "par_helpdesk" for element "#edit-role"
        And I click on the button "#edit-submit-user-admin-people"
        And I click on the button "td.views-field.views-field-name a.username"
        And I click on the link "Edit"
        And I scroll to element "#edit-pass-pass2"
        And I add "TestPassword" to the inputfield "#edit-pass-pass1"
        # Then I expect that element ".messages" is not visible
        And I add "TestPassword" to the inputfield "#edit-pass-pass2"
        # Then I expect that element ".messages" is not visible
        When I click on the button "#edit-submit"
#        Then the element ".messages" contains the text "The changes have been saved"
        And I store the user email address
        And I open the url "/user/logout"

        # PARTNERSHIPS DASHBOARD

        Given I am logged in as stored user
        And I select the option with the text "Active" for element "#edit-partnership-status"
#        When I add "Cambridgeshire" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        When I click on the button "td.views-field.views-field-par-flow-link a"

        # APPROVAL FORM

        And I click on the button "#edit-next"
        And I expect that element ".error-summary" does exist
        And I click on the radio "#edit-confirm-authorisation-select-0"
        And I click on the button "#edit-next"
        And I expect that element ".error-summary" does exist
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"
        Then I expect that element "#par-rd-help-desk-approve" contains the text "Partnership is approved between"
        And I click on the button "#edit-done"

        # REFER FORM

        # BLOCK FORM
