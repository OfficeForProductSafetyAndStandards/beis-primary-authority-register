@Pending
Feature: Primary Authority - Change Partnership Details

    Scenario: Primary Authority - Change Partnership Details

        #LOGIN
        
        Given I am logged in as "par_authority@example.com"
        When I click the link text "See your partnerships"
        And I add "Organisation For Direct Partnership 8" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click the link text "City Enforcement Squad"

        # REVIEW PARTNERSHIPS DETAILS

        And I click the link text "edit about the partnership"
        And I add "test partnership info change" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-save"
        Then the element "#edit-about-partnership" contains the text "test partnership info change"
        And I click the link text "edit authority contact"
        When I add "Mr" to the inputfield "#edit-salutation"
        When I add "91723456789" to the inputfield "#edit-work-phone"
        When I add "9777777778" to the inputfield "#edit-mobile-phone"
        When I click on the button "#edit-save"
        And the element "#edit-authority-contacts" contains the text "91723456789"
        And the element "#edit-authority-contacts" contains the text "9777777778"

        # DOCUMENTATION

        # When I select next partnership awaiting review
        And I click the link text "See all Advice"
        When I click the link text "Upload advice"
        # Then the element "h1" contains the text "How to upload Primary Authority Advice to Local Authorities"
        When I click on the button "#edit-cancel"
