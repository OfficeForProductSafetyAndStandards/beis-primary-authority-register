@ci
Feature: Verify implmentation of GDS theme

    Background:
    Given I open url "/login"
    And I am logged in as PAR user "testuser" with password "testpwd"
    
    Scenario Outline: Check element placements
        Given I open the url "<url>"
        Then the element "#block-par-theme-page-title" contains the text "PAR Styleguide"
        Examples:
            | url         |
            | /styleguide |

