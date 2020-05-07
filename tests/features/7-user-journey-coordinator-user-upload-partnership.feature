Feature: Coordinator User - Upload Members

    @ci @PAR790 @coordinatedpartnership
    Scenario: Coordinator User - Upload Members

        # PARTNERSHIPS DASHBOARD
        Given I am logged in as "par_coordinator@example.com"
        And I click the link with text "See your partnerships"
        When I click the link text "Organisation For Coordinated Partnership"
        Then the element "h1" is not empty

        # UPLOAD MEMBERS

        When I click the link text "upload a member list (csv)"
        Then the element "h1.heading-xlarge" contains the text "Upload a list of members"

        # SUPPORT LINKS

        And the element "#edit-info--description em a" contains the text "Member Guidance Page"
        And the element "#download-members-link" does exist

        # VALIDATION

        When I upload the file "files/csv_test_errors.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then the element "h1.heading-xlarge" contains the text "CSV validation errors"
        And the element "#edit-info--description em a" contains the text "Member Guidance Page"
        Then the element "#edit-error-list thead" contains the text "Line"
        And the element "#edit-error-list thead" contains the text "Column"
        And the element "#edit-error-list thead" contains the text "Error"

        And the element "#edit-error-list .error-line-3.error-column-organisation-name" contains the text "The value could not be found."
        And the element "#edit-error-list .error-line-3.error-column-membership-start-date" contains the text "The value could not be found."
        And the element "#edit-error-list .error-line-3.error-column-address-line-1" contains the text "The value could not be found."
        And the element "#edit-error-list .error-line-3.error-column-nation" contains the text "The value you entered is not a valid selection, please see the Member Guidance Page for a full list of available country codes."
        And the element "#edit-error-list .error-line-3.error-column-legal-entity-type-first" contains the text "The value you entered is not a valid selection, please see the Member Guidance Page for a full list of legal entity types."
        And the element "#edit-error-list .error-line-4.error-column-membership-start-date" contains the text "The date should be in the past."
        And the element "#edit-error-list .error-line-4.error-column-membership-end-date" contains the text "The membership end date should be after the start date."
        When I click on the button "#edit-done"

        # CSV PROCESSING

        Then the element "h1.heading-xlarge" contains the text "Upload a list of members"
        When I upload the file "files/csv_test_valid.csv" to field "#edit-csv-upload"
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
        And I click the link text "Manage partnerships"
        And I add "Organisation For Coordinated Partnership" to the inputfield "#edit-keywords"
        And I select the option with the value "confirmed_business" for element "#edit-partnership-status"
        And I click on the button "#edit-submit-helpdesk-dashboard"
        Then I click the link text "Approve partnership"
        And the element "#par-rd-help-desk-confirm" contains the text "Organisation For Coordinated Partnership"
        When I click on the radio "#edit-confirm-authorisation-select"
        And I click on the button "#edit-next"
        And I click on the radio "#edit-partnership-cover-default"
        And I click on the button "#edit-next"
        Then the element "h1.heading-xlarge" contains the text "Partnership is approved"
        And the element "#edit-partnership-info" contains the text "Organisation For Coordinated Partnership"
        And I click on the button "#edit-done"

        And I open the path "/user/logout"

        # CHECK MEMBERS (COORDINATOR)

        Given I am logged in as "par_coordinator@example.com"

        And I click the link with text "See your partnerships"
        And I click the link text "Organisation For Coordinated Partnership"
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
        When I upload the file "files/csv_test_second.csv" to field "#edit-csv-upload"
        And I click on the button "#edit-upload"
        Then the element "h1.heading-xlarge" contains the text "Confirm member upload"
        When I click on the button "#edit-save"
        Then the element "h1.heading-xlarge" contains the text "Member list uploaded"
        When I click on the button "#edit-done"

        # RE-CHECK MEMBERS (COORDINATOR)

        When I click the link text "Dashboard"
        And I click the link with text "See your partnerships"
        And I click the link text "Organisation For Coordinated Partnership"
        Then the element "h1" is not empty
        When I click the link text "Show members list"
        Then the element "h1.heading-xlarge" contains the text "Members list"

        When I add "Coordinated Member 2" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-submit-members-list"
        Then the element ".table-scroll-wrapper" contains the text "Coordinated Member 2"
        And the element ".table-scroll-wrapper a*=Coordinated Member 2" does not exist
        And the element "Cease membership" does not exist

        When I add "Coordinated Member 3" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-submit-members-list"
        Then the element ".table-scroll-wrapper" contains the text "Coordinated Member 3"
        And the element ".table-scroll-wrapper a*=Coordinated Member 3" does not exist
        And the element "Cease membership" does not exist

        When I add "Coordinated Member 4" to the inputfield "#edit-organisation-name"
        And I click on the button "#edit-submit-members-list"
        Then the element ".table-scroll-wrapper" contains the text "Coordinated Member 4"
        And the element ".table-scroll-wrapper a*=Coordinated Member 4" does not exist
        And the element "Cease membership" does not exist

        @coordinatedpartnership @ci
    Scenario: Test search criteria
        # CHECK SEARCH PICKS UP ON TRADERS AND MEMBERS
        Given I am logged in as "par_authority@example.com"
        And I click the link text "Search for a partnership"
        When I add "New LLP Company" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        Then the element ".table-scroll-wrapper" contains the text "Organisation For Coordinated Partnership"
        When I add "Coordinated Member 1" to the inputfield "#edit-keywords"
        And I click on the button "#edit-submit-partnership-search"
        Then the element ".table-scroll-wrapper" contains the text "Organisation For Coordinated Partnership"

        @coordinatedpartnership @ci
    Scenario: Test member upload numbers
        Given I am logged in as "par_coordinator@example.com"
        And I click the link with text "See your partnerships"
        When I click the link text "Member Upload Test Business"
        And the element "#edit-members-link" contains the text "13"
        When I click the link text "Show members list"
        And the element ".pagerer-container" contains the text "Showing 1-10 of 15 results"
        And I select the option with the value "1" for element "#edit-revoked"
        And I click on the button "#edit-submit-members-list"
        And the element ".pagerer-container" contains the text "Showing 1-10 of 13 results"

