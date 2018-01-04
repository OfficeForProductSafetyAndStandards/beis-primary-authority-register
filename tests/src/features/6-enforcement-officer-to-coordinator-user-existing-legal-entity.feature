@ci
Feature: Enforcement Officer/Coordinator - Enforcement Notice Process

    Scenario: Enforcement Officer/Coordinator - Issue enforcement notice

        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I add "par_enforcement_officer@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element "#block-par-theme-content" contains the text "Search for a partnership"
        When I click on the link "Search for a partnership"
        And I add "Charlie" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        When I click on the button "td.views-field.views-field-authority-name a"

        # ENFORCEMENT ACTION FORM

        When I click on the link "Send a notification of a proposed enforcement action"
        And I click on the button "#edit-cancel"
        And I click on the link "Send a notification of a proposed enforcement action"

        # CHOOSE MEMBER

        Then I expect that element "#par-enforce-organisation" contains the text "Choose the member to enforce"
        And I click on the radio "label*=Laundromat"
        And I click on the button "#edit-next"

        # ENTER EO DETAILS

        And I clear the inputfield "#edit-first-name"
        And I clear the inputfield "#edit-last-name"
        And I clear the inputfield "#edit-work-phone"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" does exist
        When I add "Fozzie" to the inputfield "#edit-first-name"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" does exist
        When I add "Bear" to the inputfield "#edit-last-name"
        And I click on the button "#edit-next"
        Then I expect that element ".error-summary" does exist
        When I add "fozzie.bear@gmail.com" to the inputfield "#edit-work-phone"
        And I click on the button "#edit-next"

        # CHOOSE LEGAL ENTITY
        And I click on the button "#edit-next"

      # ENFORCEMENT SUMMARY

        And I add "action summary enforcement notice" to the inputfield "#edit-action-summary"
        And I click on the radio "#edit-type-proposed"
        And I click on the button "#edit-next"

        And I add "Title of enforcement notice Three" to the inputfield "#edit-title-of-action"
        And I click on the radio ".option*=Cookie control"
        And I add "Some details about the enforcement notice" to the inputfield "#edit-details"
        And I click on the button "#edit-next"
        And I scroll to element "#footer"
        Then I expect that element "#par-enforcement-notice-raise-confirm" contains the text "action summary enforcement notice"
        And I expect that element "#par-enforcement-notice-raise-confirm" contains the text "Title of enforcement notice Three"
        And I expect that element "#par-enforcement-notice-raise-confirm" contains the text "Some details about the enforcement notice"
        And I expect that element "#par-enforcement-notice-raise-confirm" contains the text "Once the primary authority receives this notification, they have 5 working days to respond to you if they intend to block the action"
        And I expect that element "#par-enforcement-notice-raise-confirm" contains the text "You will be notified by email of the outcome of this notification"
        When I click on the button "#edit-save"
        Then I expect that element "h1.heading-xlarge" contains the text "Primary Authority Register"
