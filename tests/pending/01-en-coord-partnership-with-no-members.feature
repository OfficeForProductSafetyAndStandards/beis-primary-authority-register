Feature: Enforcement Officer - Issue enforcement notice
    
    @pending
    Scenario Outline: Enforcement Officer - Issue enforcement notice on coordinated partnership with no members

        #LOGIN
        
        Given I am logged in as "par_enforcement_officer@example.com"
        And I click on the link text "Search for a partnership"
        When I add "Univilla Limited" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        And I click on the link text "Univilla Limited"

       # ENFORCEMENT ACTION FORM

       When I click the link text "Send a notification of a proposed enforcement action"
        And I click on authority selection if available
       When I click on the button "#edit-next"
       Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"

        # ENTER EO DETAILS

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
        Then the element "#par-enforcement-notice-raise-review" contains the text "action summary enforcement notice"
        And the element "#par-enforcement-notice-raise-review" contains the text "Title of enforcement notice 1"
        And the element "#par-enforcement-notice-raise-review" contains the text "Some details about the enforcement notice"
        And the element "#par-enforcement-notice-raise-review" contains the text "Once the primary authority receives this notification, they have 5 working days to respond to you if they intend to block the action"
        And the element "#par-enforcement-notice-raise-review" contains the text "You will be notified by email of the outcome of this notification"
        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"
#       When I click the link text "See enforcement notices"