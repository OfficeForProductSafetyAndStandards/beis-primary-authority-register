@PAR790
Feature: Coordinator User - Upload Members

    Scenario: Coordinator User - Upload Members

        # PARTNERSHIPS DASHBOARD
        Given I am logged in as "par_coordinator@example.com"
        And I click the link text "See your partnerships"
        When I click the link text "Organisation for Coordinated Partnership"
        Then the element "h1" is not empty

        # UPLOAD MEMBERS

        When I click the link text "Upload a Member List (CSV)"
        Then the element "h1.heading-xlarge" contains the text "Upload a list of members"

        # SUPPORT LINKS

        And the element "a=Member Guidance Page" does exist
        And the element "a=Download link" does exist

        # VALIDATION

        When I upload the file "./files/csv_test_errors.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then the element "h1.heading-xlarge" contains the text "CSV validation errors"
        And the element "a=Member Guidance Page" does exist
        Then the element "#edit-error-list thead" contains the text "Line"
        And the element "#edit-error-list thead" contains the text "Column"
        And the element "#edit-error-list thead" contains the text "Error"
        And the element "#edit-error-list tbody" contains the text "Organisation name"
        And the element "#edit-error-list tbody" contains the text "Membership Start Date"
        And the element "#edit-error-list tbody" contains the text "Address Line 1"
        And the element "#edit-error-list tbody" contains the text "Nation"
        When I click on the button "#edit-done"

        # CSV PROCESSING

        Then the element "h1.heading-xlarge" contains the text "Upload a list of members"
        When I upload the file "./files/csv_test_valid.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then the element "h1.heading-xlarge" contains the text "Confirm member upload"
        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Member list uploaded"
        When I click on the button "#edit-done"

        And I open the path "/user/logout"

        # APPROVE COORDINATED PARTNERSHIP (HELPDESK)

        Given I open the path "/user/login"
        And I add "par_helpdesk@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        When I click on the button "#edit-submit"
        When I add "Organisation for Coordinated Partnership" to the inputfield "#edit-keywords"
        And I select the option with the value "Confirmed by the Organisation" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click the link text "Approve partnership"
        And the element "#par-rd-help-desk-confirm" contains the text "Organisation for Coordinated Partnership"
        And I click on the radio "#edit-confirm-authorisation-select"
        And I click on the radio "#edit-partnership-regulatory-functions-1"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
        And the element "#edit-partnership-info" contains the text "Organisation for Coordinated Partnership"
        And I click on the button "#edit-done"

        And I open the path "/user/logout"

        # ENFORCE MEMBER (ENFORCEMENT OFFICER)

        Given I open the path "/user/login"
        When I add "par_enforcement_officer@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"
        And I click the link text "Search for a partnership"
        And I add "Organisation for Coordinated Partnership" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        And I click on the button "td.views-field.views-field-authority-name a"
        And I click the link text "Send a notification of a proposed enforcement action"
        Then the element "h1.heading-xlarge" contains the text "Notification of Enforcement action"
        And the element "#par-enforce-organisation" contains the text "Choose the member to enforce"
        When I click on the radio "label*=Coordinated Member 4"
        And I click on the button "#edit-next"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Notification of Enforcement action"
        When I click on the radio "#edit-legal-entities-select-add-new"
        And I add "New Legal Entity 4" to the inputfield "#edit-alternative-legal-entity"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Proposed enforcement notification regarding"
        And the element "h1.heading-xlarge" contains the text "Legal Entity 4"
        When I add "601" random chars of text to field "#edit-summary"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Provide details of the proposed enforcement action"
        And the element "h1.heading-xlarge" contains the text "Add an action to the enforcement notice"
        When I add "Title of Member Enforcement Action" to the inputfield "#edit-title"
        And I click on the radio "#edit-field-regulatory-function-1"
        And I add "601" random chars of text to field "#edit-details"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge .heading-secondary" contains the text "Summary of the proposed enforcement action(s) regarding"
        And the element "h1.heading-xlarge" contains the text "Legal Entity 4"
        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Primary Authority Register"
        Given I open the path "/user/logout"

        # CHECK MEMBERS (COORDINATOR)

        Given I open the path "/user/login"
        When I add "par_coordinator@example.com" to the inputfield "#edit-name"
        And I add "TestPassword" to the inputfield "#edit-pass"
        And I click on the button "#edit-submit"

        And I click the link text "See your partnerships"
        And I click the link text "Organisation for Coordinated Partnership"
        Then the element "h1" is not empty
        When I click the link text "Show members list"
        Then the element "h1.heading-xlarge" contains the text "Members list"

        And the element ".table-scroll-wrapper" contains the text "Coordinated Member 1"
        And the element ".table-scroll-wrapper" contains the text "Coordinated Member 2"
        And the element ".table-scroll-wrapper" contains the text "Coordinated Member 3"
        And the element ".table-scroll-wrapper" contains the text "Coordinated Member 4"

        # REUPLOAD MEMBERS

        When I click the link text "Upload a Member List (CSV)"
        Then the element "h1.heading-xlarge" contains the text "Upload a list of members"
        When I upload the file "./files/csv_test_second.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then the element "h1.heading-xlarge" contains the text "Confirm member upload"
        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Member list uploaded"
        When I click on the button "#edit-done"

        # RE-CHECK MEMBERS (COORDINATOR)

        When I click the link text "Dashboard"
        And I click the link text "See your partnerships"
        And I click the link text "Organisation for Coordinated Partnership"
        Then the element "h1" is not empty
        When I click the link text "Show members list"
        Then the element "h1.heading-xlarge" contains the text "Members list"

        And the element ".table-scroll-wrapper" does not contain the text "Coordinated Member 1"
        And the element ".table-scroll-wrapper" does not contain the text "Coordinated Member 2"
        And the element ".table-scroll-wrapper" does not contain the text "Coordinated Member 3"

        And the element ".table-scroll-wrapper" does not contain the text "New Member 1"
        And the element ".table-scroll-wrapper" does not contain the text "New Member 2"
        And the element ".table-scroll-wrapper" does not contain the text "New Member 3"
        And the element ".table-scroll-wrapper" does not contain the text "New Member 5"
        And the element ".table-scroll-wrapper" does not contain the text "Coordinated Member 4"