@Pending
Feature: Searching partnerships

    Background:
        Given I open the url "/login"
        And I add "EnforcementOfficer" to the inputfield "#username"
        And I add "password" to the inputfield "#password"
        And I press "Login"
        Then I expect that element "#logged-in-header" contains the text "Logged in"

    Scenario: Search By Business
        Given I open the url "/search-partnerships"
        And I click on the radio "#by-business"
        And I press "Continue"
        Then I expect that element "#search-businesses" contains the text "Search for a Partnership by Business"
        When I click on the radio "#by-name"
        Then I expect that element "#business_name" does exist
        And I add "Joe" to the inputfield "#business_name"
        And I press "Continue"
        Then I expect that element "#search-results" does exist
        When I click on the radio "#first-search-result-radio"
        And I press "Continue"
        Then I expect that element "#logged-in-header" contains the text "Primary Authority Partnerships"
        When I click on the link "#show-details"
        Then I expect that element "#about-the-business" does exist

    Scenario: Search By Coordinator
        Given I open the url "/search-partnerships"
        And I click on the radio "#by-coordinator"
        And I press "Continue"
        Then I expect that element "#search-businesses" contains the text "Search for a Partnership by Coordinator"
        When I click on the radio "#by-coordinator"
        And I press "Continue"
        Then I expect that element "#search-results" does exist
        When I click on the radio "#first-search-result-radio"
        And I press "Continue"
        Then I expect that element "#header" contains the text "Primary Authority Partnerships"
        When I click on the link "#show-details"
        Then I expect that element "#about-the-business" does exist

    Scenario: Search By Primary Authority
        Given I open the url "/search-partnerships"
        And I click on the radio "#by-primary-authority"
        And I press "Continue"
        Then I expect that element "#search-businesses" contains the text "Search for a Partnership by Primary Authority"
        When I click on the radio "#by-pa"
        And I press "Continue"
        Then I expect that element "#search-results" does exist
        When I click on the radio "#first-search-result-radio"
        And I press "Continue"
        Then I expect that element "#header" contains the text "Primary Authority Partnerships"
        When I click on the link "#show-details"
        Then I expect that element "#about-the-business" does exist
