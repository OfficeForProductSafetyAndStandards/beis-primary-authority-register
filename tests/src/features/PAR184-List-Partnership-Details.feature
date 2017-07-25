@ci
Feature: List Partnership Details: Load summary elements Load data into the form

    # Background:
    #     Given I open the url "/user/login"
    #     And I add "testuser" to the inputfield "#edit-name"
    #     And I add "testpwd" to the inputfield "#edit-pass"
    #     When I press "#edit-submit"
    #     Then I expect that element ".error-message" is not visible

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1/details"
        Then the element "h1" contains the text "Viewing/confirming partnership details"
        And the element "#par-flow-transition-partnership-details-overview" contains the text "About the Partnership"
        And the element "#par-flow-transition-partnership-details-overview" contains the text "Main Primary Authority contact"
        And the element "#par-flow-transition-partnership-details-overview" contains the text "Secondary Primary Authority contacts"
        And the element "#par-flow-transition-partnership-details-overview" contains the text "Areas of Regulatory Advice"
        And I click on the checkbox "#edit-partnership-agreement"
        And I click on the checkbox "#edit-confirmation"
