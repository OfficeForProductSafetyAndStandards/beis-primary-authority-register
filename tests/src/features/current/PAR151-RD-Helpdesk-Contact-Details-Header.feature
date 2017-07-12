@Pending
Feature: As a PAR user,
I need to be able to see the telephone number for the RD Helpdesk in the Header across the site
So that I can contact the Helpdesk if I require assistance.

    Scenario: Valid Login  Id
        Given I open the url "/"
        Then I expect that element "#logged-in-header" contains the text "Helpdesk telephone number"
