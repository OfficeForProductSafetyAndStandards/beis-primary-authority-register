@ci
Feature: As a PAR user
    I need pagination system
    In order to break down information into easier views

    Background:
        Given I open the url "/styleguide/pagination"
        And the title is "PAR Styleguide Pagination | Regulatory Authority"

    Scenario Outline: pagination check
        Given I click on the link "<page link>"
        Then I expect that element "li.pager__item.pagerer-prefix span" contains the text "<page set>"

        Examples:
            | page link | page set                     |
            | 2         | Showing 11-20 of 462 results |
            | 3         | Showing 21-30 of 462 results |
            | 4         | Showing 31-40 of 462 results |
            | 5         | Showing 41-50 of 462 results |
        
    Scenario Outline: pagination check (by url)
        Given I open the url "<page link>"
        Then I expect that element "li.pager__item.pagerer-prefix span" contains the text "<page set>"

        Examples:
            | page link                           | page set                     |
            | /styleguide/pagination?page=%2C%2C1 | Showing 11-20 of 462 results |
            | /styleguide/pagination?page=%2C%2C2 | Showing 21-30 of 462 results |
            | /styleguide/pagination?page=%2C%2C3 | Showing 31-40 of 462 results |
            | /styleguide/pagination?page=%2C%2C4 | Showing 41-50 of 462 results |
