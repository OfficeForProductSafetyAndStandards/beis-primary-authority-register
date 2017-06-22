@Pending
Feature: Verify implmentation of GDS theme

    Scenario: Check styles

    Scenario Outline: Check element placements
        Given I open the url "<url>"
        Then  I expect that the css attribute "color" from element "#cssAttributeComparison" is "rgba(255,0,0,1)"

        Examples:
            | url         |
            | /tasks-list |
