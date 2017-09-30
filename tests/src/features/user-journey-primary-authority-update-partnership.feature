@ci @journey1 @deprecated
Feature: Primary Authority - Change Partnership Details

  Background:
      # TEST DATA RESET
    Given I reset the test data

  Scenario: Primary Authority - Change Partnership Details

      # SEARCH PARTNERSHIPS

    Given I am logged in as "par_helpdesk@example.com"
#    And I expect that element "#block-par-theme-content" contains the text "See your partnerships"
#    And I expect that element "#block-par-theme-content" contains the text "Search for a partnership"
#    And I expect that element "#block-par-theme-content" contains the text "Messages"
#    When I click on the link "See your partnerships"

      # PARTNERSHIPS DASHBOARD

    And I add "" to the inputfield "#edit-keywords"
    And I click on the button "#edit-submit-helpdesk-dashboard"
    And I click on the button "a*=Council"
    And I expect that element "h1" is not empty

      # REVIEW PARTNERSHIPS DETAILS

    And I click on the link "edit about the partnership"
    And I add "test partnership info change" to the inputfield "#edit-about-partnership"
    And I click on the button "#edit-save"
    Then I expect that element "#edit-about-partnership" contains the text "test partnership info change"
    When I click on the button "/html/body/main/div[2]/div[4]/form/div[12]/fieldset/div[2]/fieldset/a"
    And I clear the inputfield "#edit-salutation"
    And I clear the inputfield "#edit-work-phone"
    And I clear the inputfield "#edit-mobile-phone"
    And I clear the inputfield "#edit-email"
    And I click on the button "#edit-save"
    When I add "Mr" to the inputfield "#edit-salutation"
    And I click on the button "#edit-save"
    Then I expect that element ".error-message" does exist
    When I add "91723456789" to the inputfield "#edit-work-phone"
    And I click on the button "#edit-save"
    Then I expect that element ".error-message" does exist
    When I add "9777777777" to the inputfield "#edit-mobile-phone"
    And I click on the button "#edit-save"
    Then I expect that element ".error-message" does exist
    When I add "par_authority_animal@example.com" to the inputfield "#edit-email"
    When I click on the button "#edit-save"
    And I expect that element "#edit-authority-contacts" contains the text "par_authority_animal@example.com"
    And I expect that element "#edit-authority-contacts" contains the text "91723456789"
    And I expect that element "#edit-authority-contacts" contains the text "9777777777"
#    And I scroll to element "#edit-organisation-contacts"
#    When I click on the button "/html/body/main/div[2]/div[4]/form/div[12]/fieldset/div[4]/fieldset/a"
#    And I add "Miss" to the inputfield "#edit-salutation"
#    And I add "Miss" to the inputfield "#edit-first-name"
#    And I add "Piggy" to the inputfield "#edit-last-name"
#    And I add "par_authority_piggy@example.com" to the inputfield "#edit-email"
#    And I add "917234567899" to the inputfield "#edit-work-phone"
#    And I add "97777777779" to the inputfield "#edit-mobile-phone"
#    When I click on the button "#edit-save"
#    Then I expect that element "#edit-authority-contacts" contains the text "par_authority_piggy@example.com"
#    Then I expect that element "#edit-authority-contacts" contains the text "917234567899"
#    Then I expect that element "#edit-authority-contacts" contains the text "97777777779"

#         Then I expect that element "#block-par-theme-content" contains the text "Confirmed by the Authority"

     # DOCUMENTATION

      # When I select next partnership awaiting review
    And I click on the button "a*=See all Advice"
    When I click on the link "Upload advice"
    Then I expect that element "h3" contains the text "How to upload Primary Authority Advice to Local Authorities"
    And I click on the button "#edit-cancel"
      #  And I click on the link "Upload a document"
#        #  And I upload a valid file
#    And I click on the link "edit"
#    And I click on the radio "#edit-advice-type-authority-advice"
#    When I click on the button "#edit-save"
#    Then I expect that element ".error-message" does exist
#    And I click on the checkbox ".form-label*=Trading standards"
#    When I click on the button "#edit-save"
      # Then I expect that element ".table-scroll-wrapper" contains the text "✔"
      # And the element ".table-scroll-wrapper" contains the text "Cookie control"
    When I click on the link "Done"
#        Then I expect that element "#edit-regulatory-functions" contains the text "Cookie control"
    And I click on the checkbox "#edit-partnership-info-agreed-authority"
    And I click on the button "#edit-save"
    Then I expect that element "h1" contains the text "Primary Authority Register"

        # INSPECTION PLANS

        # When I select next partnership awaiting review
#        When I click on the link "See all Inspection Plans"
#        Then I expect that element "#edit-document-list" contains the text "Inspection Plan"
#        And I click on the checkbox ".form-checkbox"
#        And I click on the link "Save"

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
