@Pending
Feature: List Partnership Details: Load summary elements Load data into the form

    Background:
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1"

    Scenario: List Partnership Details: Load summary elements Load data into the form
        Given I expect that element "h1" contains the text "You need to review and confirm the following partnerships"


