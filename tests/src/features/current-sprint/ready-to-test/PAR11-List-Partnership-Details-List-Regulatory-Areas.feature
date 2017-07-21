@Pending
Feature: As a Primary Authority Officer, I need to be able to see a list of my existing partnership details including| About the Partnership, Main Primary Authority Contact, Secondary Primary Authority Contact, Business Contact Name, Business Contact email, So that I can review my partnership details|

    Background:
        Given I open the url "/dv/primary-authority-partnerships/1/partnership/1"

    Scenario: List Partnership Details: List Regulatory Areas
        Given the element "h1" contains the text "You need to review and confirm the following partnerships"
