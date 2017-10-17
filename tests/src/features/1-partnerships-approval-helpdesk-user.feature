@Pending
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # PARTNERSHIPS DASHBOARD

#        Given I reset the test data
        Given I have a screen that is 1920 by 1080 pixels
        And I am logged in as "par_helpdesk@example.com"
        When I add "Charlie" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        When I click on the button "a*=Approve partnership"
#
#        # APPROVAL FORM

        And I click on the button "#edit-next"
        And I click on the radio "#edit-confirm-authorisation-select-0"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"
        Then I expect that element "#par-rd-help-desk-approve" contains the text "Partnership is approved between"
        And I expect that element "#par-rd-help-desk-approve" contains the text "Charlie's"
        And I expect that element "#par-rd-help-desk-approve" contains the text "Upper West Side Borough Council"
        And I click on the button "#edit-done"

        # REFER FORM

        # BLOCK FORM
