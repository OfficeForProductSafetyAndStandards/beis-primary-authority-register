@ci
Feature: Enforcement notice management

    Scenario: Enforcement notice management

        # PARTNERSHIPS DASHBOARD

#        Given I reset the test data
        Given I am logged in as "par_helpdesk@example.com"
        When I add "Business For Direct Partnership 1" to the inputfield "#edit-keywords"
        And I select the option with the text "- Any -" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        When I click on the button "a*=Approve partnership"
#
#        # APPROVAL FORM

        And I click on the button "#edit-next"
        And I click on the radio "#edit-confirm-authorisation-select-0"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"
        Then I expect that element "#par-rd-help-desk-approve" contains the text "Partnership is approved between"
        And I expect that element "#par-rd-help-desk-approve" contains the text "Business For Direct Partnership 1"
        And I click on the button "#edit-done"

        When I open the url "/helpdesk"
        When I add "Business For Direct Partnership 1" to the inputfield "#edit-keywords"
        And I select the option with the text "- Any -" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And I click on the button "td.views-field.views-field-authority-name a"
        Then the element "div time" contains any text

        # REFER FORM

        # BLOCK FORM
