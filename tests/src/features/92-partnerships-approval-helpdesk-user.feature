@ci
Feature: Helpdesk approve partnership

    Scenario: Helpdesk approve partnership

        # HELPDESK DASHBOARD
        Given I am logged in as "par_helpdesk@example.com"
        When I add "Business For Direct Partnership 1" to the inputfield "#edit-keywords"
        And I select the option with the text "Confirmed by the Authority" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        When I click on the link "a*=Approve partnership"

        # APPROVAL CONFIRMATION SCREEN
        Then I expect that element "#par-rd-help-desk-approve" contains the text "Partnership is approved between"
        And I expect that element "#par-rd-help-desk-approve" contains the text "Business For Direct Partnership 1"
        # Press apply button on exposed form.
        And I click on the button "#edit-next"

        #
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-2"
        And I click on the button "#edit-next"

        # GO BACK TO HELPDESK
        When I open the url "/helpdesk"
        When I add "Business For Direct Partnership 1" to the inputfield "#edit-keywords"
        And I select the option with the text "- Any -" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And I click on the button "td.views-field.views-field-authority-name a"
        Then the element "div time" contains any text

        # REFER FORM

        # BLOCK FORM
