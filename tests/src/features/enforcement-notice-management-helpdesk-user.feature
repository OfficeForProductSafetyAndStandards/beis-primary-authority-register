@ci
Feature: Enforcement notice management

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Enforcement notice management

        # LOGIN SCREEN

        Given I am logged in as "par_admin@example.com"
        And I open the url "/user/202"
        And I click on the link "Edit"
        And I scroll to element "#edit-pass-pass2"
        And I add "TestPassword" to the inputfield "#edit-pass-pass1"
        # Then I expect that element ".messages" is not visible
        And I add "TestPassword" to the inputfield "#edit-pass-pass2"
        # Then I expect that element ".messages" is not visible
        When I click on the button "#edit-submit"
        Then the element ".messages" contains the text "The changes have been saved"
        And I open the url "/user/logout"


        # PARTNERSHIPS DASHBOARD

        Given I am logged in as "5b49820e39cb21bd5a573fe79c6f7e29@localhost.localdomain"
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
