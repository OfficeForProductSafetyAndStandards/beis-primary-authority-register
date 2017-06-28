@ci
Feature: Verify implmentation of GDS theme

    Scenario: Check styles

    Scenario Outline: Check element placements
        Given I open the url "<url>"
        Then the element "#block-par-theme-page-title" contains the text "PAR Styleguide"
        Examples:
            | url         |
            | /styleguide |

