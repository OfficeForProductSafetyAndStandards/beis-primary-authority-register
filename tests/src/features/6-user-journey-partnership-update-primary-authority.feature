@ci
Feature: Primary Authority - Change Partnership Details

    Scenario: Primary Authority - Change Partnership Details

#        Given I reset the test data
        Given I am logged in as "par_authority@example.com"
        And I click on the link "See your partnerships"
        When I add "Business For" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click on the button "td.views-field.views-field-par-flow-link a"
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
        And I click on the button "#edit-save"
        When I add "Mr" to the inputfield "#edit-salutation"
        And I click on the button "#edit-save"
        Then I expect that element ".error-summary" does exist
        When I add "91723456789" to the inputfield "#edit-work-phone"
        When I add "9777777777" to the inputfield "#edit-mobile-phone"
        When I click on the button "#edit-save"
        And I expect that element "#edit-authority-contacts" contains the text "91723456789"
        And I expect that element "#edit-authority-contacts" contains the text "9777777777"

        # DOCUMENTATION

        # When I select next partnership awaiting review
        And I click on the button "a*=See all Advice"
        When I click on the link "Upload advice"
        Then I expect that element "h3" contains the text "How to upload Primary Authority Advice to Local Authorities"
        When I click on the button "#edit-cancel"
