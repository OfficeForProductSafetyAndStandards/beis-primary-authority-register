@ci @PAR790
Feature: Coordinator User - Upload Members

    Scenario: Coordinator User - Upload Members

        # PARTNERSHIPS DASHBOARD
        Given I open the url "/user/login"
        And I add "par_coordinator@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        Then I expect that element "#block-par-theme-content" contains the text "See your partnerships"
        And I click on the link "See your partnerships"
        And I click on the link "Organisation For Coordinated Partnership 20"
        Then I expect that element "h1" is not empty

        # UPLOAD MEMBERS

        When I click on the link "Show members list"
        Then I expect that element "h1.heading-xlarge" contains the text "Members list"
        # And I run tota11y against the current page
        When I click on the link "Upload a Member List (CSV)"
        Then I expect that element "h1.heading-xlarge" contains the text "Upload a list of members"

        # SUPPORT LINKS

        And I expect that element "a=Member Guidance Page" does exist
        And I expect that element "a=Download link" does exist

        # VALIDATION

        When I upload the file "./files/csv_test_errors.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then I expect that element "h1.heading-xlarge" contains the text "CSV validation errors"
        And I expect that element "a=Member Guidance Page" does exist
        Then I expect that element "#edit-error-list thead" contains the text "Line"
        And I expect that element "#edit-error-list thead" contains the text "Column"
        And I expect that element "#edit-error-list thead" contains the text "Error"
        And I expect that element "#edit-error-list tbody" contains the text "Organisation name"
        And I expect that element "#edit-error-list tbody" contains the text "Membership Start Date"
        And I expect that element "#edit-error-list tbody" contains the text "Address Line 1"
        And I expect that element "#edit-error-list tbody" contains the text "Nation"
        When I click on the button "#edit-done"

        # CSV PROCESSING

        Then I expect that element "h1.heading-xlarge" contains the text "Upload a list of members"
        When I upload the file "./files/csv_test_valid.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then I expect that element "h1.heading-xlarge" contains the text "Confirm member upload"
        When I click on the button "#edit-save"


        And I open the url "/user/logout"

        # APPROVE COORDINATED PARTNERSHIP (HELPDESK)

        Given I open the url "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I add "Organisation For Coordinated Partnership 20" to the inputfield "#edit-keywords"
        And I select the option with the text "Confirmed by the Organisation" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click on the link "Approve partnership"
        And I expect that element "#par-rd-help-desk-confirm" contains the text "Organisation For Coordinated Partnership 20"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-1"
        And I click on the button "#edit-next"
        Then I expect that element "h1.heading-xlarge" contains the text "Partnership is approved"
        And I expect that element "#edit-partnership-info" contains the text "Organisation For Coordinated Partnership 20"
        And I click on the button "#edit-done"

        And I open the url "/user/logout"

        # ENFORCE MEMBER (ENFORCEMENT OFFICER)

        Given I open the url "/user/login"
        When I add "par_enforcement_officer@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I click on the link "Search for a partnership"
        And I add "Organisation For Coordinated Partnership 20" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        And I click on the button "td.views-field.views-field-authority-name a"
        And I click on the link "Send a notification of a proposed enforcement action"
        Then I expect that element "h1.heading-xlarge" contains the text "Notification of Enforcement action"
        And I expect that element "#par-enforce-organisation" contains the text "Choose the member to enforce"
        When I click on the radio "label*=Coordinated Member 4"
        And I click on the button "#edit-next"
        And I click on the button "#edit-next"
        And I scroll to element "#edit-legal-entities-select-add-new"
        Then I expect that element "h1.heading-xlarge" contains the text "Notification of Enforcement action"
        When I click on the radio "#edit-legal-entities-select-add-new"
        And I add "New Legal Entity 4" to the inputfield "#edit-alternative-legal-entity"
        And I click on the button "#edit-next"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Proposed enforcement notification regarding"
        And I expect that element "h1.heading-xlarge" contains the text "Legal Entity 4"
        When I add "601" random chars of text to field "#edit-summary"
        And I click on the button "#edit-next"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Provide details of the proposed enforcement action"
        And I expect that element "h1.heading-xlarge" contains the text "Add an action to the enforcement notice"
        When I add "Title of Member Enforcement Action" to the inputfield "#edit-title"
        And I click on the radio "#edit-field-regulatory-function-1"
        And I add "601" random chars of text to field "#edit-details"
        And I click on the button "#edit-next"
        Then I expect that element "h1.heading-xlarge .heading-secondary" contains the text "Summary of the proposed enforcement action(s) regarding"
        And I expect that element "h1.heading-xlarge" contains the text "Legal Entity 4"
        When I click on the button "#edit-save"
        Then I expect that element "h1.heading-xlarge" contains the text "Primary Authority Register"

        Given I open the url "/user/logout"

        # CHECK MEMBERS (COORDINATOR)

        Given I open the url "/user/login"
        When I add "par_coordinator@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"

        And I click on the link "See your partnerships"
        And I click on the link "Organisation For Coordinated Partnership 20"
        Then I expect that element "h1" is not empty
        When I click on the link "Show members list"
        Then I expect that element "h1.heading-xlarge" contains the text "Members list"

        And I expect that element ".table-scroll-wrapper" contains the text "Coordinated Member 1"
        And I expect that element ".table-scroll-wrapper" contains the text "Coordinated Member 2"
        And I expect that element ".table-scroll-wrapper" contains the text "Coordinated Member 3"
        And I expect that element ".table-scroll-wrapper" contains the text "Coordinated Member 4"

        # REUPLOAD MEMBERS

        When I click on the link "Upload a Member List (CSV)"
        Then I expect that element "h1.heading-xlarge" contains the text "Upload a list of members"
        When I upload the file "./files/csv_test_second.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then I expect that element "h1.heading-xlarge" contains the text "Confirm member upload"
        When I click on the button "#edit-save"

        # RE-CHECK MEMBERS (COORDINATOR)

        When I click on the link "Dashboard"
        And I click on the link "See your partnerships"
        And I click on the link "Organisation For Coordinated Partnership 20"
        Then I expect that element "h1" is not empty
        When I click on the link "Show members list"
        Then I expect that element "h1.heading-xlarge" contains the text "Members list"

        And I expect that element ".table-scroll-wrapper" not contains the text "Coordinated Member 1"
        And I expect that element ".table-scroll-wrapper" not contains the text "Coordinated Member 2"
        And I expect that element ".table-scroll-wrapper" not contains the text "Coordinated Member 3"
        And I expect that element ".table-scroll-wrapper" contains the text "Coordinated Member 4"

        And I expect that element ".table-scroll-wrapper" contains the text "New Member 1"
        And I expect that element ".table-scroll-wrapper" contains the text "New Member 2"
        And I expect that element ".table-scroll-wrapper" contains the text "New Member 3"
        And I expect that element ".table-scroll-wrapper" contains the text "New Member 5"
