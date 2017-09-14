@Pending @journey1 @deprecated
Feature: Primary Authority - Change Partnership Details

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Primary Authority - Change Partnership Details

        # SEARCH PARTNERSHIPS

        Given I am logged in as "par_authority@example.com"
        And I expect that element "#block-par-theme-content" contains the text "Your partnerships"
        And I expect that element "#edit-authority-contacts" contains the text "Find a partnership"
        And I expect that element "#edit-authority-contacts" contains the text "Messages"
        When I click on the link "Create a new partnership"
        # When I add "the Muppet" to the inputfield "#edit-last-name"
        # And I click on the button "#edit-submit-partnership-search"

        # CREATE NEW PARTNERSHIP FORM
