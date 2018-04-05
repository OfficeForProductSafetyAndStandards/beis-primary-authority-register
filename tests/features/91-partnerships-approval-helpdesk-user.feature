Feature: Helpdesk approve partnership

    @ci
    Scenario: Helpdesk approve partnership

        #LOGIN
        
        Given I am logged in as "par_helpdesk@example.com"
        When I add "Organisation For Direct Partnership" to the inputfield "#edit-keywords"
        # And I select the option with the value "confirmed_business" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click the link text "Approve partnership"

        # APPROVAL REVIEW SCREEN

        Then the element "#par-rd-help-desk-confirm" contains the text "Partnership between"
        And the element "#par-rd-help-desk-confirm" contains the text "Organisation For Direct Partnership"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"

        # APPROVAL CONFIRMATION SCREEN
        Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
        And the element "#edit-partnership-info" contains the text "Organisation For Direct Partnership"
        And I click on the button "#edit-done"

        # GO BACK TO HELPDESK

        When I open the path "/helpdesk"
        When I add "Organisation For Direct Partnership" to the inputfield "#edit-keywords"
        And I select the option with the value "All" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And I click on the button "td.views-field.views-field-par-flow-link a"
        Then the element "div time" does not exist

        # # APPROVE PARTNERSHIP FOR REVOKING

        # When I open the path "/helpdesk"
        # When I add "Organisation For Direct Partnership To Revoke" to the inputfield "#edit-keywords"
        # And I select the option with the value "All" for element "#edit-partnership-status"
        # And I click on the button "#edit-submit-helpdesk-dashboard"
        # Then I click the link text "Approve partnership"
        # Then the element "#par-rd-help-desk-confirm" contains the text "Partnership between"
        # And the element "#par-rd-help-desk-confirm" contains the text "Organisation For Direct Partnership"
        # And I click on the radio "#edit-confirm-authorisation-select"
        # And I click on the radio "#edit-partnership-regulatory-functions-2"
        # And I click on the button "#edit-next"
        # Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
        # And the element "#edit-partnership-info" contains the text "Organisation For Direct Partnership"
        # And I click on the button "#edit-done"

        # REVOKE PARTNERSHIP

        When I open the path "/helpdesk"
        When I add "Organisation For Direct Partnership To Revoke" to the inputfield "#edit-keywords"
        And I select the option with the value "0" for element "#edit-revoked"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click the link text "Revoke partnership"
        And I add "A reason for revoking" to the inputfield "#edit-revocation-reason"
        And I click on the button "#edit-next"
        And the element "#edit-partnership-info" contains the text "The following partnership has been revoked"
        And I click on the button "#edit-done"
        When I add "Organisation For Direct Partnership To Revoke" to the inputfield "#edit-keywords"
        And I select the option with the value "1" for element "#edit-revoked"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And the element ".table-scroll-wrapper" contains the text "Organisation For Direct Partnership To Revoke"
        