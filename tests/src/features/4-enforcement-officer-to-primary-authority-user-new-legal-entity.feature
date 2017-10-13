@ci
Feature: Enforcement Officer/PA - Enforcement Notice Process

    Scenario: Enforcement Officer/PA - Issue enforcement notice
        # LOGIN SCREEN

        Given I reset the test data
        And I am logged in as "par_authority@example.com"
        And I click on the link "Search for a partnership"
        When I add "Charlie" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        When I click on the button "td.views-field.views-field-authority-name a"

        # ENFORCEMENT ACTION FORM

        When I click on the link "Send notification of enforcement action"
        Then I expect that element "h3" contains the text "Which authority are you acting on behalf of"
        When I click on the radio ".form-radio"
        And I click on the button "#edit-next"

        # CHOOSE MEMBER

        Then I expect that element "#par-enforce-organisation" contains the text "Choose the member to enforce"
        And I click on the radio "label*=Hooper"
        And I click on the button "#edit-next"

        # CHOOSE LEGAL ENTITY

        And I scroll to element "#edit-legal-entities-select-add-new"
        And I click on the radio "#edit-legal-entities-select-add-new"
        And I add "Legal Entity 1" to the inputfield "#edit-alternative-legal-entity"
        And I click on the button "#edit-next"

      # ENFORCEMENT SUMMARY

        And I add "action summary enforcement notice" to the inputfield "#edit-action-summary"
        And I click on the radio "#edit-enforcement-type-proposed"
        And I click on the button "#edit-next"

        And I add "Title of enforcement notice Two" to the inputfield "#edit-title-of-action"
        And I click on the radio ".option*=Cookie control"
        And I add "Some details about the enforcement notice" to the inputfield "#edit-details"
        And I click on the button "#edit-next"
        And I scroll to element "#par-enforcement-notice-raise-confirm"
        Then I expect that element "#par-enforcement-notice-raise-confirm" contains the text "action summary enforcement notice"
        And I expect that element "#par-enforcement-notice-raise-confirm" contains the text "Title of enforcement notice Two"
        And I expect that element "#par-enforcement-notice-raise-confirm" contains the text "Some details about the enforcement notice"
        And I expect that element "#par-enforcement-notice-raise-confirm" contains the text "Once the primary authority receives this notification, they have 5 working days to respond to you if they intend to block the action"
        And I expect that element "#par-enforcement-notice-raise-confirm" contains the text "You will be notified by email of the outcome of this notification"
        When I click on the button "#edit-save"
        Then I expect that element "h1.heading-xlarge" contains the text "Primary Authority Register"

        # REFER FORM

        # BLOCK FORM

#        And I add "An enforcement action title" to the inputfield "#last-name"
#        When I click on the radio "#edit-enforcement-type-proposed"
#
#      # ENFORCEMENT REVIEW
#
#        Then I expect that element "#par-enforcement-notice-raise-confirm" contains the text "Title of the enforcement notice"
#        Then I expect that element "#par-enforcement-notice-raise-confirm" contains the text "Some details about the enforcement notice"
#        Then I expect that element "#par-enforcement-notice-raise-confirm" contains the text "An enforcement action title"
#        And I click on the button "#edit-next"
#        Then I expect that element "#block-par-theme-content" contains the text "See enforcement notifications"
#        When I click on the link "See enforcement notifications"
#        Then the element "h1.heading-xlarge" contains the text "Par User Enforcements"
#
#      # ENFORCEMENT APPROVAL
#
#        When I click on the button "Title of the enforcement notice"
#        Then the element "h1.heading-xlarge" contains the text "Approve Enforcement Notice"
#        When I click on the radio "#edit-actions-0-primary-authority-status-approved"
#        And I click on the button "#edit-actions-next"
#        Then the element "h1.heading-xlarge" contains the text "Confirm Enforcement Notice"
#        When I click on the link "Send notification of enforcement action"
#        Then the element "h1.heading-xlarge" contains the text "Awaiting approval"

