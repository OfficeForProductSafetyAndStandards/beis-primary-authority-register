@ci
Feature: Enforcement notice management

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Enforcement notice management

        # LOGIN SCREEN

        Given I am logged in as "par_helpdesk@example.com"
        And I select the option with the text "Awaiting Review" for element "#edit-partnership-status"
        When I add "Cambridgeshire" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        When I click on the link "Approve partnership"

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
