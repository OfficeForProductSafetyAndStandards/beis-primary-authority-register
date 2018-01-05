Feature: Helpdesk approve partnership

    @ci
    Scenario: Helpdesk approve partnership

        # HELPDESK DASHBOARD

        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I add "Business For Direct Partnership 1" to the inputfield "#edit-keywords"
        And I select the option with the text "Confirmed by the Business" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click on the link "Approve partnership"

        # APPROVAL REVIEW SCREEN

        Then I expect that element "#par-rd-help-desk-confirm" contains the text "Partnership between"
        And I expect that element "#par-rd-help-desk-confirm" contains the text "Business For Direct Partnership 1"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"

        # APPROVAL CONFIRMATION SCREEN
        Then I expect that element "#edit-partnership-info" contains the text "The following partnership has been approved"
        And I expect that element "#edit-partnership-info" contains the text "Business For Direct Partnership 1"
        And I click on the button "#edit-done"

        # GO BACK TO HELPDESK

        When I open the url "/helpdesk"
        When I add "Business For Direct Partnership 1" to the inputfield "#edit-keywords"
        And I select the option with the text "- Any -" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And I click on the button "td.views-field.views-field-authority-name a"
        Then the element "div time" contains any text

        # CHECK REVOKING PARTNERSHIP DENIED DUE TO OUTSTANDING ENFORCEMENT NOTICE

    @ci @PAR1043
    Scenario: Verify Helpdesk user cannot approve partnerships with EN outstanding

        # HELPDESK DASHBOARD

        Given I click on the link "Log out"
        And I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I add "Business For Direct Partnership 1" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit"
        And I click on the link "Revoke"
        And I add "Some reasons for revoking partnership" to the inputfield "#partnership-revoke-reason"
        And I click on the button "#edit-next"
        Then I expect that element "#revocation-denied-message" contains the text "Revocation Denied"

        # REFER FORM

        # BLOCK FORM
