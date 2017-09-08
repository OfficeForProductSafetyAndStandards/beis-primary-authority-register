@Pending
Feature: Enforcement Officer/PA - Enforcement Notice Process

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Enforcement Officer/PA - Issue enforcement notice
        # LOGIN SCREEN

        Given I am logged in as "par_enforcementofficer@example.com"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
        When I add "ABCD" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-rd-helpdesk-dashboard"
        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
        When I click on the button "td.views-field.views-field-authority-name a"

        # ENFORCEMENT ACTION FORM

        When I click on the link "Enforcement action"
        Then I expect that element "h1" contains the text "Send an enforcement notification to"
        When I add "Bob" to the inputfield "#first-name"
        And I add "Builder" to the inputfield "#last-name"
        And I add "bob.builder@example.com" to the inputfield "#email"
        And I add "A summary" to the inputfield "#enforcement-summary"
        And I add "Premises address" to the inputfield "#address-premises"
        And I select the option with the text "Proposed" for element "#enforcement-type"
        And I add "Title for the action" to the inputfield "#action-title"
        And I select the option with the text "Fire safety" for element "#regulatory-function"
        And I add "Details for the action" to the inputfield "#action-details"
        And I press "Submit"
        Then I expect that element "h1" contains the text "Your enforcement notification has been submitted to"
        And I expect that element "#enforcement-ref" is not empty
        When I click on the link "Log out"

        # LOGIN AS PA USER TO CHECK MESSAGING

        And I am logged in as "par_authority@example.com"
        Then I expect that element "#secure-messages" contains the text "You have 1 new message"
        When I click on the link "#secure-message"
        Then I expect that element "h1" contains the text "Enforcement notification from"
        And I press "Submit"
        Then I expect that element "h1" contains the text "You have responded to the enforcement action by"

        # LOGIN AS ENFORCEMENT OFFICER TO CHECK MESSAGING

        When I am logged in as "par_enforcementofficer@example.com"
        Then I expect that element "#secure-messages" contains the text "You have 1 new message"
        When I click on the link "#secure-message"
        Then I expect that element "h1" contains the text "Response to enforcement notification received by"
        And I expect that element "#proposed-enforcement-ref" is not empty
        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
        When I add "ABCD" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-rd-helpdesk-dashboard"
        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
        When I click on the button "td.views-field.views-field-authority-name a"

        # ENFORCEMENT ACTION FORM

        When I click on the link "Enforcement action"
        Then I expect that element "h1" contains the text "Send an enforcement notification to"
        When I add "Bob" to the inputfield "#first-name"
        And I add "Builder" to the inputfield "#last-name"
        And I add "bob.builder@example.com" to the inputfield "#email"
        And I add "A summary" to the inputfield "#enforcement-summary"
        And I add "Premises address" to the inputfield "#address-premises"
        And I select the option with the text "Proposed" for element "#enforcement-type"
        And I add "Title for the action" to the inputfield "#action-title"
        And I select the option with the text "Fire safety" for element "#regulatory-function"
        And I add "Details for the action" to the inputfield "#action-details"
        And I press "Submit"
        Then I expect that element "h1" contains the text "Your enforcement notification has been submitted to"
        And I expect that element "#enforcement-ref" is not empty
        When I click on the link "Log out"

         # LOGIN AS PA USER TO CHECK MESSAGING

        And I am logged in as "par_authority@example.com"
        Then I expect that element "#secure-messages" contains the text "You have 1 new message"
        When I click on the link "#secure-message"
        And I expect that element "h1" contains the text "Enforcement notification from"
        And I click on the radio "#block-action"
        And I press "Submit"
        Then I expect that element "h1" contains the text "You have responded to the enforcement action by"
        When I click on the link "Log out"

         # LOGIN AS ENFORCEMENT OFFICER TO CHECK MESSAGING FOR BLOCKED ACTION

        And I am logged in as "par_enforcementofficer@example.com"
        Then I expect that element "#secure-messages" contains the text "You have 1 new message"
        When I click on the link "#secure-message"
        Then I expect that element "#action-message" contains the text "have BLOCKED this action"
        And I expect that element "#proposed-enforcement-ref" is not empty

