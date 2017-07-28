@ci
Feature:Create a view structured as illustrated in the attached image.

    Background:
        Given I open the url "/user/login"
        And I add "par_authority@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible
        And the element"#block-sitewidehelpdeskmessage" contains the text "0121 345 1201"

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I open the url "/dv/primary-authority-partnerships"
        Then the element "h1" contains the text "List of Partnerships for a Primary Authority"
        Then I expect that element "#view-organisation-table-column" is visible
