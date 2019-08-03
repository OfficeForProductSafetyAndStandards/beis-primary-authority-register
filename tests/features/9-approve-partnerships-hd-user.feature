Feature: Helpdesk approve partnership

    Background:
        Given I open the path "/user/login"
        # Given I click the link text "Menu"
        # And I click the link text "Sign in"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        When I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"


    @ci @directpartnership @iostest
    Scenario: Helpdesk approve direct partnership

        Given I click the link text "Manage partnerships"
        And I add "Organisation For Direct Partnership" to the inputfield "#edit-keywords"
        And I select the option with the value "confirmed_business" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And there is "1" occurences of element ".par_helpdesk_partnership_list tbody tr"
        Then I click the link text "Approve partnership"

        # APPROVAL REVIEW SCREEN

        Then the element "#par-rd-help-desk-confirm" contains the text "Partnership between"
        And the element "#par-rd-help-desk-confirm" contains the text "Organisation For Direct Partnership"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-1"
        And I click on the button "#edit-next"

        # APPROVAL CONFIRMATION SCREEN

        Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
        And the element "#edit-partnership-info" contains the text "Organisation For Direct Partnership"
        And I click on the button "#edit-done"

        # GO BACK TO HELPDESK

    @ci @directpartnership @iostest
    Scenario: Helpdesk revoke partnership

        # REVOKE DIRECT PARTNERSHIP

        Given I click the link text "Manage partnerships"
        And I add "Organisation For Direct Partnership" to the inputfield "#edit-keywords"
        And I select the option with the value "confirmed_rd" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And there is "1" occurences of element ".par_helpdesk_partnership_list tbody tr"
        Then I click the link text "Revoke partnership"

        Then the element "h1.heading-xlarge" contains the text "Revoke a partnership"
        And the element "#edit-revocation-reason" is visible
        And I add "A reason for revoking" to the inputfield "#edit-revocation-reason"
        And I click on the button "#edit-next"

        Then the element "h1.heading-xlarge" contains the text "Partnership revoked"
        And the element "#edit-partnership-info" contains the text "The following partnership has been revoked"
        And the element "#edit-partnership-info" contains the text "Organisation For Direct Partnership"
        And I click on the button "#edit-done"

        And the element "#edit-keywords" is visible
        When I add "Organisation For Direct Partnership" to the inputfield "#edit-keywords"
        And I select the option with the value "revoked" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And the element ".table-scroll-wrapper" is visible
        And the element ".table-scroll-wrapper" contains the text "Organisation For Direct Partnership"
