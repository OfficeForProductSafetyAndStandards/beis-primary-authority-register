Feature: Helpdesk approve partnership

    @ci
    Scenario: Helpdesk approve partnership

        # HELPDESK DASHBOARD

        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I add "Organisation For Direct Partnership 8" to the inputfield "#edit-keywords"
        And I select the option with the text "Confirmed by the Organisation" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click on the link "Approve partnership"

        # APPROVAL REVIEW SCREEN

        Then I expect that element "#par-rd-help-desk-confirm" contains the text "Partnership between"
        And I expect that element "#par-rd-help-desk-confirm" contains the text "Organisation For Direct Partnership 8"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"

        # APPROVAL CONFIRMATION SCREEN
        Then I expect that element "h1.heading-xlarge" contains the text "Partnership is approved"
        And I expect that element "#edit-partnership-info" contains the text "Organisation For Direct Partnership 8"
        And I click on the button "#edit-done"

        # GO BACK TO HELPDESK

        When I open the url "/helpdesk"
        When I add "Organisation For Direct Partnership 8" to the inputfield "#edit-keywords"
        And I select the option with the text "- Any -" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And I click on the button "td.views-field.views-field-par-flow-link a"
        Then the element "div time" contains any text

        # REVOKE

        When I open the url "/helpdesk"
        When I add "Direct Partnership For Revoking" to the inputfield "#edit-keywords"
#        And I select the option with the text "False" for element "#edit-revoked"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I expect that element ".table-scroll-wrapper" contains the text "Approve partnership"
        And I click on the link "Log out"

    @Pending @PAR1084
    Scenario: Verify Helpdesk user cannot approve partnerships with EN outstanding
        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I add "Direct Partnership For Revoking" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        When I click on the link "Approve partnership"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"

        # APPROVAL CONFIRMATION SCREEN

        Then I expect that element "h1.heading-xlarge" contains the text "Partnership is approved"
        And I expect that element "#edit-partnership-info" contains the text "Direct Partnership For Revoking"
        And I click on the button "#edit-done"
        When I add "Direct Partnership For Revoking" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        When I click on the link "Revoke partnership"
        And I add "Some reasons for revoking partnership" to the inputfield "#edit-revocation-reason"
        And I click on the button "#edit-next"
        Then I expect that element "#content" contains the text "Partnership revoked"
        When I click on the link "Helpdesk"
        When I add "Direct Partnership For Revoking" to the inputfield "#edit-keywords"
        And I select the option with the text "True" for element "#edit-revoked"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I expect that element ".table-scroll-wrapper" contains the text "Direct Partnership For Revoking"

        # CHECK NOT POSSIBLE TO ADD ENFORCEMENT NOTICE ON REVOKED PARTNERSHIP

        And I click on the link "Log out"
        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        When I click on the link "Search for a partnership"
        When I add "Direct Partnership For Revoking" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        Then I expect that element ".views-element-container" not contains the text "Direct Partnership For Revoking"

        # CHECK REVOKING PARTNERSHIP DENIED DUE TO OUTSTANDING ENFORCEMENT NOTICE

    @Pending @PAR1043
    Scenario: Verify Helpdesk user cannot approve partnerships with EN outstanding

        Given I click on the link "Log out"
        And I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I add "Charlie's Cafe's" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And I expect that element ".views-field views-field-par-flow-link views-field-par-flow-link-1" not contains text "Revoke partnership"
        And I add "Some reasons for revoking partnership" to the inputfield "#edit-revocation-reason"
        And I click on the button "#edit-next"
        Then I expect that element "#revocation-denied-message" contains the text "Revocation Denied"

        # REFER FORM

        # BLOCK FORM
