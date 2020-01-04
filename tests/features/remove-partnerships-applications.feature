Feature: Remove partnership appliations

    Background:
        Given I am logged in as "par_helpdesk@example.com"
        Given I click the link text "Manage partnerships"


    @ci
    Scenario: Search and remove an incomplete partnership
        When I add "Demolition Experts" to the inputfield "#edit-keywords"
        And I select the option with the value "confirmed_business" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        And there is "1" occurences of element ".par-helpdesk-partnership-list tbody tr"
        Then I click the link text "Delete partnership"

        # DELETION REASON SCREEN

        And the element "h1.heading-xlarge" contains the text "Delete a partnership"
        When I click on the button "#edit-next"
        Then the element ".error-summary" contains the text "Please supply the reason for cancelling this partnership."
        When I enter the deletion reason "Testing the deletion of a partnership application."
        And I click on the button "#edit-next"

        # DELETION CONFIRMATION SCREEN

        Then the element "h1.heading-xlarge" contains the text "Partnership deleted"
        Then the element ".edit-partnership-info" contains the text "The partnership application has been deleted"
        And I click on the button "#edit-done"
