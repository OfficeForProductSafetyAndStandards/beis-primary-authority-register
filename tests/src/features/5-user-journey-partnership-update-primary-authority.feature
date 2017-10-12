@ci @journey1 @deprecated
Feature: Primary Authority - Change Partnership Details

    Scenario: Primary Authority - Change Partnership Details

        Given I reset the test data
        And I am logged in as "par_authority@example.com"
        And I click on the link "See your partnerships"
        When I click on the button "td.views-field.views-field-par-flow-link a"
        And I expect that element "h1" is not empty

        # REVIEW PARTNERSHIPS DETAILS

        And I click on the link "edit about the partnership"
        And I add "test partnership info change" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-about-partnership" contains the text "test partnership info change"
        And I click on the button "a*=edit authority contact"
        And I clear the inputfield "#edit-salutation"
        And I clear the inputfield "#edit-work-phone"
        And I clear the inputfield "#edit-mobile-phone"
        And I clear the inputfield "#edit-email"
        And I click on the button "#edit-save"
        When I add "Mr" to the inputfield "#edit-salutation"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        When I add "91723456789" to the inputfield "#edit-work-phone"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        When I add "9777777777" to the inputfield "#edit-mobile-phone"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        When I add "par_authority@example.com" to the inputfield "#edit-email"
        When I click on the button "#edit-save"
        And I expect that element "#edit-authority-contacts" contains the text "par_authority@example.com"
        And I expect that element "#edit-authority-contacts" contains the text "91723456789"
        And I expect that element "#edit-authority-contacts" contains the text "9777777777"

       # DOCUMENTATION

        # When I select next partnership awaiting review
        And I click on the button "a*=See all Advice"
        When I click on the link "Upload advice"
        Then I expect that element "h3" contains the text "How to upload Primary Authority Advice to Local Authorities"
        And I click on the button "#edit-cancel"
#        And I click on the link "Done"
#        And I click on the link "edit"
#        Then I expect that element "#block-par-theme-content" contains the text "Upload advice"
#        When I click on the button ".button"
##    And I click on the checkbox "#edit-partnership-info-agreed-authority"
#        And I click on the button "#edit-save"
#        Then I expect that element "h1" contains the text "Primary Authority Register"

        # INSPECTION PLANS

        # When I select next partnership awaiting review
#        When I click on the link "See all Inspection Plans"
#        Then I expect that element "#block-par-theme-content" contains the text "Inspection Plans"
#        And I click on the link "Continue"

        # CHECK INSPECTION PLAN CONFIRMED

#         When I click on the link "See all Inspection Plans"
# #        Then I expect that element ".form-checkbox" is not enabled
#         And I click on the link "Save"

        # # PARTERSHIP TASKS SCREEN

        # When I click on the link "Invite the business to confirm their details"

        # # BUSINESS EMAIL INVITATION

        # And I add "Test change meassage body [invite:invite-accept-link]" to the inputfield "#edit-email-body"
        # And I add "Test change meassage subject" to the inputfield "#edit-email-body"
        # And I press "Send Invitation"
