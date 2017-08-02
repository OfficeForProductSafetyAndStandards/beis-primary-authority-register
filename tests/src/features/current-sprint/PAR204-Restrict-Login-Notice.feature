@ci
Feature: As RD
I need to display a login notice that is specific to PA offers
So that I can remind PA officers of Data Validation deadlines

Scenario Outline: Login notices
        Given I open the url "/user/login"
        And I add "<userid>" to the inputfield "#edit-name"
        And I add "<password>" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element ".error-message" is not visible
        And the element "<area>" contains the text "<text>"
        And I click on the link "Log out"

Examples:
        | userid                    | password     | area                     |text                             |
        | par_authority@example.com | TestPassword | #block-par-theme-content | Review and confirm your data by |
        | par_authority@example.com | TestPassword | #block-par-theme-content | Review and confirm your data by |
