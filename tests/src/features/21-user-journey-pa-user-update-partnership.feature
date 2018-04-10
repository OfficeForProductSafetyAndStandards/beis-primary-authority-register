@ci
Feature: PA User - Manage Addresses

    Scenario: PA User - Manage Addresses

        # PARTNERSHIPS DASHBOARD

        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        And I open the url "/dashboard"
        Then I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        When I click on the link "See your partnerships"
        And I add "Direct Partnership For Revoking" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I click on the link "City Enforcement Squad"
        Then I expect that element "h1" is not empty

        # EDIT ABOUT THE ORGANISATION

        When I click on the link "edit about the partnership"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Primary Authority partnership information"
        And I add "Change to the about organisation details section" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-about-partnership" contains the text "Change to the about organisation details section"

        # EDIT MAIN AUTHORITY CONTACT

        When I click on the link "edit authority contact"
        And I clear the inputfield "#edit-salutation"
        And I clear the inputfield "#edit-first-name"
        And I clear the inputfield "#edit-last-name"
        And I clear the inputfield "#edit-work-phone"
        And I clear the inputfield "#edit-mobile-phone"
        And I clear the inputfield "#edit-notes"
        And I add "Dr" to the inputfield "#edit-salutation"
        And I add "Harvey" to the inputfield "#edit-first-name"
        And I add "Kneeslapper" to the inputfield "#edit-last-name"
        And I add "02078886666" to the inputfield "#edit-work-phone"
        And I add "07965465723" to the inputfield "#edit-mobile-phone"
        And I check the checkbox "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        Then I expect that element "#edit-authority-contacts" contains the text "Dr Harvey Kneeslapper"
        And I expect that element "#edit-authority-contacts" contains the text "02078886666"
        And I expect that element "#edit-authority-contacts" contains the text "07965465723 (preferred)"

        # COMPLETE CHANGES

        When I click on the button "#edit-save"
        And I select the option with the text "Confirmed by the Authority" for element "#edit-partnership-status-1"
        And I click on the button "#edit-submit-par-user-partnerships"
        And I expect that element "#block-par-theme-content" contains the text "Direct Partnership For Revoking"
