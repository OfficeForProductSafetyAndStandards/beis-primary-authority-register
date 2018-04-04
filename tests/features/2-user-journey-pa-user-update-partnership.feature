@ci
Feature: PA User - Manage Addresses

    Scenario: PA User - Manage Addresses

        #LOGIN
        
        Given I am logged in as "par_authority@example.com"
        When I click the link text "See your partnerships"
        And I add "Organisation for Direct Partnership" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"

        # EDIT ABOUT THE ORGANISATION

        When I click the link text "City Enforcement Squad"
        When I click the link text "edit about the partnership"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Primary Authority partnership information"
        And I add "Change to the about organisation details section" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-save"
        Then the element "#edit-about-partnership" contains the text "Change to the about organisation details section"

        # EDIT MAIN AUTHORITY CONTACT

        When I click the link text "edit authority contact"
        And I add "Mrs" to the inputfield "#edit-salutation"
        And I add "Helen" to the inputfield "#edit-first-name"
        And I add "Brittas" to the inputfield "#edit-last-name"
        And I add "02078886663" to the inputfield "#edit-work-phone"
        And I add "07965465726" to the inputfield "#edit-mobile-phone"
        And I click on the checkbox "#edit-preferred-contact-communication-mobile"
        And I add "Some additional notes" to the inputfield "#edit-notes"
        And I click on the button "#edit-save"
        Then the element "#edit-authority-contacts" contains the text "Mrs Helen Brittas"
        And the element "#edit-authority-contacts" contains the text "02078886663"
        And the element "#edit-authority-contacts" contains the text "07965465726"

        # COMPLETE CHANGES

        When I click on the button "#edit-save"
        And I add "Organisation for Direct Partnership" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-par-user-partnerships"

        And the element "#block-par-theme-content" contains the text "Organisation for Direct Partnership"
