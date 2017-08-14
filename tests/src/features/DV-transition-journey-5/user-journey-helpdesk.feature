@ci @journey5
Feature: Helpdesk - Dashboard

    Background:
        # TEST DATA RESET
        Given I open the url "/user/login"
        And I add "dadmin" to the inputfield "#edit-name"
        And I add "password" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I open the url "/admin/par-data-test-reset"
        And I open the url "/user/logout"

    Scenario: User Journey 1 - Send invitiation to business
        # LOGIN SCREEN

        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then the element "#block-par-theme-account-menu" contains the text "Log out"

        # PARTNERSHIP TASKS SCREEN/DASHBOARD

        When I click on the link "Dashboard"
#        When I open the url "/dv/rd-dashboard"

        # PARTNERSHIP DETAILS

        Then the element "h1" contains the text "RD Helpdesk Dashboard"
        When I click on the link "List of tasks"
        Then the element ".table-scroll-wrapper" contains the text "Review and confirm your partnership details"
        And I click on the link "Review and confirm your partnership details"
        And I click on the link "edit"
        And I add "test partnership info change" to the inputfield "#edit-about-partnership"
        And I click on the button "#edit-next"
        Then the element "#edit-first-section" contains the text "test partnership info change"

        # CSV CHECK
        When I click on the link "Dashboard"
        And I click on the button "td.views-field.views-field-nothing-1 a"
        Then the element ".table-scroll-wrapper" contains the text "Review and confirm your business details"
        When I open the url "/dv/rd-dashboard"
        And I click on the link "Download as CSV"




