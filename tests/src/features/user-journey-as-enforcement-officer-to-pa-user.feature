@Pending
Feature: Enforcement officer to Primary Authority

    Background:
        # TEST DATA RESET
#        Given I reset the test data

    Scenario: Enforcement Officer/PA - Issue enforcement notice
        # LOGIN SCREEN

        Given I am logged in as "par_authority@example.com"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        And I open the url "/dashboard"
        Then I expect that element "#block-par-theme-content" contains the text "See all outstanding enforcement notices"
        When I click on the link "Search for a partnership"

        # PARTNERSHIP SEARCH

        When I add "ABCD" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        And I click on the button "td.views-field.views-field-authority-name a"

#        # ENFORCEMENT ACTION FORM
#
#        And I click on the link "scope of partnership"
#        Then the element "#partnership-scope" contains the text "Scope"
#        And I click on the link "Back"

        # CHECK PARTNERSHIP SCREEN

        # ENFORCEMENT NOTIFY

        Then I expect that element "h3" contains the text "About the business"
        When I click on the link "Send notification of enforcement action"
        And I click on the radio ".form-radio"
        And I click on the button "#edit-next"
        And I expect that element "#edit-action-summmary" becomes visible
        And I add "Some action summary text" to the inputfield "#edit-action-summmary"
        And I click on the radio ".form-radio"
        And I click on the radio "#edit-enforcement-type-proposed"
        And I click on the link "Add a legal entity"


        # ADD LEGAL ENTITIES

        And I add "A Legal Entity" to the inputfield "#edit-alternative-legal-entity"
        When I click on the button "#edit-next"
#        Then I expect that element "#par-enforcement-notice-raise" contains the text "A Legal Entity"
#        And I select the option with the text "Sole Trader" for element "#edit-legal-entity-type"
#        And I click on the button "#edit-next"

         # ADD ENFORCEMENT ACTION

        And I click on the link "Add an enforcement action"
        When I add "Enforcement action title" to the inputfield "#edit-title-of-action"
#        And I click on the radio ".form-label*=Explosives licensing"
        And I add "Enforcement action details" to the inputfield "#edit-details"
        And I click on the button "#edit-next"
#        Then I expect that element "#par-enforcement-notice-raise" contains the text "Enforcement action title"

        # CONFRIMATION SUMMARY CHECK ALL DETAILS

        And I click on the button "#edit-next"
        When I click on the link "Log out"

        # LOGIN AS PA USER TO CHECK MESSAGING

#        And I am logged in as "par_authority@example.com"
#        When I click on the link "See all messages"

        # MESSAGES SCREEN

#        Then I expect that element "h1" contains the text "Enforcement notification from"
#        And I press "Submit"
#        Then I expect that element "h1" contains the text "You have responded to the enforcement action by"
#
#        # LOGIN AS ENFORCEMENT OFFICER TO CHECK MESSAGING
#
#        When I am logged in as "par_enforcementofficer@example.com"
#        Then I expect that element "#secure-messages" contains the text "You have 1 new message"
#        When I click on the link "#secure-message"
#        Then I expect that element "h1" contains the text "Response to enforcement notification received by"
#        And I expect that element "#proposed-enforcement-ref" is not empty
#
#
#        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
#        When I add "ABCD" to the inputfield "#edit-keywords"
#        And I click on the button "#edit-submit-rd-helpdesk-dashboard"
#        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
#        When I click on the button "td.views-field.views-field-authority-name a"
#
#        # ENFORCEMENT ACTION FORM
#
#        When I click on the link "Enforcement action"
#        Then I expect that element "h1" contains the text "Send an enforcement notification to"
#        When I add "Bob" to the inputfield "#first-name"
#        And I add "Builder" to the inputfield "#last-name"
#        And I add "bob.builder@example.com" to the inputfield "#email"
#        And I add "A summary" to the inputfield "#enforcement-summary"
#        And I add "Premises address" to the inputfield "#address-premises"
#        And I select the option with the text "Proposed" for element "#enforcement-type"
#        And I add "Title for the action" to the inputfield "#action-title"
#        And I select the option with the text "Fire safety" for element "#regulatory-function"
#        And I add "Details for the action" to the inputfield "#action-details"
#        And I press "Submit"
#        Then I expect that element "h1" contains the text "Your enforcement notification has been submitted to"
#        And I expect that element "#enforcement-ref" is not empty
#        When I click on the link "Log out"
#
#         # LOGIN AS PA USER TO CHECK MESSAGING
#
#        And I am logged in as "par_authority@example.com"
#        Then I expect that element "#secure-messages" contains the text "You have 1 new message"
#        When I click on the link "#secure-message"
#        And I expect that element "h1" contains the text "Enforcement notification from"
#        And I click on the radio "#block-action"
#        And I press "Submit"
#        Then I expect that element "h1" contains the text "You have responded to the enforcement action by"
#
#         # LOGIN AS ENFORCEMENT OFFICER TO CHECK MESSAGING FOR BLOCKED ACTION
#
#        When I am logged in as "par_enforcementofficer@example.com"
#        Then I expect that element "#secure-messages" contains the text "You have 1 new message"
#        When I click on the link "#secure-message"
#        Then I expect that element "#action-message" contains the text "have BLOCKED this action"
#        And I expect that element "#proposed-enforcement-ref" is not empty
#                And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
#        When I add "ABCD" to the inputfield "#edit-keywords"
#        And I click on the button "#edit-submit-rd-helpdesk-dashboard"
#        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
#        When I click on the button "td.views-field.views-field-authority-name a"
#
#        # ENFORCEMENT ACTION FORM
#
#        When I click on the link "Enforcement action"
#        Then I expect that element "h1" contains the text "Send an enforcement notification to"
#        When I add "Bob" to the inputfield "#first-name"
#        And I add "Builder" to the inputfield "#last-name"
#        And I add "bob.builder@example.com" to the inputfield "#email"
#        And I add "A summary" to the inputfield "#enforcement-summary"
#        And I add "Premises address" to the inputfield "#address-premises"
#        And I select the option with the text "Proposed" for element "#enforcement-type"
#        And I add "Title for the action" to the inputfield "#action-title"
#        And I select the option with the text "Fire safety" for element "#regulatory-function"
#        And I add "Details for the action" to the inputfield "#action-details"
#        And I press "Submit"
#        Then I expect that element "h1" contains the text "Your enforcement notification has been submitted to"
#        And I expect that element "#enforcement-ref" is not empty
#        When I click on the link "Log out"
#
#         # LOGIN AS PA USER TO CHECK MESSAGING
#
#        And I am logged in as "par_authority@example.com"
#        Then I expect that element "#secure-messages" contains the text "You have 1 new message"
#        When I click on the link "#secure-message"
#        And I expect that element "h1" contains the text "Enforcement notification from"
#        And I click on the radio "#refer-action"
#        And I press "Submit"
#        Then I expect that element "h1" contains the text "You have responded to the enforcement action by"
