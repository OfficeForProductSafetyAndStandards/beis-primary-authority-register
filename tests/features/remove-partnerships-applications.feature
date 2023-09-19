Feature: Remove partnership appliations

    Background:
        Given I am logged in as "par_helpdesk@example.com"
        Given I click the link text "Search partnerships"


    @ci @nonsmoke
    Scenario: Search and remove an incomplete partnership
        When I add '"Demolition Experts"' to the inputfield "#edit-keywords"
        And I select the option with the value "confirmed_authority" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-advanced-partnership-search"
        Then the element ".par-advanced-partnership-search-list .views-row-1 .partnership-name" contains the text "Demolition Experts"
        And I click the link "Delete partnership" in row "1"

        # DELETION REASON SCREEN

        And the element "h1" contains the text "Delete a partnership"
        When I click on the button "#edit-next"
        Then the element ".error-summary" contains the text "Please supply the reason for cancelling this partnership."
        When I enter the deletion reason "Testing the deletion of a partnership application."
        And I click on the button "#edit-next"

        # DELETION CONFIRMATION SCREEN

        Then the element "h1" contains the text "Partnership deleted"
        Then the element "#edit-partnership-info" contains the text "The partnership application has been deleted"
        And I click on the button "#edit-done"
