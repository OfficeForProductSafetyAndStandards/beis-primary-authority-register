@ci @deprecated
Feature: Helpdesk As Primary Authority - Manage name and summary detail

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Helpdesk As Primary Authority - Manage name and summary detail
        # LOGIN SCREEN

        Given I am logged in as "par_helpdesk@example.com"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        Then I expect that element "h1" contains the text "RD Helpdesk Dashboard"
        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
        When I add "ABCD" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-rd-helpdesk-dashboard"
        And I scroll to element "#views-exposed-form-rd-helpdesk-dashboard-par-rd-helpdesk-dashboard-page"
        When I click on the button "td.views-field.views-field-authority-name a"

        # REVIEW PARTNERSHIPS DETAILS
        When I click on the link "Review and confirm your partnership details"
        And I click on the link "edit"
        And I add "test partnership info change" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"
        Then I expect that element "#edit-first-section" contains the text "test partnership info change"
        When I click on the button "form#par-flow-transition-partnership-details-overview .authority-alternative-contact a.flow-link"
        And I clear the inputfield "#edit-salutation"
        And I clear the inputfield "#edit-first-name"
        And I clear the inputfield "#edit-last-name"
        And I clear the inputfield "#edit-work-phone"
        And I clear the inputfield "#edit-mobile-phone"
        And I clear the inputfield "#edit-email"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "Mr" to the inputfield "#edit-salutation"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "Animal" to the inputfield "#edit-first-name"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "the Muppet" to the inputfield "#edit-last-name"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "91723456789" to the inputfield "#edit-work-phone"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "9777777777" to the inputfield "#edit-mobile-phone"
        And I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        When I add "par_authority_animal@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-next"
        Then I expect that element "#edit-authority-contacts" contains the text "Animal"
        And I expect that element "#edit-authority-contacts" contains the text "the Muppet"
        And I expect that element "#edit-authority-contacts" contains the text "par_authority_animal@example.com"
        And I expect that element "#edit-authority-contacts" contains the text "91723456789"
        And I expect that element "#edit-authority-contacts" contains the text "9777777777"
        When I click on the button "form#par-flow-transition-partnership-details-overview .authority-alternative-contact-0 a.flow-link"
        And I add "Miss" to the inputfield "#edit-first-name"
        And I add "Piggy" to the inputfield "#edit-last-name"
        And I add "par_authority_piggy@example.com" to the inputfield "#edit-email"
        And I add "917234567899" to the inputfield "#edit-work-phone"
        And I add "97777777779" to the inputfield "#edit-mobile-phone"
        When I click on the button "#edit-next"
        Then I expect that element ".authority-alternative-contact-0" contains the text "Miss"
        Then I expect that element ".authority-alternative-contact-0" contains the text "Piggy"
        Then I expect that element ".authority-alternative-contact-0" contains the text "par_authority_piggy@example.com"
        Then I expect that element ".authority-alternative-contact-0" contains the text "917234567899"
        Then I expect that element ".authority-alternative-contact-0" contains the text "97777777779"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"

        # PARTNERSHIP DASHBOARD

        Then I expect that element "#block-par-theme-content" contains the text "Confirmed by the Authority"
        And I expect that element ".table-scroll-wrapper" contains the text "Invite the business to confirm their details"
        And I expect that element ".table-scroll-wrapper" contains the text "Review and confirm your inspection plan"
        And I expect that element ".table-scroll-wrapper" contains the text "Review and confirm your documentation"

        # DOCUMENTATION

        And I scroll to element ".table-scroll-wrapper"
        When I click on the button "a*=Review and confirm your documentation"
        And I scroll to element ".table-scroll-wrapper"
#        When I click on the link "Upload a document"
#        And I add "test.png" to the inputfield "#edit-files-upload"
#        And I click on the link "classify"
#        And I click on the radio "#edit-advice-type-authority-advice"
#        When I click on the button "#edit-next"
#        Then I expect that element ".error-message" does exist
#        And I click on the checkbox ".form-label*=Cookie control"
#        When I click on the button "#edit-next"
#        Then I expect that element ".table-scroll-wrapper" contains the text "✔"
#        And the element ".table-scroll-wrapper" contains the text "Cookie control"
        When I click on the link "Save"
        Then I expect that element ".table-scroll-wrapper" contains the text "50%"

        # INSPECTION PLANS

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Review and confirm your inspection plan"
#        Then I expect that element "#edit-document-list" contains the text "Inspection Plan"
#        And I click on the checkbox ".form-checkbox"
        And I click on the button "#edit-next"

        # CHECK INSPECTION PLAN CONFIRMED

#        And I scroll to element ".table-scroll-wrapper"
#        When I click on the link "Review and confirm your inspection plan"
#        Then I expect that element ".form-checkbox" is not enabled
#        When I click on the button "#edit-next"

        # PARTNERSHIP TASKS SCREEN

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Invite the business to confirm their details"

        # BUSINESS EMAIL INVITATION

        And I add "Test change meassage body [invite:invite-accept-link]" to the inputfield "#edit-email-body"
        And I add "Test change meassage subject" to the inputfield "#edit-email-body"
        When I press "Send Invitation"
