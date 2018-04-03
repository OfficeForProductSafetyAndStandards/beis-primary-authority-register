Feature: Helpdesk approve partnership

    @ci
    Scenario: Helpdesk approve partnership

        #LOGIN
        
        Given I am logged in as "par_helpdesk@example.com"
        When I add "Organisation For Direct Partnership 4" to the inputfield "#edit-keywords"
        # And I select the option with the value "confirmed_business" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click the link text "Approve partnership"

        # APPROVAL REVIEW SCREEN

        Then the element "#par-rd-help-desk-confirm" contains the text "Partnership between"
        And the element "#par-rd-help-desk-confirm" contains the text "Organisation For Direct Partnership 4"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"

        # APPROVAL CONFIRMATION SCREEN
        Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
        And the element "#edit-partnership-info" contains the text "Organisation For Direct Partnership 4"
        And I click on the button "#edit-done"

        # GO BACK TO HELPDESK

        When I open the path "/helpdesk"
        When I add "Organisation For Direct Partnership 4" to the inputfield "#edit-keywords"
        And I select the option with the value "All" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And I click on the button "td.views-field.views-field-authority-name a"
        Then the element "div time" does not exist

        # # REVOKE

        # When I open the path "/helpdesk"
        # When I add "Direct Partnership For Revoking8" to the inputfield "#edit-keywords"
        # And I select the option with the value "False" for element "#edit-revoked"
        # And I click on the button "#edit-submit-helpdesk-dashboard"
        # Then the element ".table-scroll-wrapper" contains the text "Approve partnership"
        # And I click the link text "Log out"

    # @Pending @PAR1084
    # Scenario: Verify Helpdesk user cannot approve partnerships with EN outstanding
    #     Given I open the path "/user/login"
    #     And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
    #     And I add "TestPassword" to the inputfield "#edit-pass"
    #     When I click on the button "#edit-submit"
    #     When I add "Direct Partnership For Revoking8" to the inputfield "#edit-keywords"
    #     And I click on the button "#edit-submit-helpdesk-dashboard"
    #     When I click the link text "Approve partnership"
    #     And I click on the radio "#edit-confirm-authorisation-select"
    #     And I click on the radio "#edit-partnership-regulatory-functions-2"
    #     And I click on the button "#edit-next"

    #     # APPROVAL CONFIRMATION SCREEN

    #     Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
    #     And the element "#edit-partnership-info" contains the text "Direct Partnership For Revoking8"
    #     And I click on the button "#edit-done"
    #     When I add "Direct Partnership For Revoking8" to the inputfield "#edit-keywords"
    #     And I click on the button "#edit-submit-helpdesk-dashboard"
    #     When I click the link text "Revoke partnership"
    #     And I add "Some reasons for revoking partnership" to the inputfield "#edit-revocation-reason"
    #     And I click on the button "#edit-next"
    #     Then the element "#content" contains the text "Partnership revoked"
    #     When I click the link text "Helpdesk"
    #     When I add "Direct Partnership For Revoking8" to the inputfield "#edit-keywords"
    #     And I select the option with the value "1" for element "#edit-revoked"
    #     And I click on the button "#edit-submit-helpdesk-dashboard"
    #     Then the element ".table-scroll-wrapper" contains the text "Direct Partnership For Revoking8"

    #     # CHECK NOT POSSIBLE TO ADD ENFORCEMENT NOTICE ON REVOKED PARTNERSHIP

    #     And I click the link text "Log out"
    #     Given I open the path "/user/login"
    #     And I add "par_authority@example.com" to the inputfield "#edit-name"
    #     And I add "TestPassword" to the inputfield "#edit-pass"
    #     And I click on the button "#edit-submit"
    #     When I click the link text "Search for a partnership"
    #     When I add "Direct Partnership For Revoking8" to the inputfield "#edit-keywords"
    #     And I click on the button "#edit-submit-partnership-search"
    #     Then the element ".views-element-container" does not contain the text "Direct Partnership For Revoking8"

        # CHECK REVOKING PARTNERSHIP DENIED DUE TO OUTSTANDING ENFORCEMENT NOTICE

    # @Pending @PAR1043
    # Scenario: Verify Helpdesk user cannot approve partnerships with EN outstanding

    #     And I open the path "/user/login"
    #     And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
    #     And I add "TestPassword" to the inputfield "#edit-pass"
    #     When I click on the button "#edit-submit"
    #     And I add "Charlie" to the inputfield "#edit-keywords"
    #     And I click on the button "#edit-submit-helpdesk-dashboard"
    #     And the element ".views-field views-field-par-flow-link views-field-par-flow-link-1" does not contain the text "Revoke partnership"
    #     And I add "Some reasons for revoking partnership" to the inputfield "#edit-revocation-reason"
    #     And I click on the button "#edit-next"
    #     Then the element "#revocation-denied-message" contains the text "Revocation Denied"

        # REFER FORM

        # BLOCK FORM
