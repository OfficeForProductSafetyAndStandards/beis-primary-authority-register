@ci @journey1
Feature: Primary Authority - Change Partnership Details

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Primary Authority - Change Partnership Details
        # LOGIN

        Given I am logged in as "par_authority@example.com"
        When I click on the link "Continue to your Partnerships"

        # PARTNERSHIPS DASHBOARD

        And I scroll to element "#views-exposed-form-par-data-transition-journey-1-step-1-dv-journey-1-step-1"
        And relevant partnerships search results returned
        When I select next partnership awaiting review

        # TERMS AND CONDITIONS SCREEN

        Then I expect that element ".par-flow-transition-partnership-details-terms" contains the text "Please review the new Primary Authority terms and conditions and confirm that you agree with them"
        When I click on the checkbox "#edit-terms-conditions"
        And I click on the button "#edit-next"
        And I scroll to element ".table-scroll-wrapper"
        And I click on the link "Review and confirm your partnership details"

        # REVIEW PARTNERSHIPS DETAILS

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
        Then I expect that element "#block-par-theme-content" contains the text "Confirmed by the Authority"

        # DOCUMENTATION

        And I scroll to element ".table-scroll-wrapper"
        When I click on the button "a*=Review and confirm your documentation"
        And I scroll to element ".table-scroll-wrapper"
        And I click on the link "edit"
        And I click on the radio "#edit-document-type-authority-advice"
        When I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        And I click on the checkbox ".form-label*=Cookie control"
        When I click on the button "#edit-next"
        Then I expect that element ".table-scroll-wrapper" contains the text "✔"
        And the element ".table-scroll-wrapper" contains the text "Cookie control"
        When I click on the link "Save"
        Then I expect that element ".table-scroll-wrapper" contains the text "100%"
        When I click on the button "a*=Review and confirm your documentation"
        And I click on the link "edit"
        And I click on the radio "#edit-document-type-authority-advice"
        And I click on the checkbox ".form-label*=Cookie control"
        And I click on the checkbox ".form-label*=Alphabet learning"
        When I click on the button "#edit-next"
        Then I expect that element ".table-scroll-wrapper" contains the text "✔"
        And I expect that element ".table-scroll-wrapper" contains the text "Cookie control, Alphabet learning"
        When I click on the link "Save"
        Then I expect that element ".table-scroll-wrapper" contains the text "100%"

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

                # PARTERSHIP TASKS SCREEN

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Invite the business to confirm their details"

        # BUSINESS EMAIL INVITATION

        And I add "Test change meassage body [invite:invite-accept-link]" to the inputfield "#edit-email-body"
        And I add "Test change meassage subject" to the inputfield "#edit-email-body"
        And I press "Send Invitation"
