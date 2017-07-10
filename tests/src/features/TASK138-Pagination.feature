@ci
Feature: As a PAR user
    I need pagination system
    In order to break down information into easier views

    Background:
#        Given I open the url "/login"
#        And I add "ValidPrimaryAuthorityId" to the inputfield "#username"
#        And I add "Password1" to the inputfield "#password"
#        And I press "Login"
#        Then I expect that element "#logged-in-header" contains the text "Logged in"
        Given I open the url "/styleguide/pagination"
        And the title is "Welcome to Regulatory Authority | Regulatory Authority"
#        And I expect that element "#pagination" contains the text "Pages:"
#        And I expect that element "pager__item--previous" is visible
#        And I expect that element "pager__item--next" is visible

    Scenario Outline: Valid Login  Id
        Given I click on the link "<page link>"
        Then I expect that element "li.pager__item.pagerer-prefix span" contains the text "<page set>"

        Examples:
            | page link | page set                     |
            | 2         | Showing 11-20 of 462 results |
            | 3         | Showing 21-30 of 462 results |
            | 4         | Showing 31-40 of 462 results |
            | 5         | Showing 41-50 of 462 results |
#            | Previous  | Showing 31-40 of 462 results |
#            | Next      | Showing 41-50 of 462 results |
