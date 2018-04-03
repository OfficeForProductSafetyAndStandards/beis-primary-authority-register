@ci
Feature: Enforcement Officer/PA - Enforcement Notice Process

    Scenario: Enforcement Officer/PA - Issue enforcement notice

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"
        When I click the link text "Search for a partnership"
        And I add "Charlie" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        When I click on the button "td.views-field.views-field-authority-name a"

        # ENFORCEMENT ACTION FORM

        When I click the link text "Send a notification of a proposed enforcement action"
        And I click on the button "#edit-cancel"
        And I click the link text "Send a notification of a proposed enforcement action"

#        # CHOOSE MEMBER

        Then the element "#par-enforce-organisation" contains the text "Choose the member to enforce"
        And I click on the radio "#edit-par-data-organisation-id-101681"
        And I click on the button "#edit-next"

        # ENTER EO DETAILS

        When I add "Fozzie" to the inputfield "#edit-first-name"
        When I add "Bear" to the inputfield "#edit-last-name"
        When I add "fozzie.bear@gmail.com" to the inputfield "#edit-work-phone"
        And I click on the button "#edit-next"

        # CHOOSE LEGAL ENTITY
        And I click on the button "#edit-next"

      # ENFORCEMENT SUMMARY

        And I add "action summary enforcement notice" to the inputfield "#edit-summary"
        And I click on the radio "#edit-type-proposed"
        And I click on the button "#edit-next"
        And I add "Title of enforcement notice 1" to the inputfield "#edit-title"
        And I click on the radio ".form-radio"
        And I add "Some details about the enforcement notice" to the inputfield "#edit-details"
        And I click on the button "#edit-next"
        Then the element "#par-enforcement-notice-raise-confirm" contains the text "action summary enforcement notice"
        And the element "#par-enforcement-notice-raise-confirm" contains the text "Title of enforcement notice 1"
        And the element "#par-enforcement-notice-raise-confirm" contains the text "Some details about the enforcement notice"
        And the element "#par-enforcement-notice-raise-confirm" contains the text "Once the primary authority receives this notification, they have 5 working days to respond to you if they intend to block the action"
        And the element "#par-enforcement-notice-raise-confirm" contains the text "You will be notified by email of the outcome of this notification"
        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"
#       When I click the link text "See enforcement notifications sent"
#       Then the element ".table-scroll-wrapper" contains the text "Title of enforcement notice 1"