#
#    When I add "Bob" to the inputfield "#first-name"
#    And I add "Builder" to the inputfield "#last-name"
#    And I add "bob.builder@example.com" to the inputfield "#email"
#    And I add "A summary" to the inputfield "#enforcement-summary"
#    And I add "Premises address" to the inputfield "#address-premises"
#    And I select the option with the text "Proposed" for element "#enforcement-type"
#    And I add "Title for the action" to the inputfield "#action-title"
#    And I select the option with the text "Fire safety" for element "#regulatory-function"
#    And I add "Details for the action" to the inputfield "#action-details"
#    And I press "Submit"
#    Then I expect that element "h1" contains the text "Your enforcement notification has been submitted to"
#    And I expect that element "#enforcement-ref" is not empty
#    When I click on the link "Log out"
#
#      # LOGIN AS PA USER TO CHECK MESSAGING
#
#    And I am logged in as "par_@example.com"
#    Then I expect that element "#secure-messages" contains the text "You have 1 new message"
#    When I click on the link "#secure-message"
#    Then I expect that element "h1" contains the text "Enforcement notification from"
#    And I press "Submit"
#    Then I expect that element "h1" contains the text "You have responded to the enforcement action by"
#
#      # LOGIN AS ENFORCEMENT OFFICER TO CHECK MESSAGING
#
#    When I am logged in as "par_enforcementofficer@example.com"
#    Then I expect that element "#secure-messages" contains the text "You have 1 new message"
#    When I click on the link "#secure-message"
#    Then I expect that element "h1" contains the text "Response to enforcement notification received by"
#    And I expect that element "#proposed-enforcement-ref" is not empty
#    And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
#    When I add "ABCD" to the inputfield "#edit-keywords"
#    And I click on the button "#edit-submit-rd-helpdesk-dashboard"
#    And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
#    When I click on the button "td.views-field.views-field-authority-name a"
#
#      # ENFORCEMENT ACTION FORM
#
#    When I click on the link "Enforcement action"
#    Then I expect that element "h1" contains the text "Send an enforcement notification to"
#    When I add "Bob" to the inputfield "#first-name"
#    And I add "Builder" to the inputfield "#last-name"
#    And I add "bob.builder@example.com" to the inputfield "#email"
#    And I add "A summary" to the inputfield "#enforcement-summary"
#    And I add "Premises address" to the inputfield "#address-premises"
#    And I select the option with the text "Proposed" for element "#enforcement-type"
#    And I add "Title for the action" to the inputfield "#action-title"
#    And I select the option with the text "Fire safety" for element "#regulatory-function"
#    And I add "Details for the action" to the inputfield "#action-details"
#    And I press "Submit"
#    Then I expect that element "h1" contains the text "Your enforcement notification has been submitted to"
#    And I expect that element "#enforcement-ref" is not empty
#    When I click on the link "Log out"
#
#       # LOGIN AS PA USER TO CHECK MESSAGING
#
#    And I am logged in as "par_authority@example.com"
#    Then I expect that element "#secure-messages" contains the text "You have 1 new message"
#    When I click on the link "#secure-message"
#    And I expect that element "h1" contains the text "Enforcement notification from"
#    And I click on the radio "#block-action"
#    And I press "Submit"
#    Then I expect that element "h1" contains the text "You have responded to the enforcement action by"
#    When I click on the link "Log out"
#
#       # LOGIN AS ENFORCEMENT OFFICER TO CHECK MESSAGING FOR BLOCKED ACTION
#
#    And I am logged in as "par_enforcementofficer@example.com"
#    Then I expect that element "#secure-messages" contains the text "You have 1 new message"
#    When I click on the link "#secure-message"
#    Then I expect that element "#action-message" contains the text "have BLOCKED this action"
#    And I expect that element "#proposed-enforcement-ref" is not empty
#
