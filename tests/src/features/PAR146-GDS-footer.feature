@ci @PAR-234
Feature: As a PAR user,
    I need to see the standard GDS Footer across the site
    so that I know I'm on the Primary Authority Register

    Scenario Outline: Valid Login  Id
        Given I open the url "<url>"
        Then the element "#block-footer" is visible
        When I click on the link "Contact"
        Then the element "h1" contains the text "Website feedback"
        When I open the url "<url>"
        And I click on the link "Open Government Licence v3.0"
        When I open the url "<url>"
        And I click on the link "© Crown copyright"

        Examples:
            | url |
            | /   |
