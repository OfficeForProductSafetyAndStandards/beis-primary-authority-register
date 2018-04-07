@ci @PAR859 @PAR962 @PAR1054 @tota11y
Feature: Enforcement Officer/PA - Enforcement Notice Process

    Scenario: Enforcement Officer/PA - Issue enforcement notice

        # LOGIN SCREEN
       Given I am logged in as "par_enforcement_officer@example.com"
       When I click the link text "Search for a partnership"
       # And I run tota11y against the current page
       And I add "Charlie's Cafe" to the inputfield "#edit-keywords"
       And I click on the button "#edit-submit-partnership-search"
       # And I run tota11y against the current page
       When I click on the button "td.views-field.views-field-par-flow-link a"

       # ENFORCEMENT ACTION FORM

       When I click the link text "Send a notification of a proposed enforcement action"
       # And I run tota11y against the current page
       And I click on the button "#edit-cancel"
       And I click the link text "Send a notification of a proposed enforcement action"
       # And I run tota11y against the current page

       # CHOOSE MEMBER

       Then the element "#par-enforce-organisation" contains the text "Choose the member to enforce"
       And I click on the radio ".form-radio"
       And I click on the button "#edit-next"
       # And I run tota11y against the current page

       # ENTER EO DETAILS

       And I add "Colin" to the inputfield "#edit-first-name"
       And I add "Weatherby" to the inputfield "#edit-last-name"
       And I add "01234780898" to the inputfield "#edit-work-phone"
       And I click on the button "#edit-next"
       # And I run tota11y against the current page

       # CHOOSE LEGAL ENTITY

       Then the element "h1.heading-xlarge" contains the text "Notification of Enforcement action"
       And I click on the radio "#edit-legal-entities-select-add-new"
       And I add "Legal Entity 1" to the inputfield "#edit-alternative-legal-entity"
       And I click on the button "#edit-next"
       # And I run tota11y against the current page

     # ENFORCEMENT SUMMARY

       Then the element "h1.heading-xlarge .heading-secondary" contains the text "Proposed enforcement notification regarding"
       Then the element "h1.heading-xlarge" contains the text "Legal Entity 1"
#        And I add "action summary enforcement notice" to the inputfield "#edit-summary"
       And I add "600" random chars of text to field "#edit-summary"
       And I click on the radio "#edit-type-proposed"
       And I click on the button "#edit-next"
       # And I run tota11y against the current page
       Then the element "h1.heading-xlarge .heading-secondary" contains the text "Provide details of the proposed enforcement action"
       Then the element "h1.heading-xlarge" contains the text "Add an action to the enforcement notice"
       And I add "Enforcement notice 4" to the inputfield "#edit-title"
       And I click on the radio ".form-radio"
       And I add "600" random chars of text to field "#edit-details"
#        And I add "Some details about the enforcement notice" to the inputfield "#edit-details"
      #  And I upload the file "./files/test.png" to field "#edit-files-upload"
       And I click on the button "#edit-next"
       # And I run tota11y against the current page
      #  Then the element ".filename" contains the text "test.png"

       # ADD ENFORCEMENT ACTION OMITTED DUE TO BUG PAR-1054
#
#        When I click the link text "Add another enforcement action"
#        And I add "Added Enforcement Action" to the inputfield "#edit-title-of-action"
#        And I click on the radio ".option*=Alphabet learning"
#        And I add "Added Enforcement Action details" to the inputfield "#edit-details"
#        And I click on the button "#edit-next"

       # CHECK DETAILS

       Then the element "h1.heading-xlarge .heading-secondary" contains the text "Summary of the proposed enforcement action(s) regarding"
       Then the element "h1.heading-xlarge" contains the text "Legal Entity 1"
       Then the element "#par-enforcement-notice-raise-confirm" contains the text "Enforcement notice 4"
       And the element "#par-enforcement-notice-raise-confirm" contains the text "last text in a long string"
       And the element "#par-enforcement-notice-raise-confirm" contains the text "Once the primary authority receives this notification, they have 5 working days to respond to you if they intend to block the action"
       And the element "#par-enforcement-notice-raise-confirm" contains the text "You will be notified by email of the outcome of this notification"

       # CHECK EO DETAILS

       And the element "#edit-enforcement-officer-name" contains the text "Colin"
       And the element "#edit-enforcement-officer-name" contains the text "Weatherby"
       And the element "#edit-enforcement-officer-telephone" contains the text "01234780898"
       And the element "#edit-enforcement-officer-email" contains the text "par_enforcement_officer@example.com"

#        When I store all EO data to use in later step

       When I click on the button "#edit-save"
       # And I run tota11y against the current page
       Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"

#       CHECK EMAIL LOG
#
      #  And I open the path "/user/logout"
      #   And I open the path "/user/login"
      #   And I add "dadmin" to the inputfield "#edit-name"
      #   And I add "TestPassword" to the inputfield "#edit-pass"
      #   When I click on the button "#edit-submit"
      #   And I open the path "/admin/reports/maillog"
      #   And I add "Primary Authority - Notification of Proposed Enforcement" to the inputfield "#edit-su
      #   When I click the link text "Primary Authority - Notification of Proposed Enforcement"bject"
      #   And I add "par_authority@example.com" to the inputfield "#edit-header-to"
      #   And I click on the button "#edit-submit-maillog-overview"
      #   Then the element "h1.heading-xlarge" contains the text "Primary Authority - Notification of Proposed Enforcement"

        #       CHECK RECEIVED ENFORCEMENT NOTIFICATIONS

        When I open the path "/user/logout"
        And I open the path "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        When I click the link text "See enforcement notifications received"
        Then the element ".table-scroll-wrapper" contains the text "Enforcement notice 4"

#       # CHECK ALL AUTHORITY MEMBERS NOTIFIED #PAR-859 #PAR-962
#
#        And email body should contain all relevant EO data
#
#        # ALL AUTHORITY MEMBERS NOTIFIED #PAR-962
#
#        And I expect that notification email has been sent to all authority members
