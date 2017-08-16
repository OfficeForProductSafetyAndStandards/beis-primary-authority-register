@ci @journey5
Feature: Helpdesk As Primary Authority - Documentation

    Background:
        # TEST DATA RESET
        Given I reset the test data

    Scenario: Helpdesk As Primary Authority - Documentation
        # LOGIN SCREEN

        Given I am logged in as "par_helpdesk@example.com"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        When I click on the link "Dashboard"
        Then I expect that element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the button "td.views-field.views-field-authority-name a"
        Then I expect that element ".table-scroll-wrapper" contains the text "Review and confirm your partnership details"
        When I click on the link "Review and confirm your partnership details"
        And I click on the checkbox "#edit-confirmation"
        And I click on the button "#edit-next"
        And I click on the link "Dashboard"

        # PARTNERSHIP DETAILS

        Then I expect that element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the button "td.views-field.views-field-authority-name a"

        # DOCUMENTATION

        And I scroll to element ".table-scroll-wrapper"
        When I click on the link "Review and confirm your documentation for ABCD Mart"
        And I scroll to element ".table-scroll-wrapper"
        And I click on the link "edit"
        And I click on the radio "#edit-document-type-authority-advice"
        When I click on the button "#edit-next"
        Then I expect that element ".error-message" does exist
        And I click on the checkbox ".form-label*=Cookie control"
        When I click on the button "#edit-next"
        Then I expect that element ".table-scroll-wrapper" contains the text "✔"
        And the element ".table-scroll-wrapper" contains the text "Cookie control"
        When I click on the link "Save"
        Then I expect that element ".table-scroll-wrapper" contains the text "100%"
        When I click on the link "Review and confirm your documentation for ABCD Mart"
        And I click on the link "edit"
        And I click on the radio "#edit-document-type-authority-advice"
        And I click on the checkbox ".form-label*=Alphabet learning"
        When I click on the button "#edit-next"
        Then I expect that element ".table-scroll-wrapper" contains the text "✔"
        And I expect that element ".table-scroll-wrapper" contains the text "Cookie control, Alphabet learning"
        When I click on the link "Save"
        Then I expect that element ".table-scroll-wrapper" contains the text "100%"
        And I click on the link "Log out"
