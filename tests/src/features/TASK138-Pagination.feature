@ci @ie8bug
Feature: As a PAR user
    I need pagination system
    In order to break down information into easier views

    Background:
        Given I open url "/login"
        And I am logged in as PAR user "testuser" with password "testpwd"
        Given I open the url "/styleguide/pagination"
        And the title is "PAR Styleguide Pagination | Primary Authority Register"
        Then the element "li.pager__item.pagerer-prefix span" contains the text "Showing 1-10"

    Scenario Outline: pagination check
        Given I click on the link "<page link>"
        Then the element "li.pager__item.pagerer-prefix span" contains the text "<page set>"

        Examples:
            | page link | page set      |
            | 2         | Showing 11-20 |
            | 3         | Showing 21-30 |
            | 4         | Showing 31-40 |
            | 5         | Showing 41-50 |
