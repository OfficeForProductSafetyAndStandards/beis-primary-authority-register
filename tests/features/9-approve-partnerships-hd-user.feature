Feature: Helpdesk approve partnership

    Background:
        Given I open the path "/user/login"
        # Given I click the link text "Menu"
        # And I click the link text "Log in"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        When I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"


    @ci @directpartnership @iostest
    Scenario: Helpdesk approve direct partnership

        Given I add "Beko PLC" to the inputfield "#edit-keywords"
        # And I select the option with the value "confirmed_business" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And there is "1" occurences of element ".par-rd-helpdesk-dashboard tbody tr"
        Then I click the link text "Approve partnership"

        # APPROVAL REVIEW SCREEN

        Then the element "#par-rd-help-desk-confirm" contains the text "Partnership between"
        And the element "#par-rd-help-desk-confirm" contains the text "Beko PLC"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"

        # APPROVAL CONFIRMATION SCREEN
        
        Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
        And the element "#edit-partnership-info" contains the text "Beko PLC"
        And I click on the button "#edit-done"

        # GO BACK TO HELPDESK

    @ci @directpartnership @iostest
    Scenario: Helpdesk revoke partnership

        # REVOKE DIRECT PARTNERSHIP

        Given I add "Andrew Brownsword Hotels" to the inputfield "#edit-keywords"
        And I select the option with the value "0" for element "#edit-revoked"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click the link text "Revoke partnership"
        And the element "#edit-revocation-reason" is visible
        And I add "A reason for revoking" to the inputfield "#edit-revocation-reason"
        And I click on the button "input[name=\"next\"]"
        And the element "#edit-partnership-info" contains the text "The following partnership has been revoked"
        And I open the path "/helpdesk"
        And the element "#edit-keywords" is visible
        When I add "Andrew Brownsword Hotels" to the inputfield "#edit-keywords"
        And I select the option with the value "1" for element "#edit-revoked"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And the element ".table-scroll-wrapper" is visible
        And the element ".table-scroll-wrapper" contains the text "Andrew Brownsword Hotels"

        # REVOKE PARTNERSHIP (SHORTER STEP VERSION)

        # And I successfully revoke a coordinated partnership
